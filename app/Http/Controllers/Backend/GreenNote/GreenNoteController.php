<?php

namespace App\Http\Controllers\backend\GreenNote;

use App\Exports\NoteExport;
use App\Http\Controllers\Controller;
use App\Mail\NoteStatusChangeMail;
use App\Mail\TestMail;
use App\Models\ApprovalFlow;
use App\Models\ApprovalLog;
use App\Models\ApprovalStep;
use App\Models\Department;
use App\Models\Designation;
use App\Models\GreenNote;
use App\Models\PaymentNote;
use App\Models\SupportingDoc;
use App\Models\User;
use App\Models\Vendor;
use Barryvdh\DomPDF\Facade\Pdf as DomPDF;
use PDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class GreenNoteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $authId = Auth::id();
        $status = request()->status;
        $query = GreenNote::query();

        $userRoles = auth()->user()->getRoleNames();


        if ($authId === 1 || ($status === 'all' && (auth()->user()->can('all-note') || $userRoles->contains('PN User')))) {
            $query->orderBy('created_at', 'desc');
        } else {
            $query->where(function ($q) use ($authId) {
                $q->whereIn('id', function ($sub) use ($authId) {
                    $sub->select('green_note_id')
                        ->from('approval_logs as al')
                        ->join('approval_steps as step', 'al.approval_step_id', '=', 'step.id')
                        ->where('al.status', 'A')
                        ->where('step.next_on_approve', $authId)
                        ->whereRaw('al.id = ( SELECT MAX(id) FROM approval_logs 
                                    WHERE approval_logs.green_note_id = al.green_note_id)');
                })
                    ->orWhere('user_id', $authId);
            });
        }
        // if ($status === 'all') {
        //     if ($authId === 1 || auth()->user()->can('all-note')) {
        //         // Admin or has all-note permission
        //         $query->orderBy('created_at', 'desc');
        //     } else {
        //         $userRoles = auth()->user()->getRoleNames();

        //         if ($userRoles->contains('PN User')) {
        //             // Role-based full access
        //             $query->orderBy('created_at', 'desc');
        //         } else {
        //             $query->where(function ($q) use ($authId) {
        //                 // Subquery to get note IDs with latest log satisfying the condition
        //                 $q->whereIn('id', function ($sub) use ($authId) {
        //                     $sub->select('green_note_id')
        //                         ->from('approval_logs as al')
        //                         ->join('approval_steps as step', 'al.approval_step_id', '=', 'step.id')
        //                         ->where('al.status', 'A')
        //                         ->where('step.next_on_approve', $authId)
        //                         ->whereRaw('al.id = ( SELECT MAX(id) FROM approval_logs 
        //                             WHERE approval_logs.green_note_id = al.green_note_id)');
        //                 })
        //                     ->orWhere('user_id', $authId);
        //             });
        //         }
        //     }
        // } else {

        //     $query->where(function ($q) use ($authId) {
        //         // Subquery to get note IDs with latest log satisfying the condition
        //         $q->whereIn('id', function ($sub) use ($authId) {
        //             $sub->select('green_note_id')
        //                 ->from('approval_logs as al')
        //                 ->join('approval_steps as step', 'al.approval_step_id', '=', 'step.id')
        //                 ->where('al.status', 'A')
        //                 ->where('step.next_on_approve', $authId)
        //                 ->whereRaw('al.id = (
        //         SELECT MAX(id) FROM approval_logs 
        //         WHERE approval_logs.green_note_id = al.green_note_id
        //     )');
        //         })
        //             ->orWhere('user_id', $authId);
        //     });
        // }

        if (empty($status)) {
            $status = 'S';
        }

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        // Server-side DataTables
        if (request()->ajax()) {
            $query = $query->select('id', 'user_id', 'status', 'created_at', 'vendor_id', 'supplier_id', 'invoice_value')
                ->with(['vendor', 'supplier', 'approvalLogs.approvalStep.nextOnApprove', 'paymentNotes.paymentApprovalLogs']);

            return DataTables::eloquent($query)
                ->addIndexColumn()
                ->addColumn('project_name', function ($row) {
                    return optional($row->vendor)->project ?: '-';
                })
                ->addColumn('vendor_name', function ($row) {
                    return optional($row->supplier)->vendor_name ?: '-';
                })
                ->addColumn('invoice_value', function ($row) {
                    // Try net value via latest payment note if present, else fallback if model has invoice_value attribute
                    $val = $row->invoice_value ?? null;
                    return $val !== null ? \App\Helpers\Helper::formatIndianNumber($val) : '-';
                })
                ->addColumn('date', function ($row) {
                    return optional($row->created_at)->setTimezone('Asia/Kolkata')->format('d/m/Y h:i A') ?: '-';
                })
                ->addColumn('status_badge', function ($row) {
                    $statusLabels = [
                        'D' => '<span class="badge bg-dark">Draft</span>',
                        'PMPL' => '<span class="badge bg-info">Sent for PMC</span>',
                        'S' => '<span class="badge bg-secondary">Sent for Approval</span>',
                        'P' => '<span class="badge bg-warning">Pending</span>',
                        'A' => '<span class="badge bg-success">Approved</span>',
                        'R' => '<span class="badge bg-danger">Rejected</span>',
                        'B' => '<span class="badge bg-black">RTGS/NEFT Created</span>',
                        'PNA' => '<span class="badge bg-info">Payment Note Approved </span>',
                        'PA' => '<span class="badge bg-black">Payment Approved </span>',
                        'PD' => '<span class="badge bg-info">Paid</span>',
                    ];
                    return $statusLabels[$row->status] ?? '-';
                })
                ->addColumn('action', function ($row) use ($authId) {
                    $user = auth()->user();
                    $actions = '';
                    if ($user && $user->can('edit-note')) {
                        $canEdit = ($user->hasRole('Admin')) || (($row->status === 'D' || $row->status === 'PMPL') && $row->user_id == $authId);
                        if ($canEdit) {
                            $actions .= '<a href="' . route('backend.note.edit', $row->id) . '"><i class="bi bi-pencil-square"></i></a> | ';
                        }
                    }
                    $actions .= '<a href="' . route('backend.note.show', $row->id) . '"><i class="bi bi-eye"></i></a>';
                    return $actions;
                })
                ->rawColumns(['status_badge', 'action'])
                ->toJson();
        }

        $notes = $query->get();

        return view('backend.greenNote.index', compact('notes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $orderNumber = $this->generateOrderNumber('OP');
        $filteredItems = Vendor::selectRaw('id,project, COUNT(*) as total_records')->whereNotNull('project')->where('project', '!=', '')->groupBy('project')->orderBy('project', 'asc')->where('active', 'Y')->get();
        $typeAccount = config('app.short_name') === 'NSPPL' ? 'Internal' : 'External';
        // $filteredVendorItems = Vendor::where('from_account_type', $type)->where('status', 'active')->where('active', 'Y')->get();
        $type = config('app.short_name');

        if ($type == 'NSPPL') {
            $filteredVendorItems = Vendor::where('status', 'active')->where('active', 'Y')->get();
        } else {
            $filteredVendorItems = Vendor::where('from_account_type', $typeAccount)->where('status', 'active')->where('active', 'Y')->get();
        }
        $departments = Department::all();

        return view('backend.greenNote.create', compact('orderNumber', 'filteredItems', 'departments', 'filteredVendorItems'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validation rules
        $validated = $request->validate(
            [
                'user_id' => 'required|exists:users,id',
                'vendor_id' => 'required',
                'department_id' => 'required|exists:departments,id',
                'order_date' => 'required|date',
                'order_no' => 'required|string|max:255',
                'base_value' => 'required|numeric',
                'gst' => 'required|numeric',
                'other_charges' => 'nullable|numeric',
                'total_amount' => 'nullable|numeric',
                'supplier_id' => 'required|string|max:255',
                'msme_classification' => 'required|string|max:255',
                'activity_type' => 'required|string|max:255',
                'protest_note_raised' => 'required|in:Y,N',
                'brief_of_goods_services' => 'required|string',
                'invoice_number' => 'required|string|max:255',
                'invoice_date' => 'required|date',
                'invoice_base_value' => 'required|numeric|min:0',
                'invoice_gst' => 'required|numeric|min:0',
                'invoice_value' => 'required|numeric|min:0',
                'invoice_other_charges' => 'required|numeric|min:0',
                'delayed_damages' => 'required|string|max:255',
                'contract_start_date' => 'required|date',
                'contract_end_date' => 'required|date|after_or_equal:contract_start_date',
                'appointed_start_date' => 'required|date',
                'supply_period_start' => 'required|date',
                'supply_period_end' => 'required|date|after_or_equal:supply_period_start',
                'whether_contract' => 'required|string|max:255',
                'extension_contract_period' => 'required|in:Y,N',
                'approval_for' => 'required|string|max:255',
                'budget_expenditure' => 'required|string|max:255',
                'actual_expenditure' => 'required|string|max:255',
                'expenditure_over_budget' => 'required|string|max:255',
                'nature_of_expenses' => 'required|string|max:255',
                'documents_workdone_supply' => 'nullable|string|max:255',
                'documents_discrepancy' => 'nullable|string|max:255',
                'amount_submission_non' => 'nullable|string|max:255',
                'remarks' => 'nullable|string|max:1000',
                'auditor_remarks' => 'nullable|string|max:1000',
                'required_submitted' => 'nullable|in:Y,N',
                'expense_amount_within_contract' => 'required|in:Y,N',
                'milestone_status' => 'required|in:Y,N',
                'milestone_remarks' => 'nullable|string',
                'specify_deviation' => 'nullable|string',
                'deviations' => 'required|in:Y,N',
                'status' => 'required|in:D,PMPL,P,A,R,S',
                'file_input_1' => 'nullable|mimes:pdf,doc,docx,xls,xlsx,csv|max:10240000|required_if:extension_contract_period,Y',
                'file_input_2' => 'nullable|mimes:pdf,doc,docx,xls,xlsx,csv|max:10240000|required_if:protest_note_raised,Y',
                'file_input_4' => 'nullable|mimes:pdf,doc,docx,xls,xlsx,csv|max:10240000',
                'file_input_6' => 'nullable|mimes:pdf,doc,docx,xls,xlsx,csv|max:10240000|required_if:deviations,Y',
            ],
            [
                'file_input_1.required_if' => 'Supporting document is required when extension contract period is Yes.',
                'file_input_2.required_if' => 'Supporting document is required when protest note is raised.',
                'file_input_6.required_if' => 'Supporting document is required when Deviations is raised.',
            ],
        );
        $exists = GreenNote::where('supplier_id', $request->supplier_id)->where('invoice_number', $request->invoice_number)->where('status', '!=', 'R')->exists();

        if ($exists) {
            return back()
                ->withErrors(['invoice_number' => 'This invoice number already exists for the selected vendor.'])
                ->withInput();
        }

        if ($request->invoice_value > $request->total_amount) {
            return back()
                ->withErrors(['invoice_value' => 'Invoice value cannot be greater than the total amount.'])
                ->withInput();
        }

        // Handle multiple invoices if enabled
        if ($request->has('enable_multiple_invoices') && $request->enable_multiple_invoices) {
            $invoices = [];
            if ($request->has('invoices') && is_array($request->invoices)) {
                foreach ($request->invoices as $invoice) {
                    if (!empty($invoice['invoice_number']) && !empty($invoice['invoice_date'])) {
                        $invoices[] = [
                            'invoice_number' => $invoice['invoice_number'],
                            'invoice_date' => $invoice['invoice_date'],
                            'invoice_base_value' => (float) ($invoice['invoice_base_value'] ?? 0),
                            'invoice_gst' => (float) ($invoice['invoice_gst'] ?? 0),
                            'invoice_other_charges' => (float) ($invoice['invoice_other_charges'] ?? 0),
                            'invoice_value' => (float) ($invoice['invoice_value'] ?? 0),
                        ];
                    }
                }
            }

            if (empty($invoices)) {
                return back()
                    ->withErrors(['invoices' => 'At least one invoice entry is required when multiple invoices are enabled.'])
                    ->withInput();
            }

            $validated['invoices'] = $invoices;

            // Calculate totals from multiple invoices
            $totalInvoiceValue = array_sum(array_column($invoices, 'invoice_value'));
            $totalBaseValue = array_sum(array_column($invoices, 'invoice_base_value'));
            $totalGST = array_sum(array_column($invoices, 'invoice_gst'));
            $totalOtherCharges = array_sum(array_column($invoices, 'invoice_other_charges'));

            $validated['invoice_value'] = $totalInvoiceValue;
            $validated['invoice_base_value'] = $totalBaseValue;
            $validated['invoice_gst'] = $totalGST;
            $validated['invoice_other_charges'] = $totalOtherCharges;
        }

        // Saving the file (if exists)
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filename = time() . '.' . $file->getClientOriginalExtension();
            $file->storeAs('public/notes', $filename);
            $validated['file'] = $filename;
        }

        // Creating the note entry
        $greenNote = GreenNote::create($validated);

        // File inputs aur unke names ka array
        $fileInputs = [
            'file_input_1' => 'Extension of contract period executed',
            'file_input_2' => 'Protest Note Raised',
            'file_input_4' => 'Auditor File',
            'file_input_6' => 'Deviations',
        ];

        // Har file ko check karke save karo
        foreach ($fileInputs as $fileKey => $name) {
            if ($request->hasFile($fileKey) && $request->file($fileKey)->isValid()) {
                $fileName = time() . '_' . $request->file($fileKey)->getClientOriginalName();
                $request->file($fileKey)->move(public_path('notes/documents'), $fileName);

                SupportingDoc::create([
                    'green_note_id' => $greenNote->id,
                    'user_id' => Auth::id(),
                    'name' => $name,
                    'file_path' => $fileName,
                ]);
            }
        }
        if ($request->status == 'D') {
            return redirect()->route('backend.note.index')->with('success', 'Draft Saved!');
        } else {
            return redirect()->route('backend.note.show', $greenNote->id)->with('success', 'Note Submitted!');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $note = GreenNote::findOrFail($id);
        $documents = SupportingDoc::where('green_note_id', $id)->get();
        $departments = Department::all();

        return view('backend.greenNote.show', compact('note', 'documents', 'departments'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $note = GreenNote::findOrFail($id);
        $documents = SupportingDoc::where('green_note_id', $id)->get();
        $departments = Department::all();
        // $type = config('app.short_name') === 'NSPPL' ? 'Internal' : 'External';
        $typeAccount = config('app.short_name') === 'NSPPL' ? 'Internal' : 'External';

        // $filteredVendorItems = Vendor::where('from_account_type', $type)->where('status', 'active')->where('active', 'Y')->get();
        $type = config('app.short_name');

        if ($type == 'NSPPL') {
            $filteredVendorItems = Vendor::where('status', 'active')->where('active', 'Y')->get();
        } else {
            $filteredVendorItems = Vendor::where('from_account_type', $typeAccount)->where('status', 'active')->where('active', 'Y')->get();
        }
        $filteredItems = Vendor::selectRaw('id,project, COUNT(*) as total_records')->whereNotNull('project')->where('project', '!=', '')->groupBy('project')->orderBy('project', 'asc')->where('active', 'Y')->get();

        return view('backend.greenNote.edit', compact('note', 'documents', 'departments', 'filteredVendorItems', 'filteredItems'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $note = GreenNote::findOrFail($id);
        // dd($request->all());
        if ($note->user_id !== auth()->id()) {
            $userAlreadySubmitted = ApprovalLog::where('green_note_id', $id)
                ->where('reviewer_id', auth()->id())
                ->exists();

            if ($userAlreadySubmitted) {
                return redirect()->back()->with('error', 'You have already submitted your approval.');
            }
        }

        // Validation rules
        $validated = $request->validate([
            'vendor_id' => 'required',
            'department_id' => 'required|exists:departments,id',
            'order_date' => 'required|date',
            'order_no' => 'required|string|max:255',
            'base_value' => 'required|numeric',
            'gst' => 'required|numeric',
            'other_charges' => 'nullable|numeric',
            'total_amount' => 'nullable|numeric',
            'supplier_id' => 'required|string|max:255',
            'msme_classification' => 'required|string|max:255',
            'activity_type' => 'required|string|max:255',
            'protest_note_raised' => 'required|in:Y,N',
            'brief_of_goods_services' => 'required|string',
            'invoice_number' => 'required|string|max:255',
            'invoice_date' => 'required|date',
            'invoice_base_value' => 'required|numeric|min:0',
            'invoice_gst' => 'required|numeric|min:0',
            'invoice_value' => 'required|numeric|min:0',
            'invoice_other_charges' => 'nullable|numeric|min:0',
            'delayed_damages' => 'required|string|max:255',
            'contract_start_date' => 'required|date',
            'contract_end_date' => 'required|date|after_or_equal:contract_start_date',
            'appointed_start_date' => 'required|date',
            'supply_period_start' => 'required|date',
            'supply_period_end' => 'required|date|after_or_equal:supply_period_start',
            'whether_contract' => 'required|string|max:255',
            'extension_contract_period' => 'required|in:Y,N',
            'approval_for' => 'required|string|max:255',
            'budget_expenditure' => 'required|string|max:255',
            'actual_expenditure' => 'required|string|max:255',
            'expenditure_over_budget' => 'required|string|max:255',
            'nature_of_expenses' => 'required|string|max:255',
            'documents_workdone_supply' => 'nullable|string|max:255',
            'documents_discrepancy' => 'nullable|string|max:255',
            'amount_submission_non' => 'nullable|string|max:255',
            'remarks' => 'nullable|string|max:1000',
            'auditor_remarks' => 'nullable|string|max:1000',
            'required_submitted' => 'nullable|in:Y,N',
            'expense_amount_within_contract' => 'required|in:Y,N',
            'milestone_status' => 'required|in:Y,N',
            'milestone_remarks' => 'nullable|string',
            'specify_deviation' => 'nullable|string',
            'deviations' => 'required|in:Y,N',
            'status' => 'required|in:D,PMPL,P,A,R,S',
            'file' => 'nullable|mimes:pdf,doc,docx,xls,xlsx,csv|max:10240000',
            'file_input_1' => 'nullable|mimes:pdf,doc,docx,xls,xlsx,csv|max:10240000',
            'file_input_2' => 'nullable|mimes:pdf,doc,docx,xls,xlsx,csv|max:10240000',
            'file_input_4' => 'nullable|mimes:pdf,doc,docx,xls,xlsx,csv|max:10240000',
            'file_input_6' => 'nullable|mimes:pdf,doc,docx,xls,xlsx,csv|max:10240000',
        ]);
        if ($request->invoice_value > $request->total_amount) {
            return back()
                ->withErrors(['invoice_value' => 'Invoice value cannot be greater than the total amount.'])
                ->withInput();
        }
        // If file is uploaded, handle the new file
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filename = time() . '.' . $file->getClientOriginalExtension();
            $file->storeAs('public/notes', $filename);
            $validated['file'] = $filename;
        }

        // dd($note->status, $request->status);
        $userRoles = auth()->user()->getRoleNames();
        if (!$userRoles->contains('Admin')) {
            if ($request->status === 'PMPL') {
                $approvalFlow = ApprovalFlow::where('vendor_id', $request->vendor_id)->where('department_id', $request->department_id)->first();
                // dd($approvalFlow, $request->vendor_id, $request->department_id);
                if (!$approvalFlow) {
                    return redirect()->back()->with('success', 'Approval flow not found.');
                }
                $existingLogsCount = ApprovalLog::where('green_note_id', $note->id)->count();
                $nextStep = $existingLogsCount + 1;
                $approvalStep = ApprovalStep::where('approval_flow_id', $approvalFlow->id)->where('step', $nextStep)->first();
                $approvalStepCheck = ApprovalStep::where('approval_flow_id', $approvalFlow->id)->first();

                $existsPMPL = ApprovalLog::where('approval_step_id', $approvalStepCheck->id)
                    ->where('green_note_id', $note->id)
                    ->where('status', 'PMPL')
                    ->where('reviewer_id', auth()->id())
                    ->exists();
                if ($existsPMPL) {
                    return redirect()->route('backend.note.index')->with('error', 'This note has already been processed by you.');
                }
                if (!$existsPMPL) {
                    ApprovalLog::create([
                        'approval_step_id' => $approvalStep->id,
                        'green_note_id' => $note->id,
                        'reviewer_id' => auth()->id(),
                        'status' => $request->status,
                        'comments' => null,
                    ]);
                }
            }
            if ($request->status == 'S') {
                $approvalFlow = ApprovalFlow::where('vendor_id', $request->vendor_id)->where('department_id', $request->department_id)->first();

                if (!$approvalFlow) {
                    return redirect()->back()->with('success', 'Approval flow not found.');
                }
                $existingLogsCount = ApprovalLog::where('green_note_id', $note->id)->whereNot('status', 'PMPL')->count();

                $currentApprovalStep = ApprovalLog::where('green_note_id', $note->id)->whereNot('status', 'PMPL')->latest()->first();

                $nextStep = $existingLogsCount + 1;
                $approvalStep = ApprovalStep::where('approval_flow_id', $approvalFlow->id)->where('step', $nextStep)->first();
                $approvalStepCheckExist01 = ApprovalStep::where('approval_flow_id', $approvalFlow->id)->first();

                if (!$approvalStep) {
                    return redirect()->back()->with('success', 'Approval step 1 not found.');
                }

                $userRoles = auth()->user()->getRoleNames();
                if ($userRoles->contains('Qs') || $userRoles->contains('Hr And Admin')) {
                    $note->update($validated);
                    if ($approvalStep && $note->invoice_value >= $approvalStep->amount) {
                        $existAStatus = ApprovalLog::where('approval_step_id', $approvalStepCheckExist01->id)
                            ->where('green_note_id', $note->id)
                            ->where('status', 'A')
                            ->where('reviewer_id', auth()->id())
                            ->exists();

                        if ($existAStatus) {
                            return redirect()->route('backend.note.index')->with('error', 'This note has already been processed by you.');
                        }
                        if (!$existAStatus) {
                            $data = [
                                'updated_by' => auth()->user()->email,
                                'subject' => 'Expense Approval Note for ' . $note->supplier->vendor_name . ' of Rs ' . $note->invoice_value . ' has been Generated',
                                'approver_name' => $approvalStep->nextOnApprove->name ?? 'Approver',
                                'maker' => $note->user->name . ' has generated a Expense Approval Note No. ' . $note->formatted_order_no . ' for ' . $note->supplier->vendor_name . ' of Rs ' . $note->invoice_value . ' for ' . $note->vendor->project . ' & due for your approval.',
                                'end' => 'Login to the panel for review & Approve/Reject.',
                            ];
                            // Mail Send
                            // Mail::to('goseg75168@doishy.com')->send(new NoteStatusChangeMail($data));
                            Mail::to($approvalStep->nextOnApprove->email)->send(new NoteStatusChangeMail($data));
                            ApprovalLog::create([
                                'approval_step_id' => $approvalStep->id,
                                'green_note_id' => $note->id,
                                'reviewer_id' => auth()->id(),
                                'status' => 'A',
                                'comments' => null,
                            ]);

                            return redirect()->route('backend.note.index')->with('success', 'Approval log created for the next step.');
                        }
                    } else {
                        $existAStatus = ApprovalLog::where('approval_step_id', $currentApprovalStep->approval_step_id)
                            ->where('green_note_id', $note->id)
                            ->where('status', 'A')
                            ->where('reviewer_id', auth()->id())
                            ->exists();
                        if ($existAStatus) {
                            return redirect()->route('backend.note.index')->with('error', 'This note has already been processed by you.');
                        }
                        if (!$existAStatus) {
                            $data = [
                                'updated_by' => auth()->user()->email,
                                'subject' => 'Expense Approval Note for ' . $note->supplier->vendor_name . ' of Rs ' . $note->invoice_value . ' is Approved & due for review / Payment',
                                'approver_name' => 'Maker',
                                'maker' => $note->user->name . ' has generated a Expenses Approval Note No. ' . $note->formatted_order_no . ' for ' . $note->supplier->vendor_name . ' of Rs ' . $note->invoice_value . ' for ' . $note->vendor->project . '& due for your review / Payment.',
                                'end' => 'Login to the panel for review & process. ',
                            ];
                            // Mail Send
                            $users = User::role('PN User')->get();
                            foreach ($users as $key => $value) {
                                Mail::to($value->email)->send(new NoteStatusChangeMail($data));
                            }
                            // Mail::to('goseg75168@doishy.com')->send(new NoteStatusChangeMail($data));
                            Mail::to($approvalStep->nextOnApprove->email)->send(new NoteStatusChangeMail($data));
                            ApprovalLog::create([
                                'approval_step_id' => $currentApprovalStep->approval_step_id,
                                'green_note_id' => $note->id,
                                'reviewer_id' => auth()->id(),
                                'status' => 'A',
                                'comments' => null,
                            ]);

                            $note->status = 'A';
                            $note->save();
                            return redirect()->route('backend.note.index')->with('success', 'Final step reached. No further approvals needed.');
                        }
                    }
                } else {
                    $existAStatus = ApprovalLog::where('approval_step_id', $approvalStepCheckExist01->id)
                        ->where('green_note_id', $note->id)
                        ->where('status', 'A')
                        ->where('reviewer_id', auth()->id())
                        ->exists();
                    if ($existAStatus) {
                        return redirect()->route('backend.note.index')->with('error', 'This note has already been processed by you.');
                    }
                    // dd('1 else go');

                    if (!$existAStatus) {
                        $data = [
                            'updated_by' => auth()->user()->email,
                            'subject' => 'Expense Approval Note for ' . $note->supplier->vendor_name . ' of Rs ' . $note->invoice_value . ' has been Generated',
                            'approver_name' => $approvalStep->nextOnApprove->name ?? 'Approver',
                            'maker' => $note->user->name . ' has generated a Expense Approval Note No. ' . $note->formatted_order_no . ' for ' . $note->supplier->vendor_name . ' of Rs ' . $note->invoice_value . ' for ' . $note->vendor->project . ' & due for your approval.',
                            'end' => 'Login to the panel for review & Approve/Reject.',
                        ];

                        // Mail::to('goseg75168@doishy.com')->send(new NoteStatusChangeMail($data));
                        Mail::to($approvalStep->nextOnApprove->email)->send(new NoteStatusChangeMail($data));
                        ApprovalLog::create([
                            'approval_step_id' => $approvalStep->id,
                            'green_note_id' => $note->id,
                            'reviewer_id' => auth()->id(),
                            'status' => 'A',
                            'comments' => null,
                        ]);
                    }
                }
            }
        }

        $note->update($validated);

        $fileInputs = [
            'file_input_1' => 'Extension of contract period executed',
            'file_input_2' => 'Protest Note Raised',
            'file_input_4' => 'Auditor File',
            'file_input_6' => 'Deviations',
        ];

        foreach ($fileInputs as $fileKey => $name) {
            if ($request->hasFile($fileKey) && $request->file($fileKey)->isValid()) {
                $fileName = time() . '_' . $request->file($fileKey)->getClientOriginalName();
                $request->file($fileKey)->move(public_path('notes/documents'), $fileName);
                SupportingDoc::create([
                    'green_note_id' => $note->id,
                    'user_id' => Auth::id(),
                    'name' => $name,
                    'file_path' => $fileName,
                ]);
            }
        }
        // Update the existing note entry
        return redirect()->route('backend.note.index')->with('success', 'Note updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            PaymentNote::where('green_note_id', $id)->delete();

            SupportingDoc::where('green_note_id', $id)->delete();

            ApprovalLog::where('green_note_id', $id)->delete();

            GreenNote::findOrFail($id)->delete();

            return redirect()->route('backend.note.index')->with('success', 'Green Note deleted successfully.');
        } catch (\Exception $e) {
            return redirect()
                ->route('backend.note.index')
                ->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }
    public function generateOrderNumber($type)
    {
        // Get the current financial year
        $currentYear = now()->year;
        $nextYear = $currentYear + 1;
        $financialYear = 'FY' . substr($currentYear, -2) . '-' . substr($nextYear, -2);

        // Get the latest order number from the GreenNote table
        $latestOrder = \App\Models\GreenNote::orderBy('id', 'desc')->first();

        // Generate the next sequence number
        if ($latestOrder && preg_match('/\/(\d{4})$/', $latestOrder->order_no, $matches)) {
            $sequence = intval($matches[1]) + 1;
        } else {
            $sequence = 1; // Start the sequence if no orders exist
        }

        // Ensure the sequence has 4 digits
        $sequenceFormatted = str_pad($sequence, 4, '0', STR_PAD_LEFT);

        // Combine the type, financial year, and sequence
        return "PO/{$financialYear}/{$sequenceFormatted}";
    }
    public function downloadGreenNotePdf($noteId)
    {
        // Retrieve the note from the database
        $note = GreenNote::findOrFail($noteId);
        $documents = SupportingDoc::where('green_note_id', $noteId)->get();
        $departments = Department::all();

        // Pass the note to the view and generate PDF
        $pdf = Pdf::loadView('backend.greenNote.partials.expense-download', [
            'note' => $note,
            'documents' => $documents,
            'departments' => $departments,
        ]);
        $pdf->set_option('isHtml5ParserEnabled', true);
        $pdf->set_option('isPhpEnabled', true);
        $pdf->set_option('isRemoteEnabled', true);
        // Return the generated PDF for download
        return $pdf->download('green_note_' . $note->order_no . '.pdf');
    }
    public function viewGreenNotePdf($noteId)
    {
        // Retrieve the note from the database
        $note = GreenNote::findOrFail($noteId);
        $documents = SupportingDoc::where('green_note_id', $noteId)->get();
        $departments = Department::all();

        // Pass the note to the view and generate PDF
        $pdf = Pdf::loadView('backend.greenNote.partials.expense-download', [
            'note' => $note,
            'documents' => $documents,
            'departments' => $departments,
        ]);
        $pdf->set_option('isHtml5ParserEnabled', true);
        $pdf->set_option('isPhpEnabled', true);
        $pdf->set_option('isRemoteEnabled', true);
        // Return the generated PDF for download
        return $pdf->stream('green_note_' . $note->order_no . '.pdf');
    }
    public function paymentGenerateOrderNumber()
    {
        // Get the current financial year
        $currentYear = now()->year;
        $nextYear = $currentYear + 1;
        $financialYear = substr($currentYear, -2) . '-' . substr($nextYear, -2) . '/' . 'PN';

        $latestOrder = PaymentNote::orderBy('id', 'desc')->first();
        if ($latestOrder && preg_match('/\/(\d{4})$/', $latestOrder->note_no, $matches)) {
            $sequence = intval($matches[1]) + 1;
        } else {
            $sequence = 1;
        }
        $sequenceFormatted = str_pad($sequence, 4, '0', STR_PAD_LEFT);
        return config('app.note_icon') . "/{$financialYear}/{$sequenceFormatted}";
    }
    public function paymentNote($id)
    {
        // Retrieve the note from the database
        $note = GreenNote::findOrFail($id);
        $orderNumber = $this->paymentGenerateOrderNumber();
        $grossAmount = $note->invoice_value ?? 0;
        $documents = SupportingDoc::where('green_note_id', $id)->get();
        return view('backend.paymentNote.create', compact('note', 'orderNumber', 'grossAmount', 'documents'));
    }

    public function rule()
    {
        $orderNumber = $this->generateOrderNumber('OP');
        $filteredItems = Vendor::selectRaw('id,project, COUNT(*) as total_records')->whereNotNull('project')->where('project', '!=', '')->groupBy('project')->orderBy('project', 'asc')->where('active', 'Y')->get();
        $filteredVendorItems = Vendor::where('active', 'Y')->get();
        $departments = Department::all();
        // Get users with various approval roles safely
        $users = \App\Services\RoleService::getUsersWithRoles(['GN Approver', 'approver', 'reviewer'])
            ->where('active', 'Y');
        $adminHrUsers = \App\Services\RoleService::getUsersWithRoles(['Hr And Admin', 'admin', 'hr'])
            ->where('active', 'Y');
        $auditorUsers = \App\Services\RoleService::getUsersWithRoles(['Auditor', 'auditor'])
            ->where('active', 'Y');
        $qsUsers = \App\Services\RoleService::getUsersWithRoles(['Qs', 'QS', 'quality', 'quality_assurance'])
            ->where('active', 'Y');
        $approvalFlows = ApprovalFlow::with('approvalSteps')->get();
        $approvalSteps = ApprovalStep::all();
        $approvalLogs = ApprovalLog::all();

        return view('backend.greenNote.rule.create', compact('orderNumber', 'filteredItems', 'departments', 'filteredVendorItems', 'approvalFlows', 'users', 'auditorUsers', 'adminHrUsers', 'qsUsers', 'approvalSteps', 'approvalLogs'));
    }
    public function importUsers(Request $request)
    {
        ini_set('max_execution_time', 600); // 10 Minutes
        set_time_limit(600); // Increase Execution Time

        $request->validate([
            'file' => 'required|mimes:xlsx,csv',
        ]);

        $file = $request->file('file');
        $data = Excel::toArray([], $file)[0];
        // dd($data);
        // Fixed departments
        foreach ($data as $index => $row) {
            if ($index == 0) {
                continue;
            } // Skip header row

            // Find user by name
            $user = User::where('name', trim($row[2]))->first();

            if ($user) {
                // Check if user exists
                $user->update([
                    'emp_id' => trim($row[1]),
                ]);
            }
        }
        // foreach ($data as $index => $row) {
        //     if ($index == 0) {
        //         continue;
        //     } // Skip header row

        //     $designationName = trim($row[3] ?? null); // Designation Column

        //     // Ensure Designation exists
        //     $designation = Designation::firstOrCreate(['name' => $designationName]);

        //     // Ensure Department exists (should match predefined ones)
        //     $randomDepartmentId = rand(1, 8);
        //     // Create or update user
        //     $user = User::updateOrCreate(
        //         ['email' => trim($row[5])], // Unique identifier
        //         [
        //             'name' => trim($row[2]),
        //             'designation_id' => $designation->id,
        //             'department_id' => $randomDepartmentId,
        //             'username' => Str::slug(trim($row[2]), '_'),
        //             'email' => trim($row[5]),
        //             'password' => Hash::make('123123'),
        //             'file' => '1740739320_test.png',
        //         ],
        //     );

        //     // Assign Roles
        //     $roles = [];

        //     if (trim($row[7]) === 'GN User') {
        //         $roles[] = 'GN User';
        //     }
        //     if (trim($row[8]) === 'GN Approver') {
        //         $roles[] = 'GN Approver';
        //     }
        //     if (trim($row[9]) === 'ER User') {
        //         $roles[] = 'ER User';
        //     }
        //     if (trim($row[10]) === 'ER Approver') {
        //         $roles[] = 'ER Approver';
        //     }
        //     if (trim($row[11]) === 'PN User') {
        //         $roles[] = 'PN User';
        //     }
        //     if (trim($row[12]) === 'PN Approver') {
        //         $roles[] = 'PN Approver';
        //     }

        //     // Sync Only the Specific Roles Assigned in the Excel Row
        //     $user->syncRoles($roles);
        // }
        return back()->with('success', 'Users imported successfully with roles!');
    }
    public function exportNoteExcel(Request $request)
    {
        $startDate = $request->start_date ? Carbon::parse($request->start_date)->startOfDay() : null;
        $endDate = $request->end_date ? Carbon::parse($request->end_date)->endOfDay() : null;

        $notesQuery = GreenNote::with(['paymentNotes', 'approvalLogs']);

        if ($startDate && $endDate) {
            $notesQuery->whereBetween('created_at', [$startDate, $endDate]);
        }

        $notes = $notesQuery->get();

        return Excel::download(new NoteExport($notes), 'note_export.xlsx');
    }

    /**
     * Show form for adding multiple invoices to a green note
     */
    public function showMultipleInvoices(GreenNote $greenNote)
    {
        $this->authorize('update', $greenNote);
        
        return view('backend.green-note.multiple-invoices', compact('greenNote'));
    }

    /**
     * Update green note with multiple invoices
     */
    public function updateMultipleInvoices(Request $request, GreenNote $greenNote)
    {
        $this->authorize('update', $greenNote);

        $request->validate([
            'invoices' => 'required|array|min:1',
            'invoices.*.invoice_number' => 'required|string|max:255',
            'invoices.*.invoice_date' => 'required|date',
            'invoices.*.invoice_value' => 'required|numeric|min:0',
            'invoices.*.invoice_base_value' => 'nullable|numeric|min:0',
            'invoices.*.invoice_gst' => 'nullable|numeric|min:0',
            'invoices.*.invoice_other_charges' => 'nullable|numeric|min:0',
            'invoices.*.description' => 'nullable|string|max:500',
        ]);

        try {
            $greenNoteService = new \App\Services\GreenNoteService();
            $greenNoteService->updateInvoices($greenNote, $request->invoices);

            return redirect()
                ->route('backend.green-note.show', $greenNote)
                ->with('success', 'Multiple invoices updated successfully.');

        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->withErrors(['error' => 'Failed to update invoices: ' . $e->getMessage()]);
        }
    }

    /**
     * Put green note on hold
     */
    public function putOnHold(Request $request, GreenNote $greenNote)
    {
        $this->authorize('update', $greenNote);

        $request->validate([
            'hold_reason' => 'required|string|max:1000',
        ]);

        try {
            $greenNoteService = new \App\Services\GreenNoteService();
            $greenNoteService->putOnHold($greenNote, $request->hold_reason, auth()->user());

            return back()->with('success', 'Green note put on hold successfully.');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to put note on hold: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove green note from hold
     */
    public function removeFromHold(Request $request, GreenNote $greenNote)
    {
        $this->authorize('update', $greenNote);

        $request->validate([
            'new_status' => 'required|in:P,D,S',
        ]);

        try {
            $greenNoteService = new \App\Services\GreenNoteService();
            $greenNoteService->removeFromHold($greenNote, auth()->user(), $request->new_status);

            return back()->with('success', 'Green note removed from hold successfully.');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to remove note from hold: ' . $e->getMessage()]);
        }
    }

    /**
     * Get invoice summary for AJAX requests
     */
    public function getInvoiceSummary(GreenNote $greenNote)
    {
        try {
            $greenNoteService = new \App\Services\GreenNoteService();
            $summary = $greenNoteService->getInvoiceSummary($greenNote);

            return response()->json([
                'success' => true,
                'data' => $summary
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get invoice summary: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Approve green note and auto-create payment note draft
     */
    public function approveWithPaymentNote(Request $request, GreenNote $greenNote)
    {
        $this->authorize('approve', $greenNote);

        $request->validate([
            'comments' => 'nullable|string|max:1000',
        ]);

        try {
            $greenNoteService = new \App\Services\GreenNoteService();
            $result = $greenNoteService->approveGreenNote($greenNote, auth()->user(), $request->comments);

            return redirect()
                ->route('backend.green-note.show', $greenNote)
                ->with('success', 'Green note approved and draft payment note created successfully.')
                ->with('payment_note_id', $result['paymentNote']->id);

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to approve note: ' . $e->getMessage()]);
        }
    }

    /**
     * Create payment note from green note
     */
    public function createPaymentNote(GreenNote $greenNote)
    {
        $this->authorize('view', $greenNote);

        // Generate payment note order number
        $orderNumber = PaymentNote::generateOrderNumber();

        // Calculate gross amount from green note
        $grossAmount = ($greenNote->invoice_base_value ?? 0) + ($greenNote->invoice_gst ?? 0) + ($greenNote->invoice_other_charges ?? 0);

        // Get supporting documents
        $documents = SupportingDoc::where('green_note_id', $greenNote->id)->get();

        return view('backend.paymentNote.create', compact('greenNote', 'orderNumber', 'grossAmount', 'documents'));
    }
}
