<?php

namespace App\Http\Controllers\backend\Reimbursement;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\PaymentNote;
use App\Models\ReimbursementApprovalLog;
use App\Models\ReimbursementExpenseDetail;
use App\Models\ReimbursementNote;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PDF;
use App\Mail\NoteStatusChangeMail;
use App\Models\PaymentNoteApprovalLog;
use App\Models\PaymentNoteLogPriority;
use Illuminate\Support\Facades\Mail;
use Yajra\DataTables\Facades\DataTables;

class ReimbursementNoteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $authId = auth()->id();
        $status = request()->status;
        $query = ReimbursementNote::query();
        $statusSearchMap = [
            'Draft' => 'D',
            'Pending' => 'P',
            'Approved' => 'A',
            'Rejected' => 'R',
            'Sent for Approval' => 'S',
            'RTGS/NEFT Created' => 'B',
            'Payment note Approved' => 'PNA',
            'Payment Approved' => 'PA',
            'Paid' => 'PD',
        ];

        // if (request()->filled('search')) {
        //     $search = request()->input('search');
        //     $matchingStatusCodes = collect($statusSearchMap)
        //         ->filter(function ($code, $label) use ($search) {
        //             return similar_text(strtolower($label), $search) >= 3 || str_contains(strtolower($label), $search);
        //         })
        //         ->values()
        //         ->all();
        //     $isProcessedSearch = str_contains(strtolower($search), 'processed') || str_contains(strtolower($search), 'payment note processed');

        //     $query->where(function ($q) use ($search, $statusSearchMap, $matchingStatusCodes, $isProcessedSearch) {
        //         $q->where('id', 'like', '%' . $search . '%')
        //             ->orWhereHas('project', function ($q2) use ($search) {
        //                 $q2->where('project', 'like', "%{$search}%");
        //             })
        //             ->orWhereHas('selectUser', function ($q2) use ($search) {
        //                 $q2->where('name', 'like', "%{$search}%");
        //             })
        //             ->orWhereHas('user', function ($q2) use ($search) {
        //                 $q2->where('name', 'like', "%{$search}%");
        //             })
        //             ->orWhereRaw("DATE_FORMAT(CONVERT_TZ(created_at, '+00:00', '+05:30'), '%d/%m/%Y %h:%i %p') LIKE ?", ["%{$search}%"])
        //             ->orWhereRaw('((SELECT IFNULL(SUM(bill_amount), 0) FROM reimbursement_expense_details  WHERE reimbursement_note_id = reimbursement_notes.id) - reimbursement_notes.adjusted ) LIKE ?', ["%{$search}%"]);

        //         if (!empty($matchingStatusCodes)) {
        //             $q->orWhereIn('status', $matchingStatusCodes);
        //         }
        //         // ✅ UTR filter
        //         $q->orWhereHas('paymentNote', function ($utrQ) use ($search) {
        //             $utrQ->where('utr_no', 'like', "%$search%")->orWhere('utr_date', 'like', "%$search%");
        //         });
        //         if ($isProcessedSearch) {
        //             $q->orWhereHas('paymentNote', function ($paymentNoteQuery) {
        //                 $paymentNoteQuery->whereHas('paymentApprovalLogs', function ($logQuery) {
        //                     $logQuery->where('status', '!=', 'R'); // Not rejected
        //                 });
        //             });
        //         }
        //     });
        // }
        if (empty($status)) {
            $status = 'S';
        }

        if ($status && $status !== null && $status !== 'all') {
            $query->where('status', $status);
            // dd(2);
        }
        // dd(1);

        // if ($authId === 1) {
        //     $notes = $query->latest()->paginate(10)->onEachSide(1);
        // } else {
        //     if ($status === 'all') {
        //         if (auth()->user()->can('all-reimbursement-note')) {
        //             $notes = $query->latest()->paginate(10)->onEachSide(1);
        //         } else {
        //             $userRoles = auth()->user()->getRoleNames();
        //             if ($userRoles->contains('PN User')) {
        //                 $notes = $query->latest()->paginate(10)->onEachSide(1);
        //             } else {
        //                 $notes = $query->where('user_id', $authId)->orWhere('approver_id', $authId)->where('status', '!=', 'D')->latest()->paginate(10);
        //             }
        //         }
        //     } else {
        //         $notes = $query->where('user_id', $authId)->orWhere('approver_id', $authId)->where('status', '!=', 'D')->latest()->paginate(10);
        //     }
        // }
        $userRoles = auth()->user()->getRoleNames();

        if (!($authId === 1 || ($status === 'all' && (auth()->user()->can('all-reimbursement-note') || $userRoles->contains('PN User'))))) {
            $query->where(function ($q) use ($authId) {
                $q->where('user_id', $authId)->orWhere('approver_id', $authId);
            });
        }

        if ($request->ajax()) {
            $query = $query->select('id', 'user_id', 'approver_id', 'status', 'created_at', 'project_id')
                ->with(['project', 'selectUser', 'user', 'expenses']);

            return DataTables::eloquent($query)
                ->addIndexColumn()
                ->addColumn('project_name', function ($row) {
                    return optional($row->project)->project ?: '-';
                })
                ->addColumn('employee_name', function ($row) {
                    return optional($row->selectUser ?: $row->user)->name ?: '-';
                })
                ->addColumn('amount', function ($row) {
                    $totalPayable = optional($row->expenses)->sum('bill_amount');
                    $advanceAdjusted = (float) ($row->adjusted ?? 0);
                    $netPayable = (float) $totalPayable - $advanceAdjusted;
                    return Helper::formatIndianNumber($netPayable) ?: '-';
                })
                ->addColumn('date', function ($row) {
                    return optional($row->created_at)->setTimezone('Asia/Kolkata')->format('d/m/Y h:i A');
                })
                ->addColumn('status_badge', function ($row) {
                    $statusLabels = [
                        'D' => '<span class="badge bg-dark">Draft</span>',
                        'P' => '<span class="badge bg-warning">Pending</span>',
                        'A' => '<span class="badge bg-success">Approved</span>',
                        'R' => '<span class="badge bg-danger">Rejected</span>',
                        'S' => '<span class="badge bg-secondary">Sent for Approval</span>',
                        'B' => '<span class="badge bg-black">RTGS/NEFT Created </span>',
                        'PNA' => '<span class="badge bg-info">Payment Note Approved </span>',
                        'PA' => '<span class="badge bg-black">Payment Approved </span>',
                        'PD' => '<span class="badge bg-info">Paid</span>',
                    ];
                    return $statusLabels[$row->status] ?? '-';
                })
                ->addColumn('action', function ($row) {
                    $actions = '';
                    if (auth()->user()->id == $row->user_id && auth()->user()->can('edit-reimbursement-note') && $row->status == 'D') {
                        $actions .= '<a href="' . route('backend.reimbursement-note.edit', $row->id) . '"><i class="bi bi-pencil-square"></i></a> | ';
                    }
                    if (auth()->user()->getRoleNames()->contains('Admin') && $row->status !== 'D' && auth()->user()->can('edit-reimbursement-note')) {
                        $actions .= '<a href="' . route('backend.reimbursement-note.edit', $row->id) . '"><i class="bi bi-pencil-square"></i></a> | ';
                    }
                    $actions .= '<a href="' . route('backend.reimbursement-note.show', $row->id) . '"><i class="bi bi-eye"></i></a>';
                    return $actions;
                })
                ->rawColumns(['status_badge', 'action'])
                ->toJson();
        }

        $notes = $query->orderBy('created_at', 'desc')->get();
        return view('backend.reimbursementNote.index', compact('notes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $orderNumber = $this->reimbursementGenerateOrderNumber();
        $filteredItems = Vendor::selectRaw('id,project, COUNT(*) as total_records')->whereNotNull('project')->where('project', '!=', '')->groupBy('project')->orderBy('project', 'asc')->where('active', 'Y')->get();
        $filteredVendorItems = Vendor::where('active', 'Y')->get();
        $departments = Department::all();
        // $user = Auth::user();

        $userId = session()->pull('selected_user_id'); // removes after using
        $user = $userId ? User::with('department', 'designation')->findOrFail($userId) : auth()->user();

        // $users = User::role('ER Approver')->get();
        $users = User::role('ER Approver')
            ->where('active', 'Y')
            ->where('id', '!=', auth()->id())
            ->get();

        return view('backend.reimbursementNote.create', compact('user', 'orderNumber', 'filteredItems', 'departments', 'filteredVendorItems', 'users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'nullable|string',
            'select_user_id' => 'nullable|string',
            'note_no' => 'nullable|string',
            'date_of_travel' => 'required|date',
            'project_id' => 'required',
            'mode_of_travel' => 'required|string',
            'travel_mode_eligibility' => 'required|string',
            'approver_id' => 'required|string',
            'approver_designation' => 'nullable|string',
            'approval_date' => 'nullable|date',
            'purpose_of_travel' => 'nullable|string',
            'adjusted' => 'nullable|string',
            'account_holder' => 'required|string',
            'bank_name' => 'required|string',
            'bank_account' => 'required|string',
            'IFSC_code' => 'required|string',
            'expense_type.*' => 'nullable|string',
            'bill_date.*' => 'nullable|date',
            'bill_number.*' => 'nullable|string',
            'vendor_id.*' => 'nullable|exists:vendors,id',
            'bill_amount.*' => 'nullable|numeric',
            'supporting_available.*' => 'nullable|string',
            'remarks.*' => 'nullable|string',
            'file_path.*' => 'file|mimes:pdf,doc,docx,xls,xlsx,csv|max:10240000',
        ]);
        $files = [];
        if ($request->hasFile('file_path')) {
            foreach ($request->file('file_path') as $file) {
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->storeAs('public/rn', $filename);
                $files[] = $filename;
            }
        }

        $travelExpense = ReimbursementNote::create([
            'user_id' => $request->user_id,
            'select_user_id' => $request->select_user_id,
            'project_id' => $request->project_id,
            'note_no' => $request->note_no,
            'date_of_travel' => $request->date_of_travel,
            'mode_of_travel' => $request->mode_of_travel,
            'travel_mode_eligibility' => $request->travel_mode_eligibility,
            'approver_id' => $request->approver_id,
            'approver_designation' => $request->approver_designation,
            'approval_date' => $request->approval_date,
            'purpose_of_travel' => $request->purpose_of_travel,
            'adjusted' => $request->adjusted,
            'account_holder' => $request->account_holder,
            'bank_account' => $request->bank_account,
            'bank_name' => $request->bank_name,
            'IFSC_code' => $request->IFSC_code,
            'file_path' => json_encode($files),
            'status' => 'D',
        ]);

        // Save multiple expense details
        if ($request->has('expense_type')) {
            foreach ($request->expense_type as $index => $expenseType) {
                if (empty($expenseType)) {
                    continue;
                }

                ReimbursementExpenseDetail::create([
                    'reimbursement_note_id' => $travelExpense->id ?? null,
                    'expense_type' => $expenseType ?? null,
                    'bill_date' => $request->bill_date[$index] ?? null,
                    'bill_number' => $request->bill_number[$index] ?? null,
                    'vendor_name' => $request->vendor_name[$index] ?? null,
                    'bill_amount' => $request->bill_amount[$index] ?? null,
                    'supporting_available' => $request->supporting_available[$index] ?? null,
                    'remarks' => $request->remarks[$index] ?? null,
                ]);
            }
        }

        return redirect()->route('backend.reimbursement-note.index')->with('success', 'Reimbursement Note added successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(ReimbursementNote $reimbursementNote, $id)
    {
        $note = ReimbursementNote::with('expenses')->findOrFail($id);

        return view('backend.reimbursementNote.show', compact('note'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ReimbursementNote $reimbursementNote, $id)
    {
        $filteredItems = Vendor::selectRaw('id,project, COUNT(*) as total_records')->whereNotNull('project')->where('project', '!=', '')->groupBy('project')->orderBy('project', 'asc')->where('active', 'Y')->get();
        $filteredVendorItems = Vendor::where('active', 'Y')->get();
        $departments = Department::all();
        $user = Auth::user();
        // $users = User::role('ER Approver')->get();
        $users = User::role('ER Approver')
            ->where('id', '!=', auth()->id())
            ->get();
        $note = ReimbursementNote::with('expenses')->findOrFail($id);
        return view('backend.reimbursementNote.edit', compact('note', 'user', 'filteredItems', 'departments', 'filteredVendorItems', 'users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ReimbursementNote $reimbursementNote, $id)
    {
        $request->validate([
            'user_id' => 'nullable|string',
            'note_no' => 'nullable|string',
            'date_of_travel' => 'required|date',
            'project_id' => 'required',
            'mode_of_travel' => 'required|string',
            'travel_mode_eligibility' => 'required|string',
            'approver_id' => 'required|string',
            'approver_designation' => 'nullable|string',
            'approval_date' => 'nullable|date',
            'purpose_of_travel' => 'nullable|string',
            'adjusted' => 'nullable|string',
            'account_holder' => 'required|string',
            'bank_name' => 'required|string',
            'bank_account' => 'required|string',
            'IFSC_code' => 'required|string',
            'expense_type.*' => 'nullable|string',
            'bill_date.*' => 'nullable|date',
            'bill_number.*' => 'nullable|string',
            'vendor_id.*' => 'nullable|exists:vendors,id',
            'bill_amount.*' => 'nullable|numeric',
            'supporting_available.*' => 'nullable|string',
            'status' => 'required|in:D,P,A,R,S',
            'remarks.*' => 'nullable|string',
            'file_path.*' => 'file|mimes:pdf,doc,docx,xls,xlsx,csv|max:10240000',
        ]);
        // Find the existing travel expense record
        $travelExpense = ReimbursementNote::findOrFail($id);
        $totalPayable = $travelExpense->expenses->sum('bill_amount');
        $advanceAdjusted = $travelExpense->adjusted;
        $netPayable = $totalPayable - $advanceAdjusted;

        // Update main travel expense details
        $existingFiles = json_decode($travelExpense->file_path, true) ?? [];

        $newFiles = [];
        if ($request->hasFile('file_path')) {
            foreach ($request->file('file_path') as $file) {
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->storeAs('public/rn', $filename);
                $newFiles[] = $filename;
            }
        }

        // Purane aur naye files ko merge karenge
        $allFiles = array_merge($existingFiles, $newFiles);

        // Update travel expense details + files
        $travelExpense->update(array_merge($request->only(['user_id', 'project_id', 'note_no', 'date_of_travel', 'mode_of_travel', 'travel_mode_eligibility', 'approver_id', 'approver_designation', 'approval_date', 'purpose_of_travel', 'adjusted', 'account_holder', 'bank_account', 'bank_name', 'IFSC_code', 'status']), ['file_path' => json_encode($allFiles)]));
        // $travelExpense->update($request->only(['user_id', 'project_id', 'note_no', 'date_of_travel', 'mode_of_travel', 'travel_mode_eligibility', 'approver_id', 'approver_designation', 'approval_date', 'purpose_of_travel', 'adjusted', 'account_holder', 'bank_account', 'bank_name', 'IFSC_code', 'status']));

        // Delete old expense details to avoid duplication
        $travelExpense->expenses()->delete();

        // Save multiple expense details
        if ($request->has('expense_type')) {
            foreach ($request->expense_type as $index => $expenseType) {
                if (empty($expenseType)) {
                    continue;
                }

                ReimbursementExpenseDetail::create([
                    'reimbursement_note_id' => $travelExpense->id,
                    'expense_type' => $expenseType,
                    'bill_date' => $request->bill_date[$index] ?? null,
                    'bill_number' => $request->bill_number[$index] ?? null,
                    'vendor_name' => $request->vendor_name[$index] ?? null,
                    'bill_amount' => $request->bill_amount[$index] ?? null,
                    'supporting_available' => $request->supporting_available[$index] ?? null,
                    'remarks' => $request->remarks[$index] ?? null,
                ]);
            }
        }

        $totalPayable = $travelExpense->expenses->sum('bill_amount');
        $advanceAdjusted = $travelExpense->adjusted;
        $netPayable = $totalPayable - $advanceAdjusted;
        if ($request->status == 'S') {
            $data = [
                'updated_by' => auth()->user()->email,
                'subject' => 'Reimbursement Note for (' . $travelExpense->user->name . ') of Rs (' . $netPayable . ') has been Generated',
                'approver_name' => $travelExpense->approver->name ?? 'Approver',
                'maker' => $travelExpense->user->name . ' has generated a Reimbursement Note (' . $travelExpense->note_no . ') of Rs ' . Helper::formatIndianNumber($netPayable) . ' for ' . $travelExpense->project->project . ' & it is due for your approval.',
                'end' => 'Login to the panel for review & Approval/Rejection',
            ];
            // Mail Send
            // Mail::to('goseg75168@doishy.com')->send(new NoteStatusChangeMail($data));
            Mail::to($travelExpense->approver->email)->send(new NoteStatusChangeMail($data));
        }

        return redirect()->route('backend.reimbursement-note.index')->with('success', 'Reimbursement Note updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ReimbursementNote $reimbursementNote, $id)
    {
        try {
            $travelExpense = ReimbursementNote::findOrFail($id);
            ReimbursementApprovalLog::where('reimbursement_note_id', $id)->delete();
            if ($travelExpense->expenses()->exists()) {
                foreach ($travelExpense->expenses as $expense) {
                    $expense->delete();
                }
            }
            $paymentNote = PaymentNote::where('reimbursement_note_id', $id)->first();

            if ($paymentNote) {
                $logs = PaymentNoteApprovalLog::where('payment_note_id', $paymentNote->id)->get();

                foreach ($logs as $log) {
                    PaymentNoteLogPriority::where('payment_note_approval_log_id', $log->id)->delete();
                }

                PaymentNoteApprovalLog::where('payment_note_id', $paymentNote->id)->delete();
                $paymentNote->delete(); // Delete Payment Note
            }

            // dd(1);
            $travelExpense->delete();

            return redirect()->route('backend.reimbursement-note.index')->with('success', 'Reimbursement Note deleted successfully.');
        } catch (\Exception $e) {
            return redirect()
                ->route('backend.reimbursement-note.index')
                ->with('error', 'Something went wrong ' . $e->getMessage());
        }
    }
    public function reimbursementGenerateOrderNumber()
    {
        // Get the current financial year
        // $currentYear = now()->year;
        // $nextYear = $currentYear + 1;
        // $financialYear = substr($currentYear, -2) . '-' . substr($nextYear, -2) . '/' . 'RN';

        // $latestOrder = ReimbursementNote::orderBy('id', 'desc')->first();
        // if ($latestOrder && preg_match('/\/(\d{4})$/', $latestOrder->note_no, $matches)) {
        //     $sequence = intval($matches[1]) + 1;
        // } else {
        //     $sequence = 1;
        // }
        // $sequenceFormatted = str_pad($sequence, 4, '0', STR_PAD_LEFT);
        // return "W/{$financialYear}/{$sequenceFormatted}";
        $currentMonth = now()->month;
        $currentYear = now()->year;
        $previousYear = $currentYear - 1;
        $nextYear = $currentYear + 1;

        // Determine financial year range (April - March)
        if ($currentMonth >= 4) {
            $financialStartYear = $currentYear;
            $financialEndYear = $nextYear;
        } else {
            $financialStartYear = $previousYear;
            $financialEndYear = $currentYear;
        }

        // Extract last two digits of the financial years
        $shortStartYear = substr($financialStartYear, -2);
        $shortEndYear = substr($financialEndYear, -2);

        // Construct the financial year format
        $financialYear = "{$shortStartYear}-{$shortEndYear}/RN";

        // Get the last sequence number
        $latestOrder = ReimbursementNote::orderBy('id', 'desc')->first();
        if ($latestOrder && preg_match('/\/(\d{4})$/', $latestOrder->note_no, $matches)) {
            $sequence = intval($matches[1]) + 1;
        } else {
            $sequence = 1;
        }

        // Format the sequence with four leading zeros
        $sequenceFormatted = str_pad($sequence, 4, '0', STR_PAD_LEFT);

        // Generate the final formatted string
        return "E/{$financialYear}/{$sequenceFormatted}";
    }
    public function approvalLogUpdate(Request $request, $id)
    {
        // Find the Green Note
        $reimbursementNote = ReimbursementNote::findOrFail($id);
        ReimbursementApprovalLog::create([
            'reimbursement_note_id' => $reimbursementNote->id,
            'reviewer_id' => auth()->id(),
            'status' => $request->status,
            'comments' => $request->remarks ?? null,
        ]);

        $totalPayable = $reimbursementNote->expenses->sum('bill_amount');
        $advanceAdjusted = $reimbursementNote->adjusted;
        $netPayable = $totalPayable - $advanceAdjusted;
        if ($request->status == 'A') {
            $data = [
                'updated_by' => auth()->user()->email,
                'subject' => 'Reimbursement Note for ' . $reimbursementNote->user->name . ' of Rs' . $netPayable . '& due for review / Payment',
                'approver_name' => 'Maker',
                'maker' => $reimbursementNote->user->name . ' has The Reimbursement Note ' . $reimbursementNote->note_no . ' for ' . $reimbursementNote->user->name . ' of Rs ' . Helper::formatIndianNumber($netPayable) . ' for ' . $reimbursementNote->project->project . '& due for your review / Payment.',
                'end' => 'Login to the panel for review & process. ',
            ];

            $users = User::role('PN User')->get();
            foreach ($users as $key => $value) {
                Mail::to($value->email)->send(new NoteStatusChangeMail($data));
            }
            // Mail::to('goseg75168@doishy.com')->send(new NoteStatusChangeMail($data));
            Mail::to($reimbursementNote->user->email)->send(new NoteStatusChangeMail($data));
        } else {
            $data = [
                'updated_by' => auth()->user()->email,
                'subject' => 'Reimbursement Note for (' . $reimbursementNote->user->name . ') of Rs (' . $netPayable . ') has been ' . ($request->status === 'A' ? 'Approved' : 'Rejected'),
                'approver_name' => $reimbursementNote->approver->name,
                'maker' => $reimbursementNote->approver->name . ' has ' . ($request->status === 'A' ? 'Approved' : 'Rejected') . 'The Reimbursement Note ' . $reimbursementNote->note_no . ' for ' . $reimbursementNote->approver->name . ' of Rs ' . Helper::formatIndianNumber($netPayable) . ' for ' . $reimbursementNote->project->project,
                'rejection' => $request->remarks ?? null,
                'end' => 'Login to the panel for review & Re-process.',
            ];
            // Mail::to('goseg75168@doishy.com')->send(new NoteStatusChangeMail($data));
            Mail::to($reimbursementNote->user->email)->send(new NoteStatusChangeMail($data));
        }

        $reimbursementNote->status = $request->status;
        $reimbursementNote->save();
        return redirect()->route('backend.reimbursement-note.index')->with('success', 'updated successfully!.');
    }
    public function downloadNotePdf($id)
    {
        // Retrieve the note from the database
        $note = ReimbursementNote::with('expenses')->findOrFail($id);

        // Pass the note to the view and generate PDF
        $pdf = Pdf::loadView('backend.reimbursementNote.partials.reimbursement-download', [
            'note' => $note,
        ]);
        $pdf->set_option('isHtml5ParserEnabled', true);
        $pdf->set_option('isPhpEnabled', true);
        $pdf->set_option('isRemoteEnabled', true);
        return $pdf->download('Reimbursement_note_' . $note->id . '.pdf');
    }
    public function viewNotePdf($id)
    {
        // Retrieve the note from the database
        $note = ReimbursementNote::with('expenses')->findOrFail($id);

        // Pass the note to the view and generate PDF
        $pdf = Pdf::loadView('backend.reimbursementNote.partials.reimbursement-download', [
            'note' => $note,
        ]);
        $pdf->set_option('isHtml5ParserEnabled', true);
        $pdf->set_option('isPhpEnabled', true);
        $pdf->set_option('isRemoteEnabled', true);
        return $pdf->stream('Reimbursement_note_' . $note->id . '.pdf');
    }
    public function paymentNote($id)
    {
        // Retrieve the note from the database
        $note = ReimbursementNote::findOrFail($id);
        $orderNumber = $this->paymentGenerateOrderNumber();
        $grossAmount = ($note->invoice_base_value ?? 0) + ($note->invoice_gst ?? 0) + ($note->invoice_other_charges ?? 0);

        return view('backend.paymentNote.createRN', compact('note', 'orderNumber', 'grossAmount'));
    }
    public function userSelection()
    {
        $users = User::where('active', 'Y')->get();
        return view('backend.reimbursementNote.user-select', compact('users'));
    }
    public function selectUser(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        session(['selected_user_id' => $request->user_id]);

        return redirect()->route('backend.reimbursement-note.create');
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
    public function deleteFile($id, $filename)
    {
        $travelExpense = ReimbursementNote::findOrFail($id);

        // Existing files ko fetch karna
        $existingFiles = json_decode($travelExpense->file_path, true) ?? [];

        // Agar file mil jaye to remove karna
        if (($key = array_search($filename, $existingFiles)) !== false) {
            unset($existingFiles[$key]); // Array se hata diya
        }

        // Database update with new files list
        $travelExpense->update([
            'file_path' => json_encode(array_values($existingFiles)), // Re-index array
        ]);

        // File ko server se bhi delete karna
        $filePath = public_path('storage/rn/' . $filename);
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        return back()->with('success', 'File deleted successfully!');
    }
}
