<?php

namespace App\Http\Controllers\backend\PaymentNote;

use App\Http\Controllers\Controller;

use App\Models\PaymentNote;
use App\Models\PaymentNoteApprovalLog;
use App\Models\PaymentNoteApprovalPriority;
use App\Models\PaymentNoteApprovalStep;
use App\Models\SupportingDoc;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use NumberFormatter;
use PDF;
use App\Mail\NoteStatusChangeMail;
use App\Mail\PaymentEmail;
use App\Models\GreenNote;
use App\Models\PaymentNoteLogPriority;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class PaymentNoteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $authId = Auth::id();

        // $notesWithoutApprovalLogs = PaymentNote::whereDoesntHave('paymentApprovalLogs')->get();
        // $notesWithoutApprovalLogs = PaymentNote::with('paymentApprovalLogs')
        //     ->where('id', '>', 1352)
        //     ->whereDoesntHave('paymentApprovalLogs')
        //     ->get();

        // foreach ($notesWithoutApprovalLogs as $note) {
        //     PaymentNoteApprovalLog::create([
        //         'payment_note_id' => $note->id,
        //         'priority_id'     => 1,
        //         'reviewer_id'     => 94,
        //         'status'          => 'P',
        //         'comments'        => null,
        //     ]);

        //     PaymentNoteApprovalLog::create([
        //         'payment_note_id' => $note->id,
        //         'priority_id'     => 2,
        //         'reviewer_id'     => 15,
        //         'status'          => 'A',
        //         'comments'        => null,
        //     ]);

        //     PaymentNoteApprovalLog::create([
        //         'payment_note_id' => $note->id,
        //         'priority_id'     => 2,
        //         'reviewer_id'     => 62,
        //         'status'          => 'A',
        //         'comments'        => null,
        //     ]);
        // }
        // dd($notesWithoutApprovalLogs);
        // if ($authId === 1 || auth()->user()->can('all-payment-note')) {
        //     $notes = PaymentNote::orderBy('created_at', 'desc')->get();
        // } else {
        //     $notes = PaymentNote::whereHas('paymentApprovalLogs.logPriorities.priority', function ($query) use ($authId) {
        //         $query->where('reviewer_id', $authId);
        //     })
        //         ->orWhere('user_id', $authId)
        //         ->orderBy('created_at', 'desc')
        //         ->get();
        // }

        $status = request()->status;
        $query = PaymentNote::query();

        $userRoles = auth()->user()->getRoleNames();

        if ($authId === 1 || ($status === 'all' && auth()->user()->can('all-payment-note'))) {
            $query = $query->orderBy('created_at', 'desc');
        } else {
            $latestLogIds = PaymentNoteApprovalLog::selectRaw('MAX(id) as id')->groupBy('payment_note_id')->pluck('id');

            $query = $query
                ->where(function ($q) use ($authId, $latestLogIds) {
                    $q->whereHas('paymentApprovalLogs', function ($logQuery) use ($authId, $latestLogIds) {
                        $logQuery->whereIn('id', $latestLogIds)->where(function ($inner) use ($authId) {
                            $inner
                                ->whereHas('logPriorities.priority', function ($priorityQuery) use ($authId) {
                                    $priorityQuery->where('reviewer_id', $authId);
                                })
                                ->orWhereDoesntHave('logPriorities.priority')
                                ->where('reviewer_id', $authId);
                        });
                    })->orWhere('user_id', $authId);
                })
                ->orderBy('created_at', 'desc');
        }
        // if ($status === 'all') {
        //     if ($authId === 1 || auth()->user()->can('all-payment-note')) {
        //         $query = $query->orderBy('created_at', 'desc');
        //     } else {
        //         $latestLogIds = PaymentNoteApprovalLog::selectRaw('MAX(id) as id')->groupBy('payment_note_id')->pluck('id');

        //         $query = $query
        //             ->where(function ($q) use ($authId, $latestLogIds) {
        //                 $q->whereHas('paymentApprovalLogs', function ($logQuery) use ($authId, $latestLogIds) {
        //                     $logQuery->whereIn('id', $latestLogIds)->whereHas('logPriorities.priority', function ($subQuery) use ($authId) {
        //                         $subQuery->where('reviewer_id', $authId);
        //                     });
        //                 })->orWhere('user_id', $authId);
        //             })
        //             ->orderBy('created_at', 'desc');
        //     }
        // } else {
        //     $latestLogIds = PaymentNoteApprovalLog::selectRaw('MAX(id) as id')
        //         ->groupBy('payment_note_id')
        //         ->pluck('id');

        //     $query = $query
        //         ->where(function ($q) use ($authId, $latestLogIds) {
        //             $q->whereHas('paymentApprovalLogs', function ($logQuery) use ($authId, $latestLogIds) {
        //                 $logQuery->whereIn('id', $latestLogIds)
        //                     ->where(function ($inner) use ($authId) {
        //                         $inner->whereHas('logPriorities.priority', function ($priorityQuery) use ($authId) {
        //                             $priorityQuery->where('reviewer_id', $authId);
        //                         })
        //                             ->orWhereDoesntHave('logPriorities.priority') // if no priority
        //                             ->where('reviewer_id', $authId); // fallback if not in priorities
        //                     });
        //             })
        //                 ->orWhere('user_id', $authId);
        //         })
        //         ->orderBy('created_at', 'desc');
        // }
        if (empty($status)) {
            $status = 'S';
        }
        // ✅ Add optional status filter
        if ($status !== 'all') {
            $query->where('status', $status);
        }
        // Server-side DataTables response
        if (request()->ajax()) {
            $query = $query->select('id', 'user_id', 'status', 'created_at', 'green_note_id', 'reimbursement_note_id', 'net_payable_round_off')
                ->with([
                    'greenNote.vendor',
                    'greenNote.supplier',
                    'reimbursementNote.project',
                    'reimbursementNote.selectUser',
                    'reimbursementNote.user',
                    'paymentApprovalLogs.logPriorities.priority.user',
                ]);

            return \Yajra\DataTables\Facades\DataTables::eloquent($query)
                ->addIndexColumn()
                ->addColumn('project_name', function ($row) {
                    if ($row->greenNote) {
                        return optional(optional($row->greenNote)->vendor)->project ?: '-';
                    }
                    if ($row->reimbursementNote) {
                        return optional(optional($row->reimbursementNote)->project)->project ?: '-';
                    }
                    return '-';
                })
                ->addColumn('vendor_name', function ($row) {
                    if ($row->greenNote) {
                        return optional(optional($row->greenNote)->supplier)->vendor_name ?: '-';
                    }
                    if ($row->reimbursementNote) {
                        return optional($row->reimbursementNote->selectUser ?: $row->reimbursementNote->user)->name ?: '-';
                    }
                    return '-';
                })
                ->addColumn('amount', function ($row) {
                    return \App\Helpers\Helper::formatIndianNumber($row->net_payable_round_off) ?: '-';
                })
                ->addColumn('date', function ($row) {
                    return optional($row->created_at)->setTimezone('Asia/Kolkata')->format('d/m/Y h:i A') ?: '-';
                })
                ->addColumn('status_badge', function ($row) {
                    $statusLabels = [
                        'D' => '<span class="badge bg-dark">Draft</span>',
                        'P' => '<span class="badge bg-warning">Pending</span>',
                        'A' => '<span class="badge bg-success">Approved</span>',
                        'R' => '<span class="badge bg-danger">Rejected</span>',
                        'S' => '<span class="badge bg-secondary">Sent for Approval</span>',
                        'B' => '<span class="badge bg-black">RTGS/NEFT Created</span>',
                        'PA' => '<span class="badge bg-black">Payment Approved </span>',
                        'PD' => '<span class="badge bg-info">Paid</span>',
                    ];
                    return $statusLabels[$row->status] ?? '-';
                })
                ->addColumn('action', function ($row) use ($authId) {
                    $userRoles = auth()->user()->getRoleNames();
                    $actions = '';
                    if (auth()->user()->hasRole('Admin') || ($authId === $row->user_id && $row->status === 'D')) {
                        $actions .= '<a href="' . route('backend.payment-note.edit', $row->id) . '"><i class="bi bi-pencil-square"></i></a> | ';
                    }
                    $actions .= '<a href="' . route('backend.payment-note.show', $row->id) . '"><i class="bi bi-eye"></i></a>';
                    return $actions;
                })
                ->rawColumns(['status_badge', 'action'])
                ->toJson();
        }

        $notes = $query->get();
        return view('backend.paymentNote.index', compact('notes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $greenNoteId = $request->get('green_note_id');
        $greenNote = null;

        if ($greenNoteId) {
            $greenNote = GreenNote::with(['supplier', 'department'])->find($greenNoteId);
        }

        // Generate payment note order number
        $orderNumber = $this->generatePaymentNoteOrderNumber();

        return view('backend.paymentNote.create', compact('greenNote', 'orderNumber'));
    }

    /**
     * Generate payment note order number
     */
    private function generatePaymentNoteOrderNumber()
    {
        $currentMonth = date('n');
        $currentYear = date('Y');
        $previousYear = $currentYear - 1;

        if ($currentMonth >= 4) {
            $financialStartYear = $currentYear;
            $financialEndYear = $currentYear + 1;
        } else {
            $financialStartYear = $previousYear;
            $financialEndYear = $currentYear;
        }

        $shortStartYear = substr($financialStartYear, -2);
        $shortEndYear = substr($financialEndYear, -2);

        // Get the last payment note ID for sequence
        $lastNote = PaymentNote::orderBy('id', 'desc')->first();
        $nextId = $lastNote ? $lastNote->id + 1 : 1;
        $formattedId = str_pad($nextId, 4, '0', STR_PAD_LEFT);

        return config('app.note_icon', 'W') . "/{$shortStartYear}-{$shortEndYear}/PN/{$formattedId}";
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'user_id' => 'required|exists:users,id',
                'green_note_id' => 'nullable|exists:green_notes,id',
                'reimbursement_note_id' => 'nullable|exists:reimbursement_notes,id',
                'net_payable_round_off' => 'required',
                'subject' => 'required|string',
                'note_no' => 'required|string',
                'recommendation_of_payment' => 'nullable|string',
                'add_particulars' => 'nullable|array',
                'add_particulars.*.particular' => 'nullable|string',
                'add_particulars.*.amount' => 'nullable|numeric',
                'less_particulars' => 'nullable|array',
                'less_particulars.*.particular' => 'nullable|string',
                'less_particulars.*.amount' => 'nullable|numeric',
            ]);

            $addParticulars = [];
            foreach ($request->add_particulars as $particular) {
                $addParticulars[] = [
                    'particular' => $particular['particular'],
                    'amount' => $particular['amount'],
                ];
            }

            $lessParticulars = [];
            foreach ($request->less_particulars as $particular) {
                $lessParticulars[] = [
                    'particular' => $particular['particular'],
                    'amount' => $particular['amount'],
                ];
            }

            $note = PaymentNote::create([
                'user_id' => $validated['user_id'],
                'green_note_id' => $validated['green_note_id'] ?? null,
                'reimbursement_note_id' => $validated['reimbursement_note_id'] ?? null,
                'net_payable_round_off' => $validated['net_payable_round_off'],
                'subject' => $validated['subject'],
                'note_no' => $validated['note_no'],
                'recommendation_of_payment' => $validated['recommendation_of_payment'],
                'add_particulars' => json_encode($addParticulars),
                'less_particulars' => json_encode($lessParticulars),
                'status' => 'D',
            ]);

            return redirect()->route('backend.payment-note.index')->with('success', 'Payment Note added successfully.');
        } catch (\Throwable $th) {
            return back()->with('error', 'Something went wrong.' . $th);
        }
    }

    function convertNumberToWords($number)
    {
        $words = [
            0 => 'Zero',
            1 => 'One',
            2 => 'Two',
            3 => 'Three',
            4 => 'Four',
            5 => 'Five',
            6 => 'Six',
            7 => 'Seven',
            8 => 'Eight',
            9 => 'Nine',
            10 => 'Ten',
            11 => 'Eleven',
            12 => 'Twelve',
            13 => 'Thirteen',
            14 => 'Fourteen',
            15 => 'Fifteen',
            16 => 'Sixteen',
            17 => 'Seventeen',
            18 => 'Eighteen',
            19 => 'Nineteen',
            20 => 'Twenty',
            30 => 'Thirty',
            40 => 'Forty',
            50 => 'Fifty',
            60 => 'Sixty',
            70 => 'Seventy',
            80 => 'Eighty',
            90 => 'Ninety',
        ];

        $suffixes = ['', 'Thousand', 'Lakh', 'Crore'];

        if ($number == 0) {
            return 'Zero';
        }

        $numberStr = (string) $number;
        $length = strlen($numberStr);

        $numArray = [];
        if ($length > 7) {
            // Handle Crores
            $numArray[] = substr($numberStr, 0, -7);
            $numberStr = substr($numberStr, -7);
        }
        if ($length > 5) {
            // Handle Lakhs
            $numArray[] = substr($numberStr, 0, -5);
            $numberStr = substr($numberStr, -5);
        }
        if ($length > 3) {
            // Handle Thousands
            $numArray[] = substr($numberStr, 0, -3);
            $numberStr = substr($numberStr, -3);
        }
        $numArray[] = $numberStr;

        $wordOutput = [];
        foreach ($numArray as $index => $part) {
            $num = (int) $part;
            if ($num == 0) {
                continue;
            }
            $wordOutput[] = $this->numberToWords($num, $words) . ' ' . $suffixes[count($numArray) - $index - 1];
        }

        return ucfirst(trim(implode(' ', $wordOutput)));
    }

    function numberToWords($num, $words)
    {
        if ($num < 21) {
            return $words[$num];
        } elseif ($num < 100) {
            return $words[10 * floor($num / 10)] . ($num % 10 == 0 ? '' : ' ' . $words[$num % 10]);
        } else {
            return $words[floor($num / 100)] . ' Hundred' . ($num % 100 == 0 ? '' : ' ' . $this->numberToWords($num % 100, $words));
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(PaymentNote $paymentNote)
    {
        $note = $paymentNote;

        if (!$note) {
            return abort(404, 'Payment Note not found');
        }
        $lessParticulars = json_decode($note->less_particulars, true) ?? [];
        $addParticulars = json_decode($note->add_particulars, true) ?? [];

        if ($note->greenNote) {
            $grossAmount = ($note->greenNote->invoice_base_value ?? 0) + ($note->greenNote->invoice_gst ?? 0) + ($note->greenNote->invoice_other_charges ?? 0);
        } elseif ($note->reimbursementNote) {
            $totalPayable = $note->reimbursementNote->expenses->sum('bill_amount') ?? 0;
            $advanceAdjusted = $note->reimbursementNote->adjusted ?? 0;
            $grossAmount = $totalPayable - $advanceAdjusted;
        } else {
            $grossAmount = 0;
        }

        $totalAdd = array_sum(array_column($addParticulars, 'amount'));
        $totalLess = array_sum(array_column($lessParticulars, 'amount'));
        $netPayable = $grossAmount + $totalAdd - $totalLess;
        $roundedNetPayable = round($netPayable);
        $netPayableWords = $this->convertNumberToWords($roundedNetPayable);
        $documents = collect();
        if ($note->green_note_id) {
            $documents = SupportingDoc::where('green_note_id', $note->greenNote->id)->get();
        }
        return view('backend.paymentNote.show', compact('note', 'lessParticulars', 'addParticulars', 'grossAmount', 'netPayable', 'roundedNetPayable', 'netPayableWords', 'documents'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PaymentNote $paymentNote)
    {
        $note = $paymentNote;

        if (!$note) {
            return abort(404, 'Payment Note not found');
        }
        $grossAmount = ($note->greenNote->invoice_base_value ?? 0) + ($note->greenNote->invoice_gst ?? 0) + ($note->greenNote->invoice_other_charges ?? 0);

        $lessParticulars = json_decode($note->less_particulars, true) ?? [];
        $addParticulars = json_decode($note->add_particulars, true) ?? [];
        $documents = collect();
        if ($note->green_note_id) {
            $documents = SupportingDoc::where('green_note_id', $note->greenNote->id)->get();
        }

        return view('backend.paymentNote.edit', compact('note', 'lessParticulars', 'addParticulars', 'grossAmount', 'documents'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PaymentNote $paymentNote)
    {
        $validated = $request->validate([
            'subject' => 'required|string',
            'note_no' => 'required|string',
            'recommendation_of_payment' => 'nullable|string',
            'status' => 'required|in:D,P,A,R,S',
            'net_payable_round_off' => 'nullable|string',
            'add_particulars' => 'nullable|array',
            'add_particulars.*.particular' => 'nullable|string',
            'add_particulars.*.amount' => 'nullable|numeric',
            'less_particulars' => 'nullable|array',
            'less_particulars.*.particular' => 'nullable|string',
            'less_particulars.*.amount' => 'nullable|numeric',
        ]);
        $getPaymentNote = $paymentNote;
        // dd($request->all());
        $addParticulars = [];
        if (!empty($request->add_particulars)) {
            foreach ($request->add_particulars as $particular) {
                if (!empty($particular['particular']) && isset($particular['amount'])) {
                    $addParticulars[] = [
                        'particular' => $particular['particular'],
                        'amount' => $particular['amount'],
                    ];
                }
            }
        }
        $lessParticulars = [];
        if (!empty($request->less_particulars)) {
            foreach ($request->less_particulars as $particular) {
                if (!empty($particular['particular']) && isset($particular['amount'])) {
                    $lessParticulars[] = [
                        'particular' => $particular['particular'],
                        'amount' => $particular['amount'],
                    ];
                }
            }
        }

        // Update the record
        $getPaymentNote->update([
            'subject' => $validated['subject'],
            'note_no' => $validated['note_no'],
            'net_payable_round_off' => $validated['net_payable_round_off'] ?? 0,
            'recommendation_of_payment' => $validated['recommendation_of_payment'],
            'add_particulars' => json_encode($addParticulars),
            'less_particulars' => json_encode($lessParticulars),
            'status' => $request->status,
        ]);

        if ($request->status == 'S') {
            $approvalStep = PaymentNoteApprovalStep::where('min_amount', '<=', $request->net_payable_round_off ?? 0)
                ->where(function ($query) use ($request) {
                    $query->where('max_amount', '>=', $request->net_payable_round_off ?? 0)->orWhereNull('max_amount');
                })
                ->first();
            if (!$approvalStep) {
                return redirect()->back()->with('success', 'Approval step 1 not found.');
            }
            // rule

            $approvalLevel = PaymentNoteApprovalPriority::where('approval_step_id', $approvalStep->id)->where('approver_level', 1)->first();

            $exists = PaymentNoteApprovalLog::where('payment_note_id', $getPaymentNote->id)
                ->where('priority_id', $approvalLevel->id)
                ->where('status', 'P')
                ->where('reviewer_id', auth()->id())
                ->exists();

            if ($exists) {
                return redirect()->route('backend.payment-note.index')->with('error', 'Approval log already submitted by you.');
            }
            $log = PaymentNoteApprovalLog::create([
                'payment_note_id' => $getPaymentNote->id,
                'priority_id' => $approvalLevel->id,
                'reviewer_id' => auth()->id(),
                'status' => 'P',
                'comments' => null,
            ]);
            if (!empty($approvalLevel)) {
                $log->priorities()->attach($approvalLevel->id);
            }

            if ($getPaymentNote->greenNote) {
                $name = optional(optional($getPaymentNote->greenNote)->vendor)->project ?? 'N/A';
                $supplier = optional(optional($getPaymentNote->greenNote)->supplier)->vendor_name ?? 'N/A';
            } elseif ($getPaymentNote->reimbursementNote) {
                $name = optional(optional($getPaymentNote->reimbursementNote)->project)->project ?? 'N/A';
                $supplier = optional(optional($getPaymentNote->reimbursementNote)->user)->name ?? 'N/A';
            } else {
                $name = 'N/A';
                $supplier = 'N/A';
            }
            $data = [
                'updated_by' => auth()->user()->email ?? 'System',
                'subject' => 'Payment Note for ' . $name . ' of Rs ' . ($getPaymentNote->net_payable_round_off ?? 0) . ' has been Generated',
                'approver_name' => optional($approvalLevel->user)->name ?? 'Approver',
                'maker' => optional($getPaymentNote->user)->name . ' has generated a Payment Note No. ' . $getPaymentNote->note_no . ' for ' . $name . ' of Rs ' . ($getPaymentNote->net_payable_round_off ?? 0) . ' of ' . $supplier . ' & due for your review.',
                'end' => 'Login to the panel for review & Approval/Rejection',
            ];

            // Mail::to('lamawav925@movfull.com')->send(new NoteStatusChangeMail($data));

            if (optional($approvalLevel->user)->email && $approvalLevel->user->email !== auth()->user()->email) {
                Mail::to($approvalLevel->user->email)->send(new NoteStatusChangeMail($data));
            }
        }

        return redirect()->route('backend.payment-note.index')->with('success', 'Payment Note updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PaymentNote $paymentNote)
    {
        try {
            $logs = PaymentNoteApprovalLog::where('payment_note_id', $paymentNote->id)->get();

            foreach ($logs as $log) {
                PaymentNoteLogPriority::where('payment_note_approval_log_id', $log->id)->delete();
            }

            PaymentNoteApprovalLog::where('payment_note_id', $paymentNote->id)->delete();

            $paymentNote->delete();

            return redirect()->route('backend.payment-note.index')->with('success', 'Payment Note deleted successfully.');
        } catch (\Exception $e) {
            return redirect()
                ->route('backend.payment-note.index')
                ->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }
    public function downloadGreenNotePdf($noteId)
    {
        // Retrieve the note from the database
        $note = PaymentNote::findOrFail($noteId);
        $lessParticulars = json_decode($note->less_particulars, true) ?? [];
        $addParticulars = json_decode($note->add_particulars, true) ?? [];

        if ($note->greenNote) {
            $grossAmount = ($note->greenNote->invoice_base_value ?? 0) + ($note->greenNote->invoice_gst ?? 0) + ($note->greenNote->invoice_other_charges ?? 0);
        } elseif ($note->reimbursementNote) {
            $totalPayable = $note->reimbursementNote->expenses->sum('bill_amount') ?? 0;
            $advanceAdjusted = $note->reimbursementNote->adjusted ?? 0;
            $grossAmount = $totalPayable - $advanceAdjusted;
        } else {
            $grossAmount = 0;
        }
        $totalAdd = array_sum(array_column($addParticulars, 'amount'));
        $totalLess = array_sum(array_column($lessParticulars, 'amount'));
        $netPayable = $grossAmount + $totalAdd - $totalLess;
        $roundedNetPayable = round($netPayable);
        $netPayableWords = $this->convertNumberToWords($roundedNetPayable);
        // Pass the note to the view and generate PDF
        $pdf = Pdf::loadView('backend.paymentNote.partials.payment-download', [
            'note' => $note,
            'grossAmount' => $grossAmount,
            'lessParticulars' => $lessParticulars,
            'addParticulars' => $addParticulars,
            'netPayable' => $netPayable,
            'roundedNetPayable' => $roundedNetPayable,
            'netPayableWords' => $netPayableWords,
        ]);
        $pdf->set_option('isHtml5ParserEnabled', true);
        $pdf->set_option('isPhpEnabled', true);
        $pdf->set_option('isRemoteEnabled', true);
        return $pdf->download('payment_note_' . $note->order_no . '.pdf');
    }
    public function rule()
    {
        $users = \App\Services\RoleService::getUsersWithRoles(['PN Approver', 'approver', 'reviewer'])
            ->where('active', 'Y');
        // $approvalSteps = PaymentNoteApprovalStep::with('reviewers')->get();
        $approvalSteps = PaymentNoteApprovalStep::with('approvers.user')->get();
        return view('backend.paymentNote.rule.create', compact('approvalSteps', 'users'));
    }
    public function updateUtr(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'note_id' => 'required|exists:payment_notes,id',
            'utr_no' => 'required',
            'utr_date' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    'success' => false,
                    'errors' => $validator->errors(),
                ],
                422,
            );
        }

        $note = PaymentNote::find($request->note_id);
        // table
        $lessParticulars = json_decode($note->less_particulars, true) ?? [];
        $addParticulars = json_decode($note->add_particulars, true) ?? [];

        if ($note->greenNote) {
            $grossAmount = ($note->greenNote->invoice_base_value ?? 0) + ($note->greenNote->invoice_gst ?? 0) + ($note->greenNote->invoice_other_charges ?? 0);
        } elseif ($note->reimbursementNote) {
            $totalPayable = $note->reimbursementNote->expenses->sum('bill_amount') ?? 0;
            $advanceAdjusted = $note->reimbursementNote->adjusted ?? 0;
            $grossAmount = $totalPayable - $advanceAdjusted;
        } else {
            $grossAmount = 0;
        }

        $totalAdd = array_sum(array_column($addParticulars, 'amount'));
        $totalLess = array_sum(array_column($lessParticulars, 'amount'));
        $netPayable = $grossAmount + $totalAdd - $totalLess;
        $roundedNetPayable = round($netPayable);
        $netPayableWords = $this->convertNumberToWords($roundedNetPayable);
        // table

        $ccEmails = [];

        if ($note->greenNote) {
            $supplierEmail = $note->greenNote->supplier->vendor_email ?? null;
            $supplierName = $note->greenNote->supplier->vendor_name ?? 'N/A';
            $invoiceNo = $note->greenNote->invoice_number ?? 'N/A';
            $invoiceDate = $note->greenNote->invoice_date ?? 'N/A';

            $greenUserEmail = $note->greenNote->user->email ?? null;
            if (filter_var($greenUserEmail, FILTER_VALIDATE_EMAIL)) {
                $ccEmails[] = $greenUserEmail;
            }

            $noteUserEmail = $note->user->email ?? null;
            if (filter_var($noteUserEmail, FILTER_VALIDATE_EMAIL)) {
                $ccEmails[] = $noteUserEmail;
            }
        } elseif ($note->reimbursementNote) {
            $supplierEmail = $note->reimbursementNote->user->email ?? null;
            $supplierName = $note->reimbursementNote->user->name ?? 'N/A';
            $invoiceNo = $note->reimbursementNote->note_no ?? 'N/A';
            $invoiceDate = null;
            $ccEmails = [];
        } else {
            $supplierEmail = 'N/A';
            $supplierName = 'N/A';
            $invoiceNo = 'N/A';
            $invoiceDate = 'N/A';
            $ccEmails = [];
        }
        $data = [
            'supplier_name' => $supplierName ?? 'N/A',
            'invoice_no' => $invoiceNo ?? 'N/A',
            'invoice_date' => \Carbon\Carbon::parse($invoiceDate)->format('d-m-Y'),
            'entity_name' => config('app.short_name'),
            'amount_paid' => $note->net_payable_round_off,
            'payment_date' => \Carbon\Carbon::parse($request->utr_date)->format('d-m-Y'),
            'utr_no' => $request->utr_no,
            'lessParticulars' => $lessParticulars,
            'addParticulars' => $addParticulars,
            'grossAmount' => $grossAmount,
            'totalAdd' => $totalAdd,
            'totalLess' => $totalLess,
            'netPayable' => $netPayable,
            'roundedNetPayable' => $roundedNetPayable,
            'netPayableWords' => $netPayableWords,
        ];

        // $toEmail = 'wateped405@eduhed.com';

        $toEmail = $supplierEmail;

        // Validate email format
        if ($toEmail) {
            Mail::to($toEmail)->cc($ccEmails)->send(new PaymentEmail($data, $ccEmails, $note));
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Email not sent. Supplier email is missing or invalid. Please add a valid email in the vendor details.',
            ]);
        }
        // dd($ccEmails, $toEmail, $supplierEmail, $request->all(),$note->greenNote->supplier);

        // if ($toEmail) {
        //     Mail::to($toEmail)->send(new PaymentEmail($data, $ccEmails, $note));
        // }
        if ($note) {
            $note->update([
                'utr_no' => $request->utr_no,
                'utr_date' => $request->utr_date,
                'status' => 'PD',
            ]);

            if ($note->greenNote) {
                $note->greenNote->update(['status' => 'PD']);
            }

            if ($note->reimbursementNote) {
                $note->reimbursementNote->update(['status' => 'PD']);
            }
        }

        return response()->json(['success' => true]);
    }

    /**
     * Show draft payment notes
     */
    public function drafts()
    {
        $authId = Auth::id();
        
        $query = PaymentNote::where('is_draft', true)
            ->with(['greenNote', 'reimbursementNote', 'user', 'createdBy']);

        // Apply role-based filtering
        if ($authId !== 1 && !auth()->user()->can('all-payment-note')) {
            $query->where(function ($q) use ($authId) {
                $q->where('user_id', $authId)
                  ->orWhere('created_by', $authId);
            });
        }

        $drafts = $query->orderBy('created_at', 'desc')->get();

        return view('backend.payment-note.drafts', compact('drafts'));
    }

    /**
     * Convert draft to active payment note
     */
    public function convertDraftToActive(PaymentNote $paymentNote)
    {
        if (!$paymentNote->isDraft()) {
            return back()->withErrors(['error' => 'Payment note is not in draft status.']);
        }

        try {
            $paymentNoteService = new \App\Services\PaymentNoteService();
            $paymentNoteService->convertDraftToActive($paymentNote, auth()->user());

            return redirect()
                ->route('backend.payment-note.show', $paymentNote)
                ->with('success', 'Draft payment note converted to active successfully.');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to convert draft: ' . $e->getMessage()]);
        }
    }

    /**
     * Delete draft payment note
     */
    public function deleteDraft(PaymentNote $paymentNote)
    {
        if (!$paymentNote->isDraft()) {
            return back()->withErrors(['error' => 'Only draft payment notes can be deleted.']);
        }

        // Check permissions
        if (!auth()->user()->can('delete-payment-note') && 
            $paymentNote->created_by !== auth()->id() && 
            $paymentNote->user_id !== auth()->id()) {
            return back()->withErrors(['error' => 'You do not have permission to delete this draft.']);
        }

        try {
            $paymentNote->delete();

            return redirect()
                ->route('backend.payment-note.drafts')
                ->with('success', 'Draft payment note deleted successfully.');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to delete draft: ' . $e->getMessage()]);
        }
    }

    /**
     * Put payment note on hold
     */
    public function putOnHold(Request $request, PaymentNote $paymentNote)
    {
        $request->validate([
            'hold_reason' => 'required|string|max:1000',
        ]);

        try {
            $paymentNoteService = new \App\Services\PaymentNoteService();
            $paymentNoteService->putOnHold($paymentNote, $request->hold_reason, auth()->user());

            return back()->with('success', 'Payment note put on hold successfully.');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to put note on hold: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove payment note from hold
     */
    public function removeFromHold(Request $request, PaymentNote $paymentNote)
    {
        $request->validate([
            'new_status' => 'required|in:P,D,S',
        ]);

        try {
            $paymentNoteService = new \App\Services\PaymentNoteService();
            $paymentNoteService->removeFromHold($paymentNote, auth()->user(), $request->new_status);

            return back()->with('success', 'Payment note removed from hold successfully.');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to remove note from hold: ' . $e->getMessage()]);
        }
    }

    /**
     * Show payment note creation form for superadmin
     */
    public function createForSuperAdmin()
    {
        // Only allow superadmin to access this
        if (!auth()->user()->hasRole('Super Admin')) {
            abort(403, 'Unauthorized access.');
        }

        $greenNotes = GreenNote::where('status', 'A')
            ->whereDoesntHave('paymentNotes', function ($query) {
                $query->where('is_draft', false);
            })
            ->with(['supplier', 'department'])
            ->get();

        return view('backend.payment-note.create-superadmin', compact('greenNotes'));
    }

    /**
     * Store payment note created by superadmin
     */
    public function storeForSuperAdmin(Request $request)
    {
        // Only allow superadmin to access this
        if (!auth()->user()->hasRole('Super Admin')) {
            abort(403, 'Unauthorized access.');
        }

        $request->validate([
            'green_note_id' => 'required|exists:green_notes,id',
            'subject' => 'required|string|max:1000',
            'recommendation_of_payment' => 'required|string|max:1000',
            'is_draft' => 'boolean',
        ]);

        try {
            $greenNote = GreenNote::findOrFail($request->green_note_id);
            
            if ($request->is_draft) {
                $paymentNoteService = new \App\Services\PaymentNoteService();
                $paymentNote = $paymentNoteService->createDraftOnApproval($greenNote, auth()->user());
            } else {
                $paymentNote = PaymentNote::create([
                    'green_note_id' => $request->green_note_id,
                    'user_id' => auth()->id(),
                    'created_by' => auth()->id(),
                    'note_no' => PaymentNote::generateOrderNumber(),
                    'subject' => $request->subject,
                    'recommendation_of_payment' => $request->recommendation_of_payment,
                    'status' => 'P',
                    'is_draft' => false,
                    'auto_created' => false,
                ]);
            }

            return redirect()
                ->route('backend.payment-note.show', $paymentNote)
                ->with('success', 'Payment note created successfully.');

        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->withErrors(['error' => 'Failed to create payment note: ' . $e->getMessage()]);
        }
    }
}