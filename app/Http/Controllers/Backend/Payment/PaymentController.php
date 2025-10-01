<?php

namespace App\Http\Controllers\Backend\Payment;

use App\Http\Controllers\Controller;
use App\Imports\PaymentsImport;
use App\Models\Payment;
use App\Models\PaymentsShortcut;
use App\Models\Vendor;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Excel;
use Yajra\DataTables\Facades\DataTables;
use App\Helpers\Helper;
use App\Mail\NoteStatusChangeMail;
use App\Models\Account;
use App\Models\BankLetterApprovalLog;
use App\Models\BankLetterApprovalPriority;
use App\Models\BankLetterApprovalStep;
use App\Models\PaymentNote;
use Brian2694\Toastr\Toastr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class PaymentController extends Controller
{
    public $helper;
    protected $toastOptions;
    /**
     * Instantiate a new UserController instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:create-payment|edit-payment|delete-payment', ['only' => ['index', 'show']]);
        $this->middleware('permission:create-payment', ['only' => ['create', 'store']]);
        $this->middleware('permission:edit-payment', ['only' => ['edit', 'update']]);
        $this->middleware('permission:delete-payment', ['only' => ['destroy']]);
        $this->middleware('permission:import-payment-excel', ['only' => ['import', 'importStore']]);
        $this->toastOptions = [
            'closeButton' => true,
            'debug' => false,
            'newestOnTop' => true,
            'progressBar' => true,
            'positionClass' => 'toast-top-right',
            'preventDuplicates' => true,
            'onclick' => null,
            'showDuration' => '300',
            'hideDuration' => '1000',
            'timeOut' => '5000',
            'extendedTimeOut' => '1000',
            'showEasing' => 'swing',
            'hideEasing' => 'linear',
            'showMethod' => 'fadeIn',
            'hideMethod' => 'fadeOut',
        ];

        $this->helper = new Helper();
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        DB::connection()->enableQueryLog();
        $userId = Auth::id(); // get logged-in user ID
        $userRoles = Auth::user()->getRoleNames();
        $status = $request->status ?? 'S';
        if ($userId == 1 || $userRoles->contains('PN User') || $userRoles->contains('PN Approver')) {
            $sl_no_filter = Payment::select('sl_no')->groupBy('sl_no')->get();
            if ($request->ajax()) {
                // $data = Payment::groupBy('sl_no')
                //     ->when($request->filled('status'), function ($query) use ($request) {
                //         $query->where('status', $request->status);
                //     })
                //     ->orderBy('id', 'desc')
                //     ->get();
                $data = Payment::groupBy('sl_no')
                    ->when($status !== 'all', function ($query) use ($status) {
                        $query->where('status', $status);
                    })
                    ->orderBy('id', 'desc')
                    ->get();

                return DataTables::of($data)
                    // ->removeColumn('id')
                    ->addIndexColumn()
                    ->addColumn('vendor_name', function ($row) {
                        return $row->name_of_beneficiary ?? '-';
                    })
                    ->addColumn('amount', function ($row) {
                        $amount = optional($row->paymentNote)->net_payable_round_off;
                        return $amount !== null ? Helper::formatIndianNumber($amount) : '-';
                    })

                    ->addColumn('action', function ($row) {
                        // \Log::info($row);

                        $btn = '';

                        if (Auth::id() == 1) {
                            $btn =
                                '<form action="' .
                                route('backend.payments.destroy', $row->id) .
                                '" method="post" style="display:inline;">
                            ' .
                                csrf_field() .
                                '
                            <input type="hidden" name="_method" value="DELETE">
                            <input type="hidden" name="sl_no" value="' .
                                $row->sl_no .
                                '">
                            <button type="submit" class="btn btn-outline-danger btn-xs" onclick="return confirm(\'Do you want to delete this role?\');"><i class="bi bi-trash"></i></button>
                        </form>
                         <a href="' .
                                route('backend.payments.editPaymentRequest', $row->sl_no) .
                                '" class="btn btn-outline-primary btn-xs">
                                    <i class="bi bi-pencil-square"></i>
                                </a>';
                        } elseif ($row->user_id == Auth::id() && $row->status == 'D') {
                            $btn .=
                                ' <a href="' .
                                route('backend.payments.editPaymentRequest', $row->sl_no) .
                                '" class="btn btn-outline-primary btn-xs">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>';
                        }
                        $btn .=
                            ' <form action="' .
                            route('backend.templates.templateCommon', $row->template_type) .
                            '" method="post" style="display:inline;"> ' .
                            csrf_field() .
                            ' <input type="hidden" name="slno" value="' .
                            $row->sl_no .
                            '"> <button type="submit" class="btn btn-outline-info btn-xs" onclick="return confirm(\'Do you want to preview/generate PDF??\');">
                            <i class="bi bi-eye"></i></button> </form>';

                        return $btn;
                    })
                    ->addColumn('shortcut_name', function ($row) {
                        $btn = '';
                        $paymentsShortcut = PaymentsShortcut::where('sl_no', $row->sl_no)->first();
                        if ($paymentsShortcut) {
                            $btn .=
                                $paymentsShortcut->shortcut_name .
                                ' <a href="' .
                                route('backend.payments.shortcut', $paymentsShortcut->id) .
                                '" class="btn btn-outline-info btn-xs" style="line-height: 1.5;">
                            <i class="bi bi-share"></i>
                        </a>';
                            // $btn .=
                            //     '    <a href="' .
                            //     route('backend.payments.shortcut', $paymentsShortcut->id) .
                            //     '" class="btn btn-outline-info btn-xs">
                            //     <i class="bi bi-share"></i><br> '.$paymentsShortcut->shortcut_name.'
                            // </a>';
                        }
                        return $btn;
                    })
                    ->addColumn('status', function ($row) {
                        $statusLabels = [
                            'D' => '<span class="badge bg-dark">Draft</span>',
                            'P' => '<span class="badge bg-warning">Pending</span>',
                            'A' => '<span class="badge bg-success">Approved</span>',
                            'R' => '<span class="badge bg-danger">Rejected</span>',
                            'S' => '<span class="badge bg-secondary">Sent for Approval</span>',
                            'PD' => '<span class="badge bg-secondary">Paid</span>',
                            '' => '<span class="badge bg-secondary">N/A</span>',
                        ];
                        $statusHtml = $statusLabels[$row->status] ?? $row->status;

                        // Now get the Next Approver
                        $approvers = BankLetterApprovalLog::with('logPriorities.priority.user')->where('sl_no', $row->sl_no)->get();

                        $nextApproverHtml = '';
                        if ($row->status === 'PD' && $row->utr_date) {
                            $formattedUtrDate = \Carbon\Carbon::parse($row->utr_date)->format('d-m-Y');
                            $statusHtml .= '<div class="mt-1 small text-muted">on ' . $formattedUtrDate . '</div>';
                        }
                        if ($approvers->last()?->logPriorities->last()?->priority) {
                            $nextApproverHtml .= '<div class="mt-2 text-start">';
                            $nextApproverHtml .= '<strong>Next Approver:</strong> ';
                            foreach ($approvers->last()->logPriorities as $log) {
                                $nextApproverHtml .= $log->priority->user->name . ', ';
                            }
                            $nextApproverHtml = rtrim($nextApproverHtml, ', ');
                            $nextApproverHtml .= '</div>';
                        }

                        return $statusHtml . $nextApproverHtml;
                    })
                    ->rawColumns(['action', 'shortcut_name', 'status'])
                    ->make(true);
            }
        } else {
            $fromLogs = DB::table('bank_letter_log_priority')->join('bank_letter_approval_logs', 'bank_letter_log_priority.bank_letter_approval_log_id', '=', 'bank_letter_approval_logs.id')->join('bank_letter_approval_priorities', 'bank_letter_log_priority.priority_id', '=', 'bank_letter_approval_priorities.id')->where('bank_letter_approval_priorities.reviewer_id', $userId)->pluck('bank_letter_approval_logs.sl_no')->toArray();

            // Get sl_no from payments where user_id = user
            $fromPayments = DB::table('payments_new')->where('user_id', $userId)->pluck('sl_no')->toArray();

            // Merge and get unique sl_no
            $allSlNos = array_unique(array_merge($fromLogs, $fromPayments));

            // Final filtered list from Payments
            $sl_no_filter = Payment::whereIn('sl_no', $allSlNos)->select('sl_no')->groupBy('sl_no')->get();

            if ($request->ajax()) {
                // $data = Payment::groupBy('sl_no')->orderBy('id', 'desc')->get();
                $data = Payment::whereIn('sl_no', $allSlNos)
                    ->when($request->filled('status'), function ($query) use ($request) {
                        $query->where('status', $request->status);
                    })
                    ->groupBy('sl_no')
                    ->orderBy('id', 'desc')
                    ->get();

                return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('vendor_name', function ($row) {
                        return optional($row->greenNote->vendor)->vendor_name ?? '-';
                    })
                    ->addColumn('action', function ($row) {
                        $btn = '';

                        $btn .=
                            ' <form action="' .
                            route('backend.templates.templateCommon', $row->template_type) .
                            '" method="post" style="display:inline;"> ' .
                            csrf_field() .
                            ' <input type="hidden" name="slno" value="' .
                            $row->sl_no .
                            '"> <button type="submit" class="btn btn-outline-info btn-xs" onclick="return confirm(\'Do you want to preview/generate PDF??\');">
                            <i class="bi bi-eye"></i></button> </form>';

                        if ($row->user_id == Auth::id() && $row->status == 'D') {
                            $btn .=
                                ' <a href="' .
                                route('backend.payments.editPaymentRequest', $row->sl_no) .
                                '" class="btn btn-outline-primary btn-xs">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>';
                        }
                        return $btn;
                    })
                    ->addColumn('shortcut_name', function ($row) {
                        $btn = '';
                        $paymentsShortcut = PaymentsShortcut::where('sl_no', $row->sl_no)->first();
                        if ($paymentsShortcut) {
                            $btn .=
                                $paymentsShortcut->shortcut_name .
                                ' <a href="' .
                                route('backend.payments.shortcut', $paymentsShortcut->id) .
                                '" class="btn btn-outline-info btn-xs" style="line-height: 1.5;">
                            <i class="bi bi-share"></i>
                        </a>';
                        }
                        return $btn;
                    })
                    ->addColumn('status', function ($row) {
                        $statusLabels = [
                            'D' => '<span class="badge bg-dark">Draft</span>',
                            'P' => '<span class="badge bg-warning">Pending</span>',
                            'A' => '<span class="badge bg-success">Approved</span>',
                            'R' => '<span class="badge bg-danger">Rejected</span>',
                            'S' => '<span class="badge bg-secondary">Sent for Approval</span>',
                            '' => '<span class="badge bg-secondary">N/A</span>',
                        ];
                        // return $statusLabels[$row->status] ?? $row->status;
                        $statusHtml = $statusLabels[$row->status] ?? $row->status;

                        // Now get the Next Approver
                        $approvers = BankLetterApprovalLog::with('logPriorities.priority.user')->where('sl_no', $row->sl_no)->get();

                        $nextApproverHtml = '';

                        if ($approvers->last()?->logPriorities->last()?->priority) {
                            $nextApproverHtml .= '<div class="mt-2 text-start">';
                            $nextApproverHtml .= '<strong>Next Approver:</strong> ';
                            foreach ($approvers->last()->logPriorities as $log) {
                                $nextApproverHtml .= $log->priority->user->name . ', ';
                            }
                            $nextApproverHtml = rtrim($nextApproverHtml, ', ');
                            $nextApproverHtml .= '</div>';
                        }

                        return $statusHtml . $nextApproverHtml;
                    })
                    ->rawColumns(['action', 'shortcut_name', 'status'])
                    ->make(true);
            }
        }
        $queries = DB::getQueryLog();
        $last_query = end($queries);

        return view('backend.payment.index', compact('sl_no_filter'));
    }

    /**
     * Display a listing of the resource.
     */
    public function getPayments(Request $request)
    {
        $records = Payment::query();
        $draw = $request->get('draw');
        $start = $request->get('start');
        $rowperpage = $request->get('length'); // total number of rows per page

        $columnIndex_arr = $request->get('order');
        $columnName_arr = $request->get('columns');
        $order_arr = $request->get('order');
        $search_arr = $request->get('search');

        $columnIndex = $columnIndex_arr[0]['column']; // Column index
        $columnName = $columnName_arr[$columnIndex]['data']; // Column name
        $columnSortOrder = $order_arr[0]['dir']; // asc or desc
        $searchValue = $search_arr['value']; // Search value

        // Total records
        $totalRecords = $records->select('count(*) as allcount')->count();
        // $totalRecordswithFilter = Payment::select('count(*) as allcount')->where('name', 'like', '%' . $searchValue . '%')->count();
        // dd($totalRecords, $totalRecordswithFilter);

        if (!empty($searchValue)) {
            $filter = ['sl_no', 'ref_no', 'date', 'project', 'amount'];
            $records->where(function ($bids) use ($filter, $searchValue) {
                foreach ($filter as $key => $column) {
                    if ($key === 0) {
                        $bids->where($column, 'like', '%' . $searchValue . '%');
                    } else {
                        $bids->orWhere($column, 'like', '%' . $searchValue . '%');
                    }
                }
            });
        }

        // Get records, also we have included search filter as well
        $records = Payment::orderBy($columnName, $columnSortOrder)->select('id', 'sl_no', 'ref_no', 'date', 'project', 'amount')->skip($start)->take($rowperpage)->get();

        $data_arr = [];

        $data_arr = [];
        if ($request->ajax()) {
            foreach ($records as $index => $record) {
                $attributes = $record->getAttributes();
                $attributes = array_map(function ($value) {
                    return $value === null ? 'N/A' : $value; // Replace null values
                }, $attributes);
                $attributes['DT_RowIndex'] = $index + 1; // Add DT_RowIndex manually
                $editUrl = route('backend.payments.edit', $attributes['id']);
                $actionBtn = '<a href="javascript:;" data-id="' . $attributes['id'] . '" class="badge black winbid" lang="' . $attributes['id'] . '">Win</a>';

                $attributes['action'] = $actionBtn;

                $data_arr[] = $attributes;
            }
            $response = [
                'draw' => intval($draw),
                'iTotalRecords' => $totalRecords,
                'iTotalDisplayRecords' => $totalRecords,
                'aaData' => $data_arr,
            ];
            return $response;
        }
        /* foreach ($records as $record) {

            $data_arr[] = array(
                "id" => $record->id,
                "name" => $record->name,
                "email" => $record->email,
                "mobile" => $record->mobile,
                "branch" => $record->branch,
            );
        }

        $response = array(
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecords,
            "iTotalDisplayRecords" => $totalRecordswithFilter,
            "aaData" => $data_arr,
        ); */

        // echo json_encode($response);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        return view('backend.payment.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        $uid = $user->id;
        $cart = Cache::get('cart_' . $uid);

        if (!empty($request->payment_note_id) && is_array($request->payment_note_id)) {
            $paymentNoteIds = array_filter(array_map('intval', $request->payment_note_id), fn($id) => $id > 0);

            if (!empty($paymentNoteIds)) {
                PaymentNote::whereIn('id', $paymentNoteIds)->update(['status' => 'B']);
                $notes = PaymentNote::with(['greenNote', 'reimbursementNote'])
                    ->whereIn('id', $paymentNoteIds)
                    ->get();

                foreach ($notes as $note) {
                    if ($note->greenNote) {
                        $note->greenNote->status = 'B';
                        $note->greenNote->save();
                    }

                    if ($note->reimbursementNote) {
                        $note->reimbursementNote->status = 'B';
                        $note->reimbursementNote->save();
                    }
                }
            }
        }

        /*
         dd("==", $request->all(), $cart); */
        // if ($request->has('template_type') && $request->template_type == 'mf-rtgs') {

        try {
            // 40619717495
            /* $validator = Validator::make($request->all(), [
                'full_account_number' => 'required|unique:payments'
            ]);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator);
            } */

            if ($request->has('vendor') && !empty($request->vendor) && count($request->vendor) > 0) {
                foreach (array_chunk($request->vendor, 12) as $i => $vendor) {
                    // array_chunk() will divide $arr_a into smaller array as [[1, 2, 3..., 500],[501, 502, .... , 1000] and so one till 5000]
                    //do some stuff with $x;
                    // $sl_no = $this->helper->generateRandomNumber();
                    $sl_no = $this->generateSerialNumber();
                    if ($request->input('shortcut') === 'on') {
                        // $sl_no = $this->helper->generateRandomNumber();
                        $sl_no = $this->generateSerialNumber();
                        $paymentsShortcut = new PaymentsShortcut();
                        $paymentsShortcut->sl_no = $sl_no;
                        $paymentsShortcut->shortcut_name = $request->shortcut_name ? $request->shortcut_name : 'Shortcut ' . rand();
                        $paymentsShortcut->request_data = json_encode($request->input('vendor'));
                        $paymentsShortcut->save();
                    }
                    foreach ($vendor as $j => $ven) {
                        // $sl_no = $this->helper->generateRandomNumber();
                        $ven['sl_no'] = $sl_no;
                        $payment = new Payment();
                        $payment->sl_no = $sl_no;
                        $payment->template_type = $ven['template_type'] ?? null;
                        $payment->project = $ven['project'] ?? null;
                        $payment->account_full_name = isset($ven['account_full_name']) ? $ven['account_full_name'] : null;
                        $payment->from_account_type = $ven['from_account_type'] ?? null;
                        $payment->full_account_number = $ven['full_account_number'] ?? null;
                        $payment->to = $ven['to_account_type'] ?? null;
                        $payment->to_account_type = isset($ven['to']) ? $ven['to'] : null;
                        $payment->name_of_beneficiary = $ven['benificiary_name'] ?? null;
                        $payment->account_number = $ven['account_number'] ?? null;
                        $payment->name_of_bank = $ven['name_of_bank'] ?? null;
                        $payment->ifsc_code = $ven['ifsc_code'] ?? null;
                        $payment->amount = $ven['amount'] ?? null;
                        $payment->purpose = $ven['purpose'] ?? null;
                        $payment->status = 'D';
                        $payment->user_id = Auth::id();
                        $payment->payment_note_id = $request->payment_note_id[$j] ?? null;
                        if (!$payment->save()) {
                            $message = 'Request not created from A/C: ' . $ven['account_full_name'] . ' to ' . $ven['benificiary_name'] . ' with SL No.:' . $sl_no . ' with template ' . $ven['template_type'];
                            throw new \Exception($message);
                            toastr()->error($message, 'Error', $this->toastOptions);
                        }
                        $message = 'Request created created for A/C: ' . $ven['account_full_name'] . ' to ' . $ven['benificiary_name'] . ' with SL No.:' . $sl_no . ' with template ' . $ven['template_type'];
                        activity('Request created created with SL No.: ' . $sl_no)
                            ->performedOn($user) // Entry add in table. model name(subject_type) & id(subject_id)
                            ->causedBy($user) //causer_id = admin id, causer type = admin model
                            ->event(__METHOD__)
                            ->withProperties($ven)
                            ->log($message . " [$user->email]");
                    }
                }
                // dd($request->all(), "===");
            } else {
                $message = 'No template selection found!: ';
                throw new \Exception($message);
            }

            Cache::forget('cart_' . $uid);
            // return redirect()->back()->with('success', 'Request created successfully.');
            return redirect()->route('backend.payments.index')->with('success', 'Request created successfully.');
        } catch (\Throwable $th) {
            // dd($th);
            //throw $th;
            if ($th instanceof ModelNotFoundException) {
                $message = $th->getMessage();
                $errors = ['QueryException', $message];
            } elseif ($th instanceof QueryException) {
                $message = $th->getMessage();
                $errors = ['QueryException', $message];
            } else {
                $message = $th->getMessage();
                $errors = ['Exception', $th->getMessage()];
            }
            activity($message . " [$user->email]")
                ->performedOn($user) // Entry add in table. model name(subject_type) & id(subject_id)
                ->causedBy($user) //causer_id = admin id, causer type = admin model
                ->event(__METHOD__)
                ->withProperties([])
                ->log($message);
            toastr()->error($message);
            return redirect()->back()->withError($message);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Payment $payment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Payment $payment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function editPaymentRequest(Request $request, $slno)
    {
        //
        // $result = Payment::where('sl_no', $slno)->get();
        $result = Payment::with('paymentNote')->where('sl_no', $slno)->get();
        // dd("===", $slno, $result, $result->toArray());
        // return view('backend.payment.editPaymentRequestForm', ['cartItems' => $result->toArray(), 'slno' => $slno]);
        return view('backend.payment.editPaymentRequestForm', ['cartItems' => $result, 'slno' => $slno]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function updatePaymentRequest(Request $request, $slno)
    {
        $request->validate(
            [
                'status' => 'required|in:D,S',
                'type' => 'required|in:I,E',
            ],
            [
                'status.required' => 'Status is required.',
                'status.in' => 'Invalid status selected.',
                'type.required' => 'Type is required.',
                'type.in' => 'Invalid type selected.',
            ],
        );

        $dataCheckAmount = Payment::where('sl_no', $slno)->latest()->take(12)->orderBy('id', 'asc')->get();
        $payment = Payment::where('sl_no', $slno)->first();

        if ($request->status == 'S') {
            if ($request->type == 'I') {
                $totalAmount = $dataCheckAmount->sum('amount');
                $approvalStep = BankLetterApprovalStep::where('min_amount', '<=', 0)->where('max_amount', 0)->first();
                // dd($request->all(), $approvalStep);
                if (!$approvalStep) {
                    return redirect()->back()->with('success', 'Approval step 1 not found.');
                }

                $approvalLevel = BankLetterApprovalPriority::where('approval_step_id', $approvalStep->id)->where('approver_level', 1)->first();
                $allApprovalStep = BankLetterApprovalPriority::where('approval_step_id', $approvalStep->id)->where('approver_level', 1)->pluck('id')->toArray();
                // dd($approvalStep, $approvalLevel);
                $exists = BankLetterApprovalLog::where('sl_no', $slno)
                    ->where('priority_id', $approvalLevel->id)
                    ->where('reviewer_id', auth()->id())
                    ->where('status', 'A')
                    ->exists();

                if ($exists) {
                    return redirect()
                        ->back()
                        ->withErrors(['duplicate' => 'Approval log already submitted by you.'])
                        ->withInput();
                }
                $allApprovalStepEmail = BankLetterApprovalPriority::where('approval_step_id', $approvalStep->id)->where('approver_level', 1)->get();
                $log = BankLetterApprovalLog::create([
                    'sl_no' => $slno,
                    'priority_id' => $approvalLevel->id,
                    'reviewer_id' => auth()->id(),
                    'status' => 'A',
                    'comments' => null,
                ]);
                if (!empty($allApprovalStep)) {
                    $log->priorities()->attach($allApprovalStep);
                }
            } else {
                // $dataCheckAmount = Payment::where('sl_no', $slno)
                //     ->latest()->take(12)->orderBy('id', 'asc')->get();

                $totalAmount = $dataCheckAmount->sum('amount');
                $approvalStep = BankLetterApprovalStep::where('min_amount', '<=', $totalAmount ?? 0)
                    ->where(function ($query) use ($totalAmount) {
                        $query->where('max_amount', '>=', $totalAmount ?? 0)->orWhereNull('max_amount');
                    })
                    ->first();
                if (!$approvalStep) {
                    return redirect()->back()->with('success', 'Approval step 1 not found.');
                }
                $approvalLevel = BankLetterApprovalPriority::where('approval_step_id', $approvalStep->id)->where('approver_level', 1)->first();
                // dd($approvalStep, $approvalLevel, $totalAmount);
                $allApprovalStep = BankLetterApprovalPriority::where('approval_step_id', $approvalStep->id)->where('approver_level', 1)->pluck('id')->toArray();
                $allApprovalStepEmail = BankLetterApprovalPriority::where('approval_step_id', $approvalStep->id)->where('approver_level', 1)->get();

                $exists = BankLetterApprovalLog::where('sl_no', $slno)
                    ->where('priority_id', $approvalLevel->id)
                    ->where('reviewer_id', auth()->id())
                    ->where('status', 'A')
                    ->exists();

                if ($exists) {
                    return redirect()
                        ->back()
                        ->withErrors(['duplicate' => 'Approval log already submitted by you.'])
                        ->withInput();
                }

                $log = BankLetterApprovalLog::create([
                    'sl_no' => $slno,
                    'priority_id' => $approvalLevel->id,
                    'reviewer_id' => auth()->id(),
                    'status' => 'A',
                    'comments' => null,
                ]);
                if (!empty($allApprovalStep)) {
                    $log->priorities()->attach($allApprovalStep);
                }
                // dd($approvalStep, $approvalLevel);
            }

            $data = [
                'updated_by' => auth()->user()->email,
                'subject' => ' Subject: Bank RTSG / NEFT Letter of Rs ' . $totalAmount . ' has been Generated',
                'approver_name' => $approvalStepCurrent->user->name ?? 'Approver',
                'maker' => auth()->user()->email . ' has generated a Bank RTGS / NEFT letter No. ' . $slno . '  of Rs ' . $totalAmount . ' for ' . $payment->project . ' & due for your review.',
                'end' => 'Login to the panel for review & process.',
            ];

            // Mail::to('rajoba3369@harinv.com')->send(new NoteStatusChangeMail($data));
            foreach ($allApprovalStepEmail as $key => $sendEmail) {
                Mail::to($sendEmail->user->email)->send(new NoteStatusChangeMail($data));
            }
        }

        $uid = auth()->user()->id;
        try {
            $user = auth()->user();
            $sl_no = $slno;
            // 40619717495
            /* $validator = Validator::make($request->all(), [
                'full_account_number' => 'required|unique:payments'
            ]);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator);
            } */

            if ($request->has('vendor') && !empty($request->vendor) && count($request->vendor) > 0) {
                foreach (array_chunk($request->vendor, 12) as $i => $vendor) {
                    // array_chunk() will divide $arr_a into smaller array as [[1, 2, 3..., 500],[501, 502, .... , 1000] and so one till 5000]
                    //do some stuff with $x;
                    // $sl_no = $this->helper->generateRandomNumber();
                    foreach ($vendor as $j => $ven) {
                        // $sl_no = $this->helper->generateRandomNumber();
                        $ven['sl_no'] = $sl_no;
                        $payment = Payment::where('sl_no', $slno)->where('id', $ven['id'])->first();
                        if ($payment) {
                            $payment->sl_no = $sl_no;
                            $payment->template_type = $ven['template_type'] ?? null;
                            $payment->project = $ven['project'] ?? null;
                            $payment->account_full_name = isset($ven['account_full_name']) ? $ven['account_full_name'] : null;
                            $payment->from_account_type = $ven['from_account_type'] ?? null;
                            $payment->full_account_number = $ven['full_account_number'] ?? null;
                            $payment->to = $ven['to'] ?? null;
                            $payment->to_account_type = isset($ven['to_account_type']) ? $ven['to'] : null;
                            $payment->name_of_beneficiary = $ven['name_of_beneficiary'] ?? null;
                            $payment->account_number = $ven['account_number'] ?? null;
                            $payment->name_of_bank = $ven['name_of_bank'] ?? null;
                            $payment->ifsc_code = $ven['ifsc_code'] ?? null;
                            $payment->amount = $ven['amount'] ?? null;
                            $payment->purpose = $ven['purpose'] ?? null;
                            $payment->status = $request->status ?? null;
                            if (!$payment->save()) {
                                $message = 'Request not updated from A/C: ' . $ven['account_full_name'] . ' to ' . $ven['to'] . ' with SL No.:' . $sl_no . ' and payemtn ID ' . $ven['id'] . ' with template ' . $ven['template_type'];
                                throw new \Exception($message);
                                toastr()->error($message, 'Error', $this->toastOptions);
                            }
                            $message = 'Request updated created for A/C: ' . $ven['account_full_name'] . ' to ' . $ven['to'] . ' with SL No.:' . $sl_no . ' and payemtn ID ' . $ven['id'] . ' with template ' . $ven['template_type'];
                            activity('Request updated with SL No.: ' . $sl_no . 'and payemtn ID ' . $ven['id'])
                                ->performedOn($user) // Entry add in table. model name(subject_type) & id(subject_id)
                                ->causedBy($user) //causer_id = admin id, causer type = admin model
                                ->event(__METHOD__)
                                ->withProperties($ven)
                                ->log($message . " [$user->email]");
                        }
                    }
                }
            } else {
                $message = 'No template selection found!: ';
                throw new \Exception($message);
            }

            Cache::forget('cart_' . $uid);
            return redirect()->back()->with('success', 'Request updated successfully.');
        } catch (\Throwable $th) {
            //throw $th;
            if ($th instanceof ModelNotFoundException) {
                $message = $th->getMessage();
                $errors = ['QueryException', $message];
            } elseif ($th instanceof QueryException) {
                $message = $th->getMessage();
                $errors = ['QueryException', $message];
            } else {
                $message = $th->getMessage();
                $errors = ['Exception', $th->getMessage()];
            }
            activity($message . " [$user->email]")
                ->performedOn($user) // Entry add in table. model name(subject_type) & id(subject_id)
                ->causedBy($user) //causer_id = admin id, causer type = admin model
                ->event(__METHOD__)
                ->withProperties([])
                ->log($message);
            toastr()->error($message);
            return redirect()->back()->withError($message);
        }
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Payment $payment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, $id)
    {
        $payment = Payment::where('id', $id)->where('sl_no', $request->sl_no)->first();

        if (!$payment) {
            return redirect()->back()->with('error', 'Payment not found.');
        }
        $payments = Payment::where('sl_no', $request->sl_no)->get();
        // dd($payments);
        if ($payments->isEmpty()) {
            return redirect()->back()->with('error', 'Payments not found.');
        }
        try {
            $log = BankLetterApprovalLog::where('sl_no', $request->sl_no)->first();

            if ($log) {
                // Detach related priorities from the pivot table
                $log->priorities()->detach();

                // Delete the log
                $log->delete();
            }
            // Update PaymentNote status to 'A'
            if ($payment->paymentNote) {
                $payment->paymentNote->status = 'A';
                $payment->paymentNote->save();
            }
            foreach ($payments as $pay) {
                $pay->forceDelete();
            }
            return redirect()->back()->with('success', 'Deleted successfully.');
        } catch (\Exception $e) {
            \Log::error('Failed to update status: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong.');
        }
    }
    /**
     * Remove the specified resource from storage.
     */
    public function deleteRequestItem(Request $request, $slno, $id)
    {
        $payment = Payment::where('id', $id)->where('sl_no', $slno)->first();
        if ($payment->delete()) {
            return redirect()->back()->with('success', 'created successfully.');
        }
    }
    /**
     * Remove the specified resource from storage.
     */
    public function getFromAccount(Request $request, $temp_type)
    {
        //
        // dd($request->all(), $temp_type);
        $internalAc = $this->helper->getFromAccount($temp_type);

        $html = '<option value="">--Select---</option>';
        foreach ($internalAc as $interAc) {
            $html .= '<option value="' . $interAc->account_name . '" data-ac="' . $interAc->account_number . '">' . $interAc->account_name . '</option>';
        }
        return response()->json([
            'html' => $html,
        ]);
    }
    /**
     * Remove the specified resource from storage.
     */
    public function getAllVendors(Request $request, $temp_type)
    {
        //
        // dd($request->all(), $temp_type);
        $accounts = $this->helper->getAllVendors();
        $html = '<option value="">--Select---</option>';
        foreach ($accounts as $account) {
            $html .= '<option value="' . $account->account_name . '" data-ac="' . $account->account_number . '">' . $account->account_name . '</option>';
        }
        return response()->json([
            'html' => $html,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function searchFromVendor(Request $request)
    {
        $search_value = null;
        $search_value = $request->search ?? null;
        $columns = Schema::getColumnListing('vendors');
        $resultsarray = [];
        // $query = Vendor::query(); // Get all data of the class
        $query = Vendor::query()->where('status', 'active'); // Get all data of the class
        $query->select('id', 'project', 's_no', 'short_name', 'account_name', 'vendor_code', 'vendor_name', 'account_number', 'vendor_nick_name', 'benificiary_name', 'from_account_type')->where('vendor_type', 'Internal');
        if (!is_null($search_value)) {
            foreach ($columns as $column) {
                $query->whereNotNull('short_name');
                // $query->where('from_account_type', 'Internal')->whereNull('parent');
                $query->orWhere($column, 'LIKE', '%' . $search_value . '%');
                /* if ($results != '[]') {
                    array_push($resultsarray, $results);
                } */
            }
        }
        $result = $query->groupBy('short_name')->get();
        return $result;
    }
    /**
     * Remove the specified resource from storage.
     */
    public function searchProject(Request $request)
    {
        $search_value = null;
        $search_value = $request->search ?? null;
        $columns = Schema::getColumnListing('vendors');
        $resultsarray = [];
        $query = Vendor::query()->where('status', 'active'); // Get all data of the class
        $query->select('id', 'project', 's_no', 'short_name', 'account_name', 'vendor_code', 'vendor_name', 'account_number', 'vendor_nick_name', 'benificiary_name', 'from_account_type');
        if (!is_null($search_value)) {
            /* foreach ($columns as $column) {
                $query->orWhere($column, 'LIKE', '%' . $search_value . '%');
            } */
            $query->where('project', $search_value);
        } else {
            $query->groupBy('project');
        }
        $result = $query->whereNotNull('project')->get();
        return $result;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function searchVendor(Request $request)
    {
        $search_value = null;
        $search_value = $request->search ?? null;
        // $from_account = $request->from_account ? $request->from_account : null;
        $from_account = $request->s_no ? $request->s_no : null;
        $project = $request->project ? $request->project : null;
        $internal_external = $request->internal_external ? $request->internal_external : null;
        $template_type = $request->has('template_type') ? $request->template_type : null;
        $result = null;
        $columns = Schema::getColumnListing('vendors');
        if ($from_account == 2) {
            return response()->json([
                'success' => false,
                'message' => 'No Transfer allowed , Inward or Outward',
                'data' => [],
            ]);
        }
        // \DB::enableQueryLog();
        $query = Vendor::query()->where('v1.status', 'active'); // Get all data of the class
        if (!is_null($template_type) && $template_type == 'sbi-sbi-internal-external-bulk') {
            $query->where('v1.name_of_bank', 'State Bank of India');
        }

        if ($from_account == 1) {
            // DB::enableQueryLog();
            // $query = Vendor::query()->where('v1.status', 'active'); // Get all data of the class
            /* $query->select('id', 's_no', 'project', 'short_name', 'account_name', 'vendor_code', 'vendor_name', 'vendor_type', 'account_number', 'vendor_nick_name', 'benificiary_name', 'from_account_type', 'name_of_bank')->where('s_no', 1)->where('project', $project)->where('from_account_type', 'Internal'); */
            $query
                ->from('vendors as v1')
                ->select('v1.*')
                ->join('vendors as v2', 'v1.id', '=', 'v2.id')
                ->whereBetween('v1.s_no', [3, 18])
                ->where('v1.project', $project)
                ->orWhere(function ($query) use ($template_type) {
                    if (!is_null($template_type) && $template_type == 'sbi-sbi-internal-external-bulk') {
                        $query->where('v1.name_of_bank', 'State Bank of India');
                    }
                    return $query->where('v1.from_account_type', 'External');
                });
            $accounts = $query->get();
            // $queries = DB::getQueryLog();
            // dd($queries, $accounts);
            $result = $accounts;
        }
        if ($from_account == 3) {
            // $query = Vendor::query()->where('v1.status', 'active'); // Get all data of the class
            /* $query->select('id', 's_no', 'project', 'short_name', 'account_name', 'vendor_code', 'vendor_name', 'vendor_type', 'account_number', 'vendor_nick_name', 'benificiary_name', 'from_account_type', 'name_of_bank')->where('s_no', 1)->where('project', $project)->where('from_account_type', 'Internal'); */
            $query
                ->from('vendors as v1')
                ->select('v1.*')
                ->join('vendors as v2', 'v1.id', '=', 'v2.id')
                ->whereIn('v1.s_no', [1, 18])
                ->where('v1.project', $project)
                ->orWhere(function ($query) use ($template_type) {
                    if (!is_null($template_type) && $template_type == 'sbi-sbi-internal-external-bulk') {
                        $query->where('v1.name_of_bank', 'State Bank of India');
                    }
                    return $query->where('v1.from_account_type', 'External');
                });
            $accounts = $query->get();
            // $queries = DB::getQueryLog();
            // dd($queries, $accounts);
            $result = $accounts;
        }
        if ($from_account == 4 || $from_account == 6) {
            // $query = Vendor::query()->where('v1.status', 'active'); // Get all data of the class
            /* $query->select('id', 's_no', 'project', 'short_name', 'account_name', 'vendor_code', 'vendor_name', 'vendor_type', 'account_number', 'vendor_nick_name', 'benificiary_name', 'from_account_type', 'name_of_bank')->where('s_no', 1)->where('project', $project)->where('from_account_type', 'Internal'); */
            $query
                ->from('vendors as v1')
                ->select('v1.*')
                ->join('vendors as v2', 'v1.id', '=', 'v2.id')
                ->whereIn('v1.s_no', [1, 17])
                ->where('v1.project', $project)
                ->orWhere(function ($query) use ($template_type) {
                    if (!is_null($template_type) && $template_type == 'sbi-sbi-internal-external-bulk') {
                        $query->where('v1.name_of_bank', 'State Bank of India');
                    }
                    return $query->where('v1.from_account_type', 'External');
                });
            $accounts = $query->get();
            // $queries = DB::getQueryLog();
            // dd($queries, $accounts);
            $result = $accounts;
        }
        /* if($from_account == 5){
            $query = Vendor::query()->where('v1.status', 'active'); // Get all data of the class
            $query->select('id', 'account_name', 'vendor_code', 'vendor_name', 'account_number', 'vendor_nick_name', 'benificiary_name', 'from_account_type')
                    ->whereBetween('id', [1,17]);

            $accounts = $query->get();

            $query1 = Vendor::query()->where('v1.status', 'active'); // Get all data of the class
            $query1->select('id', 'account_name', 'vendor_code', 'vendor_name', 'account_number', 'vendor_nick_name', 'benificiary_name', 'from_account_type');
            $vendors = $query1->get();
            $accounts->push(...$vendors);
            $result = $accounts;
            return response()->json([
                'success' => true,
                'message' => "Permitted internal/external transfer",
                'data' => $accounts
            ]);
        } */
        if ($from_account == 7 || $from_account == 9 || $from_account == 11 || $from_account == 12 || $from_account == 13 || $from_account == 14 || $from_account == 15 || $from_account == 16 || $from_account == 17) {
            // DB::enableQueryLog();
            // $query = Vendor::query()->where('v1.status', 'active'); // Get all data of the class
            /* $query->select('id', 's_no', 'project', 'short_name', 'account_name', 'vendor_code', 'vendor_name', 'vendor_type', 'account_number', 'vendor_nick_name', 'benificiary_name', 'from_account_type', 'name_of_bank')->where('s_no', 1)->where('project', $project)->where('from_account_type', 'Internal'); */
            $query
                ->from('vendors as v1')
                ->select('v1.*')
                ->join('vendors as v2', 'v1.id', '=', 'v2.id')
                ->where('v1.s_no', 1)
                ->where('v1.project', $project)
                ->orWhere(function ($query) use ($template_type) {
                    if (!is_null($template_type) && $template_type == 'sbi-sbi-internal-external-bulk') {
                        $query->where('v1.name_of_bank', 'State Bank of India');
                    }
                    return $query->where('v1.from_account_type', 'External');
                });
            $accounts = $query->get();
            // $queries = DB::getQueryLog();
            // dd($queries, $accounts);
            $result = $accounts;
        }
        if ($from_account == 8) {
            // DB::enableQueryLog();
            // $query = Vendor::query()->where('v1.status', 'active'); // Get all data of the class
            /* $query->select('id', 's_no', 'project', 'short_name', 'account_name', 'vendor_code', 'vendor_name', 'vendor_type', 'account_number', 'vendor_nick_name', 'benificiary_name', 'from_account_type', 'name_of_bank')->where('s_no', 1)->where('project', $project)->where('from_account_type', 'Internal'); */
            $query
                ->from('vendors as v1')
                ->select('v1.*')
                ->join('vendors as v2', 'v1.id', '=', 'v2.id')
                ->whereIn('v1.s_no', [1, 6])
                ->where('v1.project', $project)
                ->orWhere(function ($query) use ($template_type) {
                    if (!is_null($template_type) && $template_type == 'sbi-sbi-internal-external-bulk') {
                        $query->where('v1.name_of_bank', 'State Bank of India');
                    }
                    return $query->where('v1.from_account_type', 'External');
                });
            $accounts = $query->get();
            // $queries = DB::getQueryLog();
            // dd($queries, $accounts);
            $result = $accounts;
        }
        if ($from_account == 10) {
            // DB::enableQueryLog();
            // DB::enableQueryLog();
            // $query = Vendor::query()->where('v1.status', 'active'); // Get all data of the class
            /* $query->select('id', 's_no', 'project', 'short_name', 'account_name', 'vendor_code', 'vendor_name', 'vendor_type', 'account_number', 'vendor_nick_name', 'benificiary_name', 'from_account_type', 'name_of_bank')->where('s_no', 1)->where('project', $project)->where('from_account_type', 'Internal'); */
            $query
                ->from('vendors as v1')
                ->select('v1.*')
                ->join('vendors as v2', 'v1.id', '=', 'v2.id')
                ->whereIn('v1.s_no', [1, 3, 4, 5, 6, 8, 9])
                ->where('v1.project', $project)
                ->orWhere(function ($query) use ($template_type) {
                    if (!is_null($template_type) && $template_type == 'sbi-sbi-internal-external-bulk') {
                        $query->where('v1.name_of_bank', 'State Bank of India');
                    }
                    return $query->where('v1.from_account_type', 'External');
                });
            $accounts = $query->get();
            // $queries = DB::getQueryLog();
            // dd($queries, $accounts);
            $result = $accounts;
        }
        if ($from_account == 18) {
            // DB::enableQueryLog();
            //  $query = Vendor::query()->where('v1.status', 'active'); // Get all data of the class
            /* $query->select('id', 's_no', 'project', 'short_name', 'account_name', 'vendor_code', 'vendor_name', 'vendor_type', 'account_number', 'vendor_nick_name', 'benificiary_name', 'from_account_type', 'name_of_bank')->where('s_no', 1)->where('project', $project)->where('from_account_type', 'Internal'); */
            $query
                ->from('vendors as v1')
                ->select('v1.*')
                ->join('vendors as v2', 'v1.id', '=', 'v2.id')
                ->where('v1.s_no', 3)
                ->where('v1.project', $project)
                ->orWhere(function ($query) use ($template_type) {
                    if (!is_null($template_type) && $template_type == 'sbi-sbi-internal-external-bulk') {
                        $query->where('v1.name_of_bank', 'State Bank of India');
                    }
                    return $query->where('v1.from_account_type', 'External');
                });
            $accounts = $query->get();
            // $queries = DB::getQueryLog();
            // dd($queries, $accounts);
            $result = $accounts;
        }
        // dd(\DB::getQueryLog());
        return response()->json([
            'success' => true,
            'message' => 'Permitted internal/external transfer',
            'data' => $result,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function addRequestInQueue(Request $request)
    {
        $uid = auth()->user()->id;
        $cart = Cache::get('cart_' . $uid);
        if ($request->has('template_type') && $request->template_type == 'anybank-internalexternal-single' && !empty($cart)) {
            return response()->json(
                [
                    'message' => "Only single request allow for {$request->template_type} template",
                ],
                400,
            );
        }
        /*  dd($request->account_full_name, $cart[0]['account_full_name'], ($request->account_full_name == $cart[0]['account_full_name']));
        if($request->has('account_full_name') && $request->account_full_name == 'anybank-onetomany-external-bulk' && !empty($cart)){
            return response()->json([
                "message" => "Only single request allow for {$request->template_type} template from select"
            ], 400);
        } */
        $inputs = $request->except(['_token']);
        $payment['template_type'] = $inputs['template_type']; // ok
        $payment['project'] = $inputs['project']; // ok
        $payment['account_full_name'] = $inputs['account_full_name'];
        $payment['from_account_type'] = $inputs['from_account_type'] ?? $inputs['from_account']; // ok
        $payment['full_account_number'] = $inputs['from_account_no']; // ok
        $payment['to'] = isset($inputs['to_account_type']) ? $inputs['to_account_type'] : null; // ok vendor_name
        $payment['to_account_type'] = $inputs['vendor_code']; // ok
        $payment['benificiary_name'] = $inputs['benificiary_name']; // ok
        $payment['account_number'] = $inputs['vendor_account']; // ok
        $payment['name_of_bank'] = $inputs['name_of_bank'];
        $payment['ifsc_code'] = $inputs['ifsc_code'];
        $payment['amount'] = $inputs['amount']; // ok
        $payment['purpose'] = $inputs['purpose']; // ok

        // dd($inputs, $payment);

        if (empty($cart)) {
            $cart[] = $payment;
            Cache::put('cart_' . $uid, $cart);
        } else {
            $cart[] = $payment;
            Cache::put('cart_' . $uid, $cart);
        }

        $cart = Cache::get('cart_' . $uid);
        /*$paymentsShortcut = PaymentsShortcut::where('id', 1)->first();
         dd($cart, $paymentsShortcut);*/
        $html = view('backend.payment.requestForm', ['cartItems' => $cart])->render();
        return response()->json([
            'html' => $html,
        ]);
    }

    /**
     * Remove the specified resource from cart session.
     */
    public function deleteRequestInQueue(Request $request)
    {
        $uid = auth()->user()->id;
        $indx = base64_decode($request->index);
        //

        // dd(Cache::forget('cart'));
        if ($request->has('clearCart') && $request->clearCart) {
            // Cache::forget('cart');
            Cache::forget('cart_' . $uid);
            $cart = [];
        } else {
            $cart = Cache::get('cart_' . $uid);
            if ($request->has('index')) {
                unset($cart[$indx]);
                $cart = array_values($cart);
                Cache::put('cart_' . $uid, $cart);
            }

            $cart = Cache::get('cart_' . $uid);
        }

        $html = view('backend.payment.requestForm', ['cartItems' => $cart])->render();
        return response()->json([
            'html' => $html,
        ]);
    }
    public function ratio(Request $request)
    {
        //
        // dd(Cache::forget('cart'));
        $uid = auth()->user()->id;
        if ($request->has('clearCart') && $request->clearCart) {
            Cache::forget('cart_' . $uid);
            $cart = [];
        } else {
            $cart = Cache::get('cart_' . $uid);
            if ($request->has('index')) {
                unset($cart[$request->index]);
                $cart = array_values($cart);
                Cache::put('cart', $cart);
            }

            $cart = Cache::get('cart_' . $uid);
        }

        $html = view('backend.payment.requestForm', ['cartItems' => $cart])->render();
        return response()->json([
            'html' => $html,
        ]);
    }

    public function shortcut($id)
    {
        $shortcut = PaymentsShortcut::find($id);
        // Find the shortcut

        $paymentDataArray = json_decode($shortcut->request_data, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            // Handle error (e.g., invalid JSON)
            return redirect()->back()->with('success', 'Invalid JSON in request_data.');
        }

        // Generate a new sl_no for the new payments
        // $newSlNo = $this->helper->generateRandomNumber();
        $newSlNo = $this->generateSerialNumber();

        // Loop through the decoded payment data and save each payment
        foreach ($paymentDataArray as $paymentData) {
            $payment = new Payment();
            /*$payment->sl_no = $newSlNo;
            $payment->template_type = $paymentData['template_type'] ?? null;
            $payment->project = $paymentData['project'] ?? null;
            $payment->account_full_name = $paymentData['account_full_name'] ?? null;
            $payment->from_account_type = $paymentData['from_account_type'] ?? null;
            $payment->full_account_number = $paymentData['full_account_number'] ?? null;
            $payment->to = $paymentData['to'] ?? null;
            $payment->to_account_type = $paymentData['to_account_type'] ?? null;
            $payment->name_of_beneficiary = $paymentData['benificiary_name'] ?? null;
            $payment->account_number = $paymentData['account_number'] ?? null;
            $payment->name_of_bank = $paymentData['name_of_bank'] ?? null;
            $payment->amount = $paymentData['amount'] ?? null;
            $payment->purpose = $paymentData['purpose'] ?? null;*/
            // dd($paymentData);
            $ven = $paymentData;
            $payment->sl_no = $newSlNo;
            $payment->template_type = $ven['template_type'] ?? null;
            $payment->project = $ven['project'] ?? null;
            $payment->account_full_name = isset($ven['account_full_name']) ? $ven['account_full_name'] : null;
            $payment->from_account_type = $ven['from_account_type'] ?? null;
            $payment->full_account_number = $ven['full_account_number'] ?? null;
            $payment->to = $ven['benificiary_name'] ?? null;
            $payment->to_account_type = isset($ven['to_account_type']) ? $ven['to_account_type'] : null;
            $payment->name_of_beneficiary = $ven['benificiary_name'] ?? null;
            $payment->account_number = $ven['account_number'] ?? null;
            $payment->name_of_bank = $ven['name_of_bank'] ?? null;
            $payment->ifsc_code = $ven['ifsc_code'] ?? null;
            $payment->amount = $ven['amount'] ?? null;
            $payment->purpose = $ven['purpose'] ?? null;
            $payment->save();
        }
        return redirect()->back()->with('success', 'created successfully.');
    }
    public function bankLetter(Request $request)
    {
        $noteIds = explode(',', $request->input('note_ids', ''));

        if (empty($noteIds)) {
            return back()->with('error', 'No notes selected.');
        }

        $notes = PaymentNote::whereIn('id', $noteIds)->get();

        return view('backend.payment.createToBank', compact('notes'));
    }
    public function logUpdate(Request $request)
    {
        $dataCheckAmount = Payment::where('sl_no', $request->sl_no)->latest()->take(12)->orderBy('id', 'asc')->get();
        $paymentEmail = Payment::where('sl_no', $request->sl_no)->first();
        $paymentEmailItems = Payment::where('sl_no', $request->sl_no)->get();

        $totalAmount = $dataCheckAmount->sum('amount');

        $existingLogsCount = BankLetterApprovalLog::where('sl_no', $request->sl_no)->count();
        $existingLogsCountFirst = BankLetterApprovalLog::where('sl_no', $request->sl_no)->first();
        $userAlreadySubmitted = BankLetterApprovalLog::where('sl_no', $request->sl_no)
            ->where('reviewer_id', auth()->id())
            ->exists();

        if ($userAlreadySubmitted) {
            return redirect()->back()->with('error', 'You have already submitted your approval.');
        }

        $approvalStepNew = BankLetterApprovalPriority::find($existingLogsCountFirst->priority_id);
        $nextStep = $existingLogsCount + 1;
        $approvalStepCurrent = BankLetterApprovalPriority::where('approver_level', $existingLogsCount)->where('approval_step_id', $approvalStepNew->approval_step_id)->first();
        $approvalStep = BankLetterApprovalPriority::where('approver_level', $nextStep)->where('approval_step_id', $approvalStepNew->approval_step_id)->first();
        $allApprovalStep = BankLetterApprovalPriority::where('approver_level', $nextStep)->where('approval_step_id', $approvalStepNew->approval_step_id)->pluck('id')->toArray();

        // dd($approvalStep, $approvalStepNew);
        if (!$approvalStepCurrent) {
            return redirect()->back()->with('success', 'Approval step 1 not found.');
        }
        if (!$approvalStep && $request->status != 'R') {
            BankLetterApprovalLog::create([
                'sl_no' => $request->sl_no,
                'priority_id' => $approvalStepCurrent->id,
                'reviewer_id' => auth()->id(),
                'status' => $request->status,
                'comments' => $request->remarks ?? null,
            ]);
            foreach ($dataCheckAmount as $payment) {
                $payment->status = 'A';
                $payment->save();
            }

            // if (!empty($paymentEmail) && !empty($paymentEmail->payment_note_id)) {
            //     $noteId = (int) $paymentEmail->payment_note_id;

            //     if ($noteId > 0) {
            //         $note = PaymentNote::with(['greenNote', 'reimbursementNote'])->find($noteId);

            //         if ($note) {
            //             $note->update(['status' => 'PA']);

            //             if ($note->greenNote) {
            //                 $note->greenNote->update(['status' => 'PA']);
            //             }

            //             if ($note->reimbursementNote) {
            //                 $note->reimbursementNote->update(['status' => 'PA']);
            //             }
            //         }
            //     }

            foreach ($paymentEmailItems as $payment) {
                $noteId = (int) $payment->payment_note_id;

                if ($noteId > 0) {
                    $note = PaymentNote::with(['greenNote', 'reimbursementNote'])->find($noteId);

                    if ($note) {
                        $note->update(['status' => 'A']);

                        if ($note->greenNote) {
                            $note->greenNote->update(['status' => 'A']);
                        }

                        if ($note->reimbursementNote) {
                            $note->reimbursementNote->update(['status' => 'A']);
                        }
                    }
                }
            }

            $data = [
                'updated_by' => auth()->user()->email,
                'subject' => 'Bank RTGS / NEFT Letter of Rs ' . $totalAmount . ' is Approved & due for Payment',
                'approver_name' => $approvalStepCurrent->user->name ?? 'Approver',
                'maker' => 'Approver 1 has approved a Bank RTGS / NEFT letter ' . $request->sl_no . ' of Rs ' . $totalAmount . ' for ' . $paymentEmail->project . ' & due for your review',
                'end' => 'Login to the panel for review & process.',
            ];

            // Mail::to('rajoba3369@harinv.com')->send(new NoteStatusChangeMail($data));
            Mail::to($paymentEmail->user->email)->send(new NoteStatusChangeMail($data));

            return redirect()->route('backend.payments.index')->with('success', 'Final step reached. No further approvals needed.');
        }

        if ($request->status == 'A') {
            $log = BankLetterApprovalLog::create([
                'sl_no' => $request->sl_no,
                'priority_id' => $approvalStep->id,
                'reviewer_id' => auth()->id(),
                'status' => $request->status,
                'comments' => $request->remarks ?? null,
            ]);
            if (!empty($allApprovalStep)) {
                $log->priorities()->attach($allApprovalStep);
            }

            $data = [
                'updated_by' => auth()->user()->email,
                'subject' => 'Bank RTGS / NEFT Letter of Rs Net Payable ' . $totalAmount . ' is due for Approval',
                'approver_name' => $approvalStep->user->name ?? 'Approver',
                'maker' => 'Approver 1 has approved a Bank RTGS / NEFT letter ' . $request->sl_no . ' of Rs ' . $totalAmount . ' for ' . $paymentEmail->project . ' & due for your review.',
                'end' => 'Login for review & Final Approval',
            ];
            // // Mail Send
            // Mail::to('rajoba3369@harinv.com')->send(new NoteStatusChangeMail($data));
            Mail::to($approvalStep->user->email)->send(new NoteStatusChangeMail($data));
            Mail::to($paymentEmail->user->email)->send(new NoteStatusChangeMail($data));

            return redirect()->route('backend.payments.index')->with('success', 'Approval log created for the next step.');
        } else {
            // BankLetterApprovalLog::create([
            //     'priority_id' => $approvalStepCurrent->id,
            //     'sl_no' => $request->sl_no,
            //     'reviewer_id' => auth()->id(),
            //     'status' => $request->status,
            //     'comments' => $request->remarks ?? null,
            // ]);

            // if (!empty($paymentEmail) && !empty($paymentEmail->payment_note_id)) {
            //     $noteId = (int) $paymentEmail->payment_note_id;

            //     if ($noteId > 0) {
            //         $note = PaymentNote::with(['greenNote', 'reimbursementNote'])->find($noteId);

            //         if ($note) {
            //             $note->update(['status' => 'A']);

            //             if ($note->greenNote) {
            //                 $note->greenNote->update(['status' => 'A']);
            //             }

            //             if ($note->reimbursementNote) {
            //                 $note->reimbursementNote->update(['status' => 'A']);
            //             }
            //         }
            //     }
            // }

            foreach ($paymentEmailItems as $payment) {
                $noteId = (int) $payment->payment_note_id;

                if ($noteId > 0) {
                    $note = PaymentNote::with(['greenNote', 'reimbursementNote'])->find($noteId);

                    if ($note) {
                        $note->update(['status' => 'A']);

                        if ($note->greenNote) {
                            $note->greenNote->update(['status' => 'A']);
                        }

                        if ($note->reimbursementNote) {
                            $note->reimbursementNote->update(['status' => 'A']);
                        }
                    }
                }
            }

            foreach ($dataCheckAmount as $payment) {
                $payment->status = 'R';
                $payment->save();
            }
            // Mail Send
            $data = [
                'updated_by' => auth()->user()->email,
                'subject' => 'Bank RTGS / NEFT Letter of Rs  ' . $totalAmount . '  has been Rejected',
                'approver_name' => $approvalStepCurrent->user->name ?? 'Approver',
                'maker' => '[Approver] has Rejected The bank RTGS / NEFT Letter No. ' . $request->sl_no . '  of Rs  ' . $totalAmount . '  for  ' . $paymentEmail->project,
                'rejection' => $request->remarks ?? null,
                'end' => 'Login to the panel for review & process.',
            ];
            // // Mail Send
            // Mail::to('rajoba3369@harinv.com')->send(new NoteStatusChangeMail($data));
            Mail::to($paymentEmail->user->email)->send(new NoteStatusChangeMail($data));

            return redirect()->route('backend.payments.index')->with('success', 'Approval has been rejected successfully with remarks.');
        }
    }

    private function generateSerialNumber()
    {
        /* $lastInvoice = Payment::latest()->first();
        $nextNumber = $lastInvoice ? intval($lastInvoice->sl_no) + 1 : 1;

        return str_pad($nextNumber, 4, '0', STR_PAD_LEFT); // Formats as 0001, 0002, etc. */

        // Fetch the maximum serial_number from the database
        $maxSerialNumber = Payment::max('sl_no');

        // Increment the maximum value or start from 1 if no records exist
        $nextNumber = $maxSerialNumber ? intval($maxSerialNumber) + 1 : 1;

        // Format the serial number as 0001, 0002, etc.
        return str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }
}
