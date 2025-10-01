<?php

namespace App\Http\Controllers\Backend\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\BankLetterApprovalLog;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\GreenNote;
use App\Models\Payment;
use App\Models\PaymentNote;
use App\Models\ReimbursementNote;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Collection;

class OptimizedDashboardController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Optimized dashboard index with caching and efficient queries
     */
    public function index(Request $request)
    {
        $authId = Auth::id();
        $userRoles = auth()->user()->getRoleNames();
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        // Cache key based on user and date
        $cacheKey = "dashboard_data_{$authId}_" . now()->format('Y-m-d-H');
        
        // Check if user has admin permissions
        $isAdmin = $authId === 1 || 
                   $userRoles->contains('PN User') || 
                   auth()->user()->hasAnyPermission(['all-reimbursement-note', 'all-note', 'all-payment-note']);

        // Get cached data or generate new data
        $dashboardData = Cache::remember($cacheKey, 300, function () use ($authId, $startOfMonth, $endOfMonth, $isAdmin) {
            return $this->getDashboardData($authId, $startOfMonth, $endOfMonth, $isAdmin);
        });

        return view('backend.dashboard.index', $dashboardData);
    }

    /**
     * Get optimized dashboard data with efficient queries
     */
    private function getDashboardData($authId, $startOfMonth, $endOfMonth, $isAdmin)
    {
        $initialCounts = [
            'Draft' => 0,
            'Pending for Approval' => 0,
            'Approved' => 0,
            'Rejected' => 0,
            'Payment Process' => 0,
            'Sent for PMC' => 0,
            'RTGS/NEFT Created' => 0,
            'Payment Note Approved' => 0,
            'Payment Approved' => 0,
            'Paid' => 0,
        ];

        if ($isAdmin) {
            // Admin data - get all records
            $allData = $this->getOptimizedGreenNoteCounts($initialCounts);
            $monthlyData = $this->getOptimizedGreenNoteCounts($initialCounts, $startOfMonth, $endOfMonth);
            $tillDateReimbursementData = $this->getOptimizedReimbursementCounts(null, null, $authId);
            $monthlyReimbursementData = $this->getOptimizedReimbursementCounts($startOfMonth, $endOfMonth, $authId);
            $currentMonthPaymentCounts = $this->getOptimizedPaymentCounts($startOfMonth, $endOfMonth);
            $tillDatePaymentCounts = $this->getOptimizedPaymentCounts();
            $bankCounts = $this->getOptimizedBankCounts();
        } else {
            // User-specific data
            $allData = $this->getFilteredGreenNoteCounts($initialCounts, $authId);
            $monthlyData = $this->getFilteredGreenNoteCounts($initialCounts, $authId, $startOfMonth, $endOfMonth);
            $tillDateReimbursementData = $this->getOptimizedReimbursementCounts(null, null, $authId);
            $monthlyReimbursementData = $this->getOptimizedReimbursementCounts($startOfMonth, $endOfMonth, $authId);
            $currentMonthPaymentCounts = $this->getFilteredPaymentCounts($startOfMonth, $endOfMonth, $authId);
            $tillDatePaymentCounts = $this->getFilteredPaymentCounts(null, null, $authId);
            $bankCounts = $this->getFilteredBankCounts($authId);
        }

        // Cache user approval data separately
        $userData = Cache::remember('user-approval-data', 300, function () {
            return $this->getOptimizedUserApprovalData();
        });

        return [
            'dataTill' => $allData,
            'dataCurrent' => $monthlyData,
            'dataReimbursementTill' => $tillDateReimbursementData,
            'dataReimbursementCurrent' => $monthlyReimbursementData,
            'dataPaymentCurrent' => $currentMonthPaymentCounts,
            'dataPaymentTill' => $tillDatePaymentCounts,
            'currentMonthFormatted' => $bankCounts['current_month'],
            'tillDateFormatted' => $bankCounts['till_date'],
            'userData' => $userData
        ];
    }

    /**
     * Optimized Green Note counts with better query structure
     */
    private function getOptimizedGreenNoteCounts(array $initialCounts, $start = null, $end = null): Collection
    {
        $query = GreenNote::select('id', 'status', 'created_at')
            ->with([
                'paymentNotes:id,green_note_id',
                'paymentNotes.paymentApprovalLogs:id,payment_note_id,status'
            ]);

        if ($start && $end) {
            $query->whereBetween('created_at', [$start, $end]);
        }

        $results = $query->get();
        return $this->processGreenNoteResults($results, $initialCounts);
    }

    /**
     * Optimized filtered Green Note counts for specific user
     */
    private function getFilteredGreenNoteCounts(array $initialCounts, $authId, $start = null, $end = null): Collection
    {
        $query = GreenNote::select('id', 'status', 'created_at', 'user_id')
            ->with([
                'paymentNotes:id,green_note_id',
                'paymentNotes.paymentApprovalLogs:id,payment_note_id,status'
            ])
            ->where(function ($query) use ($authId) {
                $query->whereHas('approvalLogs', function ($q) use ($authId) {
                    $q->whereHas('approvalStep', fn($step) => $step->where('next_on_approve', $authId))
                      ->where('status', 'A');
                })->orWhere('user_id', $authId);
            });

        if ($start && $end) {
            $query->whereBetween('created_at', [$start, $end]);
        }

        $results = $query->get();
        return $this->processGreenNoteResults($results, $initialCounts);
    }

    /**
     * Process Green Note results efficiently
     */
    private function processGreenNoteResults($notes, array $initialCounts): Collection
    {
        $resultsArray = $initialCounts;

        foreach ($notes as $note) {
            $status = $note->status;
            $latestPaymentNote = $note->paymentNotes->last();
            $latestLog = $latestPaymentNote?->paymentApprovalLogs->last();

            if ($status === 'A') {
                if ($latestPaymentNote && $latestLog && $latestLog->status !== 'R') {
                    $resultsArray['Payment Process']++;
                } else {
                    $resultsArray['Approved']++;
                }
            } else {
                $this->incrementStatusCount($resultsArray, $status);
            }
        }

        return collect($resultsArray)->map(fn($value, $key) => ['name' => $key, 'value' => $value])->values();
    }

    /**
     * Increment status count based on status code
     */
    private function incrementStatusCount(array &$resultsArray, string $status): void
    {
        $statusMap = [
            'D' => 'Draft',
            'S' => 'Pending for Approval',
            'B' => 'RTGS/NEFT Created',
            'PNA' => 'Payment Note Approved',
            'PA' => 'Payment Approved',
            'PD' => 'Paid',
            'PMPL' => 'Sent for PMC',
            'R' => 'Rejected',
        ];

        if (isset($statusMap[$status])) {
            $resultsArray[$statusMap[$status]]++;
        }
    }

    /**
     * Optimized Reimbursement counts
     */
    private function getOptimizedReimbursementCounts($startDate = null, $endDate = null, $authId = null): array
    {
        $query = ReimbursementNote::select('id', 'status', 'created_at', 'user_id', 'approver_id');

        if ($authId !== null) {
            $query->where(function ($q) use ($authId) {
                $q->where('user_id', $authId)->orWhere('approver_id', $authId);
            });
        }

        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }

        $notes = $query->get();
        return $this->countReimbursementNotes($notes);
    }

    /**
     * Count reimbursement notes efficiently
     */
    private function countReimbursementNotes(Collection $notes): array
    {
        $statusList = [
            'D' => 'Draft',
            'S' => 'Pending for Approval',
            'A' => 'Approved',
            'R' => 'Reject',
            'B' => 'RTGS/NEFT Created',
            'PNA' => 'Payment Note Approved',
            'PA' => 'Payment Approved',
            'PD' => 'Paid',
        ];

        $counts = array_fill_keys(array_values($statusList), 0);
        $counts['Payment Ready'] = 0;

        foreach ($notes as $note) {
            $code = $note->status;
            
            if ($code === 'A') {
                $latestPaymentNote = $note->paymentNote;
                $latestLog = $latestPaymentNote?->paymentApprovalLogs->sortByDesc('created_at')->first();

                if ($latestLog && $latestLog->status === 'D') {
                    $counts['Payment Ready']++;
                } else {
                    $counts['Approved']++;
                }
            } elseif (isset($statusList[$code])) {
                $counts[$statusList[$code]]++;
            }
        }

        return collect($counts)->map(fn($value, $name) => ['name' => $name, 'value' => $value])->values()->toArray();
    }

    /**
     * Optimized Payment counts
     */
    private function getOptimizedPaymentCounts($startDate = null, $endDate = null): array
    {
        $query = PaymentNote::select('id', 'status', 'created_at');

        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }

        return $this->aggregateStatusCounts($query);
    }

    /**
     * Filtered Payment counts for specific user
     */
    private function getFilteredPaymentCounts($startDate = null, $endDate = null, $authId = null): array
    {
        $query = PaymentNote::select('id', 'status', 'created_at', 'user_id')
            ->where(function ($q) use ($authId) {
                $q->whereHas('paymentApprovalLogs.logPriorities.priority', function ($priority) use ($authId) {
                    $priority->where('reviewer_id', $authId);
                })->orWhere('user_id', $authId);
            });

        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }

        return $this->aggregateStatusCounts($query);
    }

    /**
     * Aggregate status counts efficiently
     */
    private function aggregateStatusCounts($query): array
    {
        $statusMap = [
            'D' => 'Draft',
            'S' => 'Pending for Approval',
            'A' => 'Approved',
            'R' => 'Reject',
            'B' => 'RTGS/NEFT Created',
            'PNA' => 'Payment Note Approved',
            'PA' => 'Payment Approved',
            'PD' => 'Paid',
        ];

        $counts = array_fill_keys(array_values($statusMap), 0);

        $query->chunk(1000, function ($chunk) use (&$counts, $statusMap) {
            foreach ($chunk as $note) {
                if (isset($statusMap[$note->status])) {
                    $counts[$statusMap[$note->status]]++;
                }
            }
        });

        return array_map(fn($name, $value) => ['name' => $name, 'value' => $value], array_keys($counts), $counts);
    }

    /**
     * Optimized Bank counts for admin
     */
    private function getOptimizedBankCounts(): array
    {
        $currentMonth = now()->format('Y-m');
        $statusList = [
            'D' => 'Draft',
            'S' => 'Pending for Approval',
            'A' => 'Approved',
            'R' => 'Reject',
            'B' => 'RTGS/NEFT Created',
            'PNA' => 'Payment Note Approved',
            'PA' => 'Payment Approved',
            'PD' => 'Paid',
        ];

        $tillDateCounts = array_fill_keys(array_values($statusList), 0);
        $currentMonthCounts = $tillDateCounts;

        $processedTillSlNos = [];
        $processedCurrentSlNos = [];

        Payment::select('id', 'sl_no', 'status', 'created_at')
            ->orderBy('created_at', 'desc')
            ->chunk(1000, function ($payments) use (&$tillDateCounts, &$currentMonthCounts, &$processedTillSlNos, &$processedCurrentSlNos, $statusList, $currentMonth) {
                foreach ($payments as $payment) {
                    $status = $statusList[$payment->status] ?? null;
                    if (!$status) continue;

                    // Till Date Count (unique sl_no)
                    if (!isset($processedTillSlNos[$payment->sl_no])) {
                        $processedTillSlNos[$payment->sl_no] = true;
                        $tillDateCounts[$status]++;
                    }

                    // Current Month Count (unique sl_no)
                    if ($payment->created_at && $payment->created_at->format('Y-m') === $currentMonth) {
                        if (!isset($processedCurrentSlNos[$payment->sl_no])) {
                            $processedCurrentSlNos[$payment->sl_no] = true;
                            $currentMonthCounts[$status]++;
                        }
                    }
                }
            });

        return [
            'till_date' => collect($tillDateCounts)->map(fn($v, $k) => ['name' => $k, 'value' => $v])->values(),
            'current_month' => collect($currentMonthCounts)->map(fn($v, $k) => ['name' => $k, 'value' => $v])->values(),
        ];
    }

    /**
     * Filtered Bank counts for specific user
     */
    private function getFilteredBankCounts($authId): array
    {
        $currentMonth = now()->format('Y-m');
        $statusList = [
            'D' => 'Draft',
            'S' => 'Pending for Approval',
            'A' => 'Approved',
            'R' => 'Reject',
            'B' => 'RTGS/NEFT Created',
            'PNA' => 'Payment Note Approved',
            'PA' => 'Payment Approved',
            'PD' => 'Paid',
        ];

        $tillDateCounts = array_fill_keys(array_values($statusList), 0);
        $currentMonthCounts = $tillDateCounts;

        $processedTillSlNos = [];
        $processedCurrentSlNos = [];

        // Get user's sl_nos from logs and payments
        $fromLogs = DB::table('bank_letter_log_priority')
            ->join('bank_letter_approval_logs', 'bank_letter_log_priority.bank_letter_approval_log_id', '=', 'bank_letter_approval_logs.id')
            ->join('bank_letter_approval_priorities', 'bank_letter_log_priority.priority_id', '=', 'bank_letter_approval_priorities.id')
            ->where('bank_letter_approval_priorities.reviewer_id', $authId)
            ->pluck('bank_letter_approval_logs.sl_no')
            ->toArray();

        $fromPayments = DB::table('payments_new')
            ->where('user_id', $authId)
            ->pluck('sl_no')
            ->toArray();

        $allSlNos = array_unique(array_merge($fromLogs, $fromPayments));

        Payment::whereIn('sl_no', $allSlNos)
            ->select('id', 'sl_no', 'status', 'created_at')
            ->orderBy('created_at', 'desc')
            ->chunk(1000, function ($payments) use (&$tillDateCounts, &$currentMonthCounts, &$processedTillSlNos, &$processedCurrentSlNos, $statusList, $currentMonth) {
                foreach ($payments as $payment) {
                    $status = $statusList[$payment->status] ?? null;
                    if (!$status) continue;

                    // Till Date Count (unique sl_no)
                    if (!isset($processedTillSlNos[$payment->sl_no])) {
                        $processedTillSlNos[$payment->sl_no] = true;
                        $tillDateCounts[$status]++;
                    }

                    // Current Month Count (unique sl_no)
                    if ($payment->created_at && $payment->created_at->format('Y-m') === $currentMonth) {
                        if (!isset($processedCurrentSlNos[$payment->sl_no])) {
                            $processedCurrentSlNos[$payment->sl_no] = true;
                            $currentMonthCounts[$status]++;
                        }
                    }
                }
            });

        return [
            'till_date' => collect($tillDateCounts)->map(fn($v, $k) => ['name' => $k, 'value' => $v])->values(),
            'current_month' => collect($currentMonthCounts)->map(fn($v, $k) => ['name' => $k, 'value' => $v])->values(),
        ];
    }

    /**
     * Optimized User Approval Data
     */
    private function getOptimizedUserApprovalData(): array
    {
        $users = User::role(['GN Approver', 'ER Approver', 'PN Approver', 'QS'])->get();
        $userData = [];

        foreach ($users as $user) {
            $userId = $user->id;
            
            // Get user's pending items efficiently
            $greenNotes = $this->getUserGreenNotes($userId);
            $paymentNotes = $this->getUserPaymentNotes($userId);
            $reimbursementNotes = $this->getUserReimbursementNotes($userId);
            $bankLetters = $this->getUserBankLetters($userId);

            if ($greenNotes->isEmpty() && $paymentNotes->isEmpty() && $reimbursementNotes->isEmpty() && $bankLetters->isEmpty()) {
                continue;
            }

            $userData[] = [
                'id' => $userId,
                'name' => $user->name,
                'green_statuses' => $this->formatUserStatuses($greenNotes, ['S' => 'Pending for Approval', 'PMPL' => 'Sent for PMC']),
                'green_ids' => $greenNotes->pluck('id')->all(),
                'payment_statuses' => $this->formatUserStatuses($paymentNotes, ['S' => 'Pending for Approval']),
                'payment_ids' => $paymentNotes->pluck('id')->all(),
                'reimbursement_statuses' => $this->formatUserStatuses($reimbursementNotes, ['S' => 'Pending for Approval']),
                'reimbursement_ids' => $reimbursementNotes->pluck('id')->all(),
                'bankLetter_statuses' => $this->formatUserStatuses($bankLetters, ['S' => 'Pending for Approval']),
                'bankLetter_ids' => $bankLetters->pluck('sl_no')->values()->all(),
            ];
        }

        return $userData;
    }

    /**
     * Get user's green notes efficiently
     */
    private function getUserGreenNotes($userId): Collection
    {
        return GreenNote::where(function ($query) use ($userId) {
            $query->where('user_id', $userId)
                  ->where('status', 'PMPL')
                  ->orWhereHas('approvalLogs', function ($q) use ($userId) {
                      $q->whereHas('approvalStep', function ($step) use ($userId) {
                          $step->where('next_on_approve', $userId);
                      });
                  });
        })->get();
    }

    /**
     * Get user's payment notes efficiently
     */
    private function getUserPaymentNotes($userId): Collection
    {
        return PaymentNote::whereHas('paymentApprovalLogs.logPriorities.priority', function ($q) use ($userId) {
            $q->where('reviewer_id', $userId);
        })->where('status', 'S')->get();
    }

    /**
     * Get user's reimbursement notes efficiently
     */
    private function getUserReimbursementNotes($userId): Collection
    {
        return ReimbursementNote::where('approver_id', $userId)
            ->where('status', 'S')
            ->get();
    }

    /**
     * Get user's bank letters efficiently
     */
    private function getUserBankLetters($userId): Collection
    {
        return Payment::whereHas('approvalLogs.logPriorities.priority', function ($q) use ($userId) {
            $q->where('reviewer_id', $userId);
        })->where('status', 'S')
          ->get()
          ->unique('sl_no');
    }

    /**
     * Format user statuses for display
     */
    private function formatUserStatuses(Collection $items, array $statusMap): string
    {
        if ($items->isEmpty()) {
            return '-';
        }

        return $items->groupBy('status')
            ->map(function ($group, $status) use ($statusMap) {
                $label = $statusMap[$status] ?? $status;
                return "$label (" . $group->count() . ')';
            })
            ->values()
            ->implode(', ');
    }

    /**
     * Filter method for specific user
     */
    public function filter($id)
    {
        try {
            $user = User::find($id);
            if (!$user) {
                return redirect()->back()->with('error', 'User not found.');
            }

            $greenNotes = $this->getUserGreenNotes($id);
            $paymentNotes = $this->getUserPaymentNotes($id);
            $reimbursementNotes = $this->getUserReimbursementNotes($id);

            $userData = [
                'id' => $user->id,
                'name' => $user->name,
                'green_statuses' => $this->formatUserStatuses($greenNotes, [
                    'D' => 'Draft',
                    'S' => 'Pending for Approval',
                    'A' => 'Approved',
                    'P' => 'Pending',
                    'R' => 'Rejected',
                    'PMPL' => 'Sent for PMC',
                    'B' => 'RTGS/NEFT Created',
                    'PNA' => 'Payment Note Approved',
                    'PA' => 'Payment Approved',
                    'PD' => 'Paid',
                ]),
                'payment_statuses' => $this->formatUserStatuses($paymentNotes, [
                    'D' => 'Draft',
                    'S' => 'Pending for Approval',
                    'A' => 'Approved',
                    'P' => 'Pending',
                    'R' => 'Rejected',
                    'B' => 'RTGS/NEFT Created',
                    'PNA' => 'Payment Note Approved',
                    'PA' => 'Payment Approved',
                    'PD' => 'Paid',
                ]),
                'reimbursement_statuses' => $this->formatUserStatuses($reimbursementNotes, [
                    'D' => 'Draft',
                    'S' => 'Pending for Approval',
                    'A' => 'Approved',
                    'B' => 'RTGS/NEFT Created',
                    'PNA' => 'Payment Note Approved',
                    'PA' => 'Payment Approved',
                    'PD' => 'Paid',
                ]),
            ];

            return view('backend.dashboard.show', ['userData' => $userData]);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }

    /**
     * Show user notes
     */
    public function showUserNotes(Request $request)
    {
        $ids = explode(',', $request->get('ids', ''));
        $notes = GreenNote::whereIn('id', $ids)->get();
        return view('backend.dashboard.allGreenNote', compact('notes'));
    }

    /**
     * Show payment notes
     */
    public function showPaymentNotes(Request $request)
    {
        $ids = explode(',', $request->get('ids', ''));
        $notes = PaymentNote::whereIn('id', $ids)->get();
        return view('backend.dashboard.allPaymentNote', compact('notes'));
    }

    /**
     * Show reimbursement notes
     */
    public function showReimbursementNotes(Request $request)
    {
        $ids = explode(',', $request->get('ids', ''));
        $notes = ReimbursementNote::whereIn('id', $ids)->get();
        return view('backend.dashboard.allReimbursementNote', compact('notes'));
    }

    /**
     * Show bank letter notes
     */
    public function showBankLetterNotes(Request $request)
    {
        $slNos = explode(',', $request->get('ids', ''));

        if ($request->ajax()) {
            $data = Payment::whereIn('sl_no', $slNos)
                ->groupBy('sl_no')
                ->orderBy('id', 'desc')
                ->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
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
                })
                ->addColumn('shortcut_name', function ($row) {
                    $shortcut = PaymentsShortcut::where('sl_no', $row->sl_no)->first();
                    return $shortcut ? $shortcut->shortcut_name . ' <a href="' . route('backend.payments.shortcut', $shortcut->id) . '" class="btn btn-outline-info btn-xs"><i class="bi bi-share"></i></a>' : '';
                })
                ->addColumn('status', function ($row) {
                    $labels = [
                        'D' => '<span class="badge bg-dark">Draft</span>',
                        'P' => '<span class="badge bg-warning">Pending</span>',
                        'A' => '<span class="badge bg-success">Approved</span>',
                        'R' => '<span class="badge bg-danger">Rejected</span>',
                        'S' => '<span class="badge bg-secondary">Sent for Approval</span>',
                        '' => '<span class="badge bg-secondary">N/A</span>',
                    ];

                    $status = $labels[$row->status] ?? $row->status;
                    $approvers = BankLetterApprovalLog::with('logPriorities.priority.user')->where('sl_no', $row->sl_no)->get();

                    $nextApprover = '';
                    if ($approvers->last()?->logPriorities->last()?->priority) {
                        $nextApprover .= '<div class="mt-2 text-start"><strong>Next Approver:</strong> ';
                        foreach ($approvers->last()->logPriorities as $log) {
                            $nextApprover .= $log->priority->user->name . ', ';
                        }
                        $nextApprover = rtrim($nextApprover, ', ') . '</div>';
                    }

                    return $status . $nextApprover;
                })
                ->rawColumns(['action', 'shortcut_name', 'status'])
                ->make(true);
        }

        $notes = Payment::whereIn('sl_no', $slNos)->groupBy('sl_no')->get();
        return view('backend.dashboard.allBankLetterNote', compact('notes'));
    }
}
