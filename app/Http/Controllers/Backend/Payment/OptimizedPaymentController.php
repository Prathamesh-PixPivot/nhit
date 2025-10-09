<?php

namespace App\Http\Controllers\Backend\Payment;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\PaymentsShortcut;
use App\Models\Vendor;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
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

class OptimizedPaymentController extends Controller
{
    public $helper;
    protected $toastOptions;

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
     * Optimized index method with better query performance
     */
    public function index(Request $request)
    {
        $userId = Auth::id();
        $userRoles = Auth::user()->getRoleNames();
        $status = $request->status ?? 'S';

        // Cache user permissions
        // Check if user has payment-related roles
        $isAdmin = $userId == 1 || 
            $userRoles->contains('PN User') || 
            $userRoles->contains('PN Approver') ||
            $userRoles->contains('approver') ||
            $userRoles->contains('admin') ||
            $userRoles->contains('superadmin');
        
        if ($isAdmin) {
            $sl_no_filter = $this->getAdminSlNos($status);
        } else {
            $sl_no_filter = $this->getUserSlNos($userId, $status);
        }

        if ($request->ajax()) {
            return $this->getDataTableData($isAdmin, $userId, $status);
        }

        return view('backend.payment.index', compact('sl_no_filter'));
    }

    /**
     * Get admin SL numbers with caching
     */
    private function getAdminSlNos($status)
    {
        $cacheKey = "admin_sl_nos_{$status}_" . now()->format('Y-m-d-H');
        
        return Cache::remember($cacheKey, 300, function () use ($status) {
            $query = Payment::select('sl_no')->groupBy('sl_no');
            
            if ($status !== 'all') {
                $query->where('status', $status);
            }
            
            return $query->orderBy('id', 'desc')->get();
        });
    }

    /**
     * Get user SL numbers with caching
     */
    private function getUserSlNos($userId, $status)
    {
        $cacheKey = "user_sl_nos_{$userId}_{$status}_" . now()->format('Y-m-d-H');
        
        return Cache::remember($cacheKey, 300, function () use ($userId, $status) {
            // Get SL numbers from logs and payments
            $fromLogs = DB::table('bank_letter_log_priority')
                ->join('bank_letter_approval_logs', 'bank_letter_log_priority.bank_letter_approval_log_id', '=', 'bank_letter_approval_logs.id')
                ->join('bank_letter_approval_priorities', 'bank_letter_log_priority.priority_id', '=', 'bank_letter_approval_priorities.id')
                ->where('bank_letter_approval_priorities.reviewer_id', $userId)
                ->pluck('bank_letter_approval_logs.sl_no')
                ->toArray();

            $fromPayments = DB::table('payments_new')
                ->where('user_id', $userId)
                ->pluck('sl_no')
                ->toArray();

            $allSlNos = array_unique(array_merge($fromLogs, $fromPayments));

            $query = Payment::whereIn('sl_no', $allSlNos)->select('sl_no')->groupBy('sl_no');
            
            if ($status !== 'all') {
                $query->where('status', $status);
            }
            
            return $query->orderBy('id', 'desc')->get();
        });
    }

    /**
     * Get DataTable data with optimized queries
     */
    private function getDataTableData($isAdmin, $userId, $status)
    {
        if ($isAdmin) {
            $data = $this->getAdminData($status);
        } else {
            $data = $this->getUserData($userId, $status);
        }

        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('vendor_name', function ($row) {
                return $row->name_of_beneficiary ?? '-';
            })
            ->addColumn('amount', function ($row) {
                $amount = optional($row->paymentNote)->net_payable_round_off;
                return $amount !== null ? Helper::formatIndianNumber($amount) : '-';
            })
            ->addColumn('action', function ($row) {
                return $this->getActionButtons($row);
            })
            ->addColumn('shortcut_name', function ($row) {
                return $this->getShortcutName($row);
            })
            ->addColumn('status', function ($row) {
                return $this->getStatusColumn($row);
            })
            ->rawColumns(['action', 'shortcut_name', 'status'])
            ->make(true);
    }

    /**
     * Get admin data with optimized query
     */
    private function getAdminData($status)
    {
        $query = Payment::select('id', 'sl_no', 'name_of_beneficiary', 'status', 'template_type', 'user_id', 'created_at')
            ->with(['paymentNote:id,net_payable_round_off'])
            ->groupBy('sl_no');

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        return $query->orderBy('id', 'desc')->get();
    }

    /**
     * Get user data with optimized query
     */
    private function getUserData($userId, $status)
    {
        // Get user's SL numbers
        $fromLogs = DB::table('bank_letter_log_priority')
            ->join('bank_letter_approval_logs', 'bank_letter_log_priority.bank_letter_approval_log_id', '=', 'bank_letter_approval_logs.id')
            ->join('bank_letter_approval_priorities', 'bank_letter_log_priority.priority_id', '=', 'bank_letter_approval_priorities.id')
            ->where('bank_letter_approval_priorities.reviewer_id', $userId)
            ->pluck('bank_letter_approval_logs.sl_no')
            ->toArray();

        $fromPayments = DB::table('payments_new')
            ->where('user_id', $userId)
            ->pluck('sl_no')
            ->toArray();

        $allSlNos = array_unique(array_merge($fromLogs, $fromPayments));

        $query = Payment::select('id', 'sl_no', 'name_of_beneficiary', 'status', 'template_type', 'user_id', 'created_at')
            ->with(['paymentNote:id,net_payable_round_off', 'greenNote:id,vendor_name'])
            ->whereIn('sl_no', $allSlNos);

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        return $query->groupBy('sl_no')->orderBy('id', 'desc')->get();
    }

    /**
     * Get action buttons for DataTable
     */
    private function getActionButtons($row)
    {
        $btn = '';

        if (Auth::id() == 1) {
            $btn = '<form action="' . route('backend.payments.destroy', $row->id) . '" method="post" style="display:inline;">' .
                   csrf_field() . '<input type="hidden" name="_method" value="DELETE">' .
                   '<input type="hidden" name="sl_no" value="' . $row->sl_no . '">' .
                   '<button type="submit" class="btn btn-outline-danger btn-xs" onclick="return confirm(\'Do you want to delete this role?\');"><i class="bi bi-trash"></i></button>' .
                   '</form>' .
                   '<a href="' . route('backend.payments.editPaymentRequest', $row->sl_no) . '" class="btn btn-outline-primary btn-xs">' .
                   '<i class="bi bi-pencil-square"></i></a>';
        } elseif ($row->user_id == Auth::id() && $row->status == 'D') {
            $btn .= '<a href="' . route('backend.payments.editPaymentRequest', $row->sl_no) . '" class="btn btn-outline-primary btn-xs">' .
                    '<i class="bi bi-pencil-square"></i></a>';
        }

        $btn .= '<form action="' . route('backend.templates.templateCommon', $row->template_type) . '" method="post" style="display:inline;">' .
                csrf_field() . '<input type="hidden" name="slno" value="' . $row->sl_no . '">' .
                '<button type="submit" class="btn btn-outline-info btn-xs" onclick="return confirm(\'Do you want to preview/generate PDF??\');">' .
                '<i class="bi bi-eye"></i></button></form>';

        return $btn;
    }

    /**
     * Get shortcut name for DataTable
     */
    private function getShortcutName($row)
    {
        $paymentsShortcut = PaymentsShortcut::where('sl_no', $row->sl_no)->first();
        
        if ($paymentsShortcut) {
            return $paymentsShortcut->shortcut_name . ' <a href="' . route('backend.payments.shortcut', $paymentsShortcut->id) . '" class="btn btn-outline-info btn-xs" style="line-height: 1.5;">' .
                   '<i class="bi bi-share"></i></a>';
        }
        
        return '';
    }

    /**
     * Get status column for DataTable
     */
    private function getStatusColumn($row)
    {
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

        // Add UTR date if available
        if ($row->status === 'PD' && $row->utr_date) {
            $formattedUtrDate = \Carbon\Carbon::parse($row->utr_date)->format('d-m-Y');
            $statusHtml .= '<div class="mt-1 small text-muted">on ' . $formattedUtrDate . '</div>';
        }

        // Get next approver information
        $approvers = BankLetterApprovalLog::with('logPriorities.priority.user')
            ->where('sl_no', $row->sl_no)
            ->get();

        $nextApproverHtml = '';
        if ($approvers->last()?->logPriorities->last()?->priority) {
            $nextApproverHtml .= '<div class="mt-2 text-start"><strong>Next Approver:</strong> ';
            foreach ($approvers->last()->logPriorities as $log) {
                $nextApproverHtml .= $log->priority->user->name . ', ';
            }
            $nextApproverHtml = rtrim($nextApproverHtml, ', ') . '</div>';
        }

        return $statusHtml . $nextApproverHtml;
    }

    /**
     * Optimized store method with better error handling
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        $uid = $user->id;
        $cart = Cache::get('cart_' . $uid);

        // Validate payment note IDs
        if (!empty($request->payment_note_id) && is_array($request->payment_note_id)) {
            $this->updatePaymentNoteStatuses($request->payment_note_id);
        }

        try {
            if ($request->has('vendor') && !empty($request->vendor) && count($request->vendor) > 0) {
                $this->processVendorPayments($request);
            } else {
                throw new \Exception('No template selection found!');
            }

            Cache::forget('cart_' . $uid);
            return redirect()->route('backend.payments.index')->with('success', 'Request created successfully.');
        } catch (\Throwable $th) {
            $this->handleStoreError($th, $user);
            return redirect()->back()->withError($th->getMessage());
        }
    }

    /**
     * Update payment note statuses
     */
    private function updatePaymentNoteStatuses(array $paymentNoteIds)
    {
        $paymentNoteIds = array_filter(array_map('intval', $paymentNoteIds), fn($id) => $id > 0);

        if (!empty($paymentNoteIds)) {
            PaymentNote::whereIn('id', $paymentNoteIds)->update(['status' => 'B']);
            
            $notes = PaymentNote::with(['greenNote', 'reimbursementNote'])
                ->whereIn('id', $paymentNoteIds)
                ->get();

            foreach ($notes as $note) {
                if ($note->greenNote) {
                    $note->greenNote->update(['status' => 'B']);
                }
                if ($note->reimbursementNote) {
                    $note->reimbursementNote->update(['status' => 'B']);
                }
            }
        }
    }

    /**
     * Process vendor payments efficiently
     */
    private function processVendorPayments(Request $request)
    {
        foreach (array_chunk($request->vendor, 12) as $i => $vendor) {
            $sl_no = $this->generateSerialNumber();
            
            if ($request->input('shortcut') === 'on') {
                $this->createPaymentShortcut($sl_no, $request);
            }

            foreach ($vendor as $j => $ven) {
                $this->createPaymentRecord($ven, $sl_no, $request, $j);
            }
        }
    }

    /**
     * Create payment shortcut
     */
    private function createPaymentShortcut($sl_no, Request $request)
    {
        $paymentsShortcut = new PaymentsShortcut();
        $paymentsShortcut->sl_no = $sl_no;
        $paymentsShortcut->shortcut_name = $request->shortcut_name ?: 'Shortcut ' . rand();
        $paymentsShortcut->request_data = json_encode($request->input('vendor'));
        $paymentsShortcut->save();
    }

    /**
     * Create payment record
     */
    private function createPaymentRecord($ven, $sl_no, Request $request, $j)
    {
        $payment = new Payment();
        $payment->sl_no = $sl_no;
        $payment->template_type = $ven['template_type'] ?? null;
        $payment->project = $ven['project'] ?? null;
        $payment->account_full_name = $ven['account_full_name'] ?? null;
        $payment->from_account_type = $ven['from_account_type'] ?? null;
        $payment->full_account_number = $ven['full_account_number'] ?? null;
        $payment->to = $ven['to_account_type'] ?? null;
        $payment->to_account_type = $ven['to'] ?? null;
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
        }

        $this->logActivity($payment, $ven, $sl_no);
    }

    /**
     * Log activity for payment creation
     */
    private function logActivity($payment, $ven, $sl_no)
    {
        $user = auth()->user();
        $message = 'Request created for A/C: ' . $ven['account_full_name'] . ' to ' . $ven['benificiary_name'] . ' with SL No.:' . $sl_no . ' with template ' . $ven['template_type'];
        
        activity('Request created with SL No.: ' . $sl_no)
            ->performedOn($user)
            ->causedBy($user)
            ->event(__METHOD__)
            ->withProperties($ven)
            ->log($message . " [$user->email]");
    }

    /**
     * Handle store errors
     */
    private function handleStoreError(\Throwable $th, $user)
    {
        $message = $th->getMessage();
        
        activity($message . " [$user->email]")
            ->performedOn($user)
            ->causedBy($user)
            ->event(__METHOD__)
            ->withProperties([])
            ->log($message);
            
        toastr()->error($message);
    }

    /**
     * Optimized search vendor method
     */
    public function searchVendor(Request $request)
    {
        $search_value = $request->search ?? null;
        $from_account = $request->s_no ?? null;
        $project = $request->project ?? null;
        $template_type = $request->template_type ?? null;

        if ($from_account == 2) {
            return response()->json([
                'success' => false,
                'message' => 'No Transfer allowed, Inward or Outward',
                'data' => [],
            ]);
        }

        $result = $this->getVendorResults($from_account, $project, $template_type);

        return response()->json([
            'success' => true,
            'message' => 'Permitted internal/external transfer',
            'data' => $result,
        ]);
    }

    /**
     * Get vendor results based on account type
     */
    private function getVendorResults($from_account, $project, $template_type)
    {
        $query = Vendor::query()->where('v1.status', 'active');

        if ($template_type == 'sbi-sbi-internal-external-bulk') {
            $query->where('v1.name_of_bank', 'State Bank of India');
        }

        $sNoMap = [
            1 => [3, 18],
            3 => [1, 18],
            4 => [1, 17],
            6 => [1, 17],
            7 => [1],
            8 => [1, 6],
            9 => [1],
            10 => [1, 3, 4, 5, 6, 8, 9],
            11 => [1],
            12 => [1],
            13 => [1],
            14 => [1],
            15 => [1],
            16 => [1],
            17 => [1],
            18 => [3],
        ];

        if (isset($sNoMap[$from_account])) {
            $query->from('vendors as v1')
                ->select('v1.*')
                ->join('vendors as v2', 'v1.id', '=', 'v2.id')
                ->whereIn('v1.s_no', $sNoMap[$from_account])
                ->where('v1.project', $project)
                ->orWhere(function ($query) use ($template_type) {
                    if ($template_type == 'sbi-sbi-internal-external-bulk') {
                        $query->where('v1.name_of_bank', 'State Bank of India');
                    }
                    return $query->where('v1.from_account_type', 'External');
                });
        }

        return $query->get();
    }

    /**
     * Generate serial number efficiently
     */
    private function generateSerialNumber()
    {
        $maxSerialNumber = Payment::max('sl_no');
        $nextNumber = $maxSerialNumber ? intval($maxSerialNumber) + 1 : 1;
        return str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Optimized shortcut method
     */
    public function shortcut($id)
    {
        $shortcut = PaymentsShortcut::find($id);
        
        if (!$shortcut) {
            return redirect()->back()->with('error', 'Shortcut not found.');
        }

        $paymentDataArray = json_decode($shortcut->request_data, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return redirect()->back()->with('error', 'Invalid JSON in request_data.');
        }

        $newSlNo = $this->generateSerialNumber();

        foreach ($paymentDataArray as $paymentData) {
            $this->createPaymentFromShortcut($paymentData, $newSlNo);
        }

        return redirect()->back()->with('success', 'Created successfully.');
    }

    /**
     * Create payment from shortcut data
     */
    private function createPaymentFromShortcut($paymentData, $newSlNo)
    {
        $payment = new Payment();
        $payment->sl_no = $newSlNo;
        $payment->template_type = $paymentData['template_type'] ?? null;
        $payment->project = $paymentData['project'] ?? null;
        $payment->account_full_name = $paymentData['account_full_name'] ?? null;
        $payment->from_account_type = $paymentData['from_account_type'] ?? null;
        $payment->full_account_number = $paymentData['full_account_number'] ?? null;
        $payment->to = $paymentData['benificiary_name'] ?? null;
        $payment->to_account_type = $paymentData['to_account_type'] ?? null;
        $payment->name_of_beneficiary = $paymentData['benificiary_name'] ?? null;
        $payment->account_number = $paymentData['account_number'] ?? null;
        $payment->name_of_bank = $paymentData['name_of_bank'] ?? null;
        $payment->ifsc_code = $paymentData['ifsc_code'] ?? null;
        $payment->amount = $paymentData['amount'] ?? null;
        $payment->purpose = $paymentData['purpose'] ?? null;
        $payment->save();
    }

    /**
     * Optimized destroy method
     */
    public function destroy(Request $request, $id)
    {
        $payment = Payment::where('id', $id)->where('sl_no', $request->sl_no)->first();

        if (!$payment) {
            return redirect()->back()->with('error', 'Payment not found.');
        }

        $payments = Payment::where('sl_no', $request->sl_no)->get();

        if ($payments->isEmpty()) {
            return redirect()->back()->with('error', 'Payments not found.');
        }

        try {
            DB::transaction(function () use ($request, $payments, $payment) {
                // Delete approval logs
                $log = BankLetterApprovalLog::where('sl_no', $request->sl_no)->first();
                if ($log) {
                    $log->priorities()->detach();
                    $log->delete();
                }

                // Update PaymentNote status
                if ($payment->paymentNote) {
                    $payment->paymentNote->update(['status' => 'A']);
                }

                // Delete all payments
                foreach ($payments as $pay) {
                    $pay->forceDelete();
                }
            });

            return redirect()->back()->with('success', 'Deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to delete payment: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong.');
        }
    }

    // ... (Other methods remain the same but can be optimized similarly)
}
