<?php

namespace App\Http\Controllers\Backend\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\BankLetterApprovalLog;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\GreenNote;
use App\Models\Payment;
use App\Models\PaymentNote;
use App\Models\PaymentsShortcut;
use App\Models\ReimbursementNote;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use App\Services\RoleService;
use Yajra\DataTables\Contracts\DataTable;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Collection;

class DashboardController extends Controller
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
    private function getGreenNoteCounts(array $initialCounts, $start = null, $end = null): Collection
    {
        $resultsArray = $initialCounts;

        GreenNote::select('id', 'status', 'created_at')
            ->with([
                'paymentNotes' => fn($q) => $q->select('id', 'green_note_id')->latest(),
                'paymentNotes.paymentApprovalLogs' => fn($q) => $q->select('id', 'payment_note_id', 'status')->latest(),
            ])
            ->when($start && $end, fn($query) => $query->whereBetween('created_at', [$start, $end]))
            ->chunk(1000, function ($chunk) use (&$resultsArray) {
                $this->processChunk($chunk, $resultsArray);
            });

        return collect($resultsArray)->map(fn($value, $key) => ['name' => $key, 'value' => $value])->values();
    }

    private function getFilteredGreenNoteCounts(array $initialCounts, $authId, $start = null, $end = null): Collection
    {
        $resultsArray = $initialCounts;

        GreenNote::select('id', 'status', 'created_at', 'user_id')
            ->with([
                'paymentNotes' => fn($q) => $q->select('id', 'green_note_id')->latest(),
                'paymentNotes.paymentApprovalLogs' => fn($q) => $q->select('id', 'payment_note_id', 'status')->latest(),
            ])
            ->when($start && $end, fn($query) => $query->whereBetween('created_at', [$start, $end]))
            ->where(function ($query) use ($authId) {
                $query
                    ->whereHas('approvalLogs', function ($q) use ($authId) {
                        $q->whereHas('approvalStep', fn($step) => $step->where('next_on_approve', $authId))->where('status', 'A');
                    })
                    ->orWhere('user_id', $authId);
            })
            ->chunk(1000, function ($chunk) use (&$resultsArray) {
                $this->processChunk($chunk, $resultsArray);
            });

        return collect($resultsArray)->map(fn($value, $key) => ['name' => $key, 'value' => $value])->values();
    }

    private function processChunk($notes, array &$resultsArray)
    {
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
            } elseif ($status === 'B') {
                $resultsArray['RTGS/NEFT Created']++;
            } elseif ($status === 'PNA') {
                $resultsArray['Payment Note Approved']++;
            } elseif ($status === 'PA') {
                $resultsArray['Payment Approved']++;
            } elseif ($status === 'PD') {
                $resultsArray['Paid']++;
            } else {
                switch ($status) {
                    case 'D':
                        $resultsArray['Draft']++;
                        break;
                    case 'S':
                        $resultsArray['Pending for Approval']++;
                        break;
                    case 'PMPL':
                        $resultsArray['Sent for PMC']++;
                        break;
                    case 'R':
                        $resultsArray['Rejected']++;
                        break;
                }
            }
        }
    }

    private function countReimbursementNotes(Collection $notes, array $statusList): array
    {
        $counts = [
            'Draft' => 0,
            'Pending for Approval' => 0,
            'Approved' => 0,
            'Reject' => 0,
            'Payment Ready' => 0,
            'RTGS/NEFT Created' => 0,
            'Payment Note Approved' => 0,
            'Payment Approved' => 0,
            'Paid' => 0,
        ];

        foreach ($notes as $note) {
            $code = $note->status;
            $label = $statusList[$code] ?? null;

            if ($code === 'A') {
                $latestPaymentNote = $note->paymentNote->sortByDesc('created_at')->first();
                $latestLog = $latestPaymentNote?->paymentApprovalLogs->sortByDesc('created_at')->first();

                if ($latestLog && $latestLog->status === 'D') {
                    $counts['Payment Ready']++;
                } else {
                    $counts['Approved']++;
                }
            } elseif ($label && isset($counts[$label])) {
                $counts[$label]++;
            }
        }

        // ✅ Convert to chart format
        return collect($counts)->map(fn($value, $name) => ['name' => $name, 'value' => $value])->values()->toArray();
    }

    private function formatStatusCounts(Collection $notes): array
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

        foreach ($notes as $note) {
            $statusLabel = $statusMap[$note->status] ?? null;
            if ($statusLabel) {
                $counts[$statusLabel]++;
            }
        }

        return array_map(fn($name, $value) => ['name' => $name, 'value' => $value], array_keys($counts), $counts);
    }
    // private function getBankCounts($authId)
    // {
    //     $allPayments = Payment::with('bankLetterApprovalLogs.logPriorities.priority')
    //         ->get()
    //         ->filter(function ($payment) use ($authId) {
    //             $logPriorities = $payment->bankLetterApprovalLogs->last()?->logPriorities;
    //             return $logPriorities && $logPriorities->contains(fn($log) => optional($log->priority)->reviewer_id == $authId);
    //         })
    //         ->where('status', 'S')
    //         ->unique('sl_no');

    //     // Current month filter
    //     $currentMonth = Carbon::now()->format('Y-m');

    //     // Split till date and current month
    //     $tillDateNotes = $allPayments;
    //     $currentMonthNotes = $allPayments->filter(function ($note) use ($currentMonth) {
    //         return optional($note->created_at)->format('Y-m') === $currentMonth;
    //     });

    //     $statusList = [
    //         'D' => 'Draft',
    //         'S' => 'Pending for Approval',
    //         'A' => 'Approved',
    //         'R' => 'Reject',
    //         'B' => 'RTGS/NEFT Created',
    //         'PNA' => 'Payment Note Approved',
    //         'PA' => 'Payment Approved',
    //         'PD' => 'Paid',
    //     ];

    //     // Initialize counters
    //     $tillDateBankCounts = [
    //         'Draft' => 0,
    //         'Pending for Approval' => 0,
    //         'Approved' => 0,
    //         'Reject' => 0,
    //         'Payment Ready' => 0,
    //         'RTGS/NEFT Created' => 0,
    //         'Payment Note Approved' => 0,
    //         'Payment Approved' => 0,
    //         'Paid' => 0,
    //     ];

    //     $currentMonthBankCounts = $tillDateBankCounts;

    //     // Count unique sl_no for Till Date
    //     $tillGrouped = $tillDateNotes->groupBy('sl_no')->map->last();
    //     foreach ($tillGrouped as $note) {
    //         $status = $statusList[$note->status] ?? null;
    //         if ($status && isset($tillDateBankCounts[$status])) {
    //             $tillDateBankCounts[$status]++;
    //         }
    //     }

    //     // Count unique sl_no for Current Month
    //     $currentGrouped = $currentMonthNotes->groupBy('sl_no')->map->last();
    //     foreach ($currentGrouped as $note) {
    //         $status = $statusList[$note->status] ?? null;
    //         if ($status && isset($currentMonthBankCounts[$status])) {
    //             $currentMonthBankCounts[$status]++;
    //         }
    //     }
    //     $tillDateFormatted = collect($tillDateBankCounts)
    //         ->map(function ($value, $key) {
    //             return ['name' => $key, 'value' => $value];
    //         })
    //         ->values();

    //     $currentMonthFormatted = collect($currentMonthBankCounts)
    //         ->map(function ($value, $key) {
    //             return ['name' => $key, 'value' => $value];
    //         })
    //         ->values();
    //     return [
    //         'till_date' => $tillDateFormatted,
    //         'current_month' => $currentMonthFormatted,
    //     ];
    // }
    private function getBankCounts($authId): array
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

        $tillDateBankCounts = array_fill_keys(array_values($statusList), 0);
        $currentMonthBankCounts = $tillDateBankCounts;

        $processedSlNosTill = [];
        $processedSlNosMonth = [];

        Payment::with('bankLetterApprovalLogs.logPriorities.priority')
            ->where('status', 'S')
            ->chunk(1000, function ($chunk) use (&$tillDateBankCounts, &$currentMonthBankCounts, &$processedSlNosTill, &$processedSlNosMonth, $statusList, $currentMonth, $authId) {
                foreach ($chunk as $payment) {
                    $logPriorities = $payment->bankLetterApprovalLogs->last()?->logPriorities;
                    if (!$logPriorities) {
                        continue;
                    }

                    $isReviewer = $logPriorities->contains(fn($log) => optional($log->priority)->reviewer_id === $authId);
                    if (!$isReviewer) {
                        continue;
                    }

                    $slNo = $payment->sl_no;
                    $statusLabel = $statusList[$payment->status] ?? null;
                    if (!$statusLabel) {
                        continue;
                    }

                    // Till Date Count (unique sl_no)
                    if (!isset($processedSlNosTill[$slNo])) {
                        $processedSlNosTill[$slNo] = true;
                        $tillDateBankCounts[$statusLabel]++;
                    }

                    // Current Month Count (unique sl_no)
                    if ($payment->created_at && $payment->created_at->format('Y-m') === $currentMonth) {
                        if (!isset($processedSlNosMonth[$slNo])) {
                            $processedSlNosMonth[$slNo] = true;
                            $currentMonthBankCounts[$statusLabel]++;
                        }
                    }
                }
            });

        return [
            'till_date' => collect($tillDateBankCounts)->map(fn($v, $k) => ['name' => $k, 'value' => $v])->values(),
            'current_month' => collect($currentMonthBankCounts)->map(fn($v, $k) => ['name' => $k, 'value' => $v])->values(),
        ];
    }

    private function getBankLetterStatusCounts($authId = null): array
    {
        // Define statuses and initialize count structure
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

        $initCounts = [
            'Draft' => 0,
            'Pending for Approval' => 0,
            'Approved' => 0,
            'Reject' => 0,
            'Payment Ready' => 0,
            'RTGS/NEFT Created' => 0,
            'Payment Note Approved' => 0,
            'Payment Approved' => 0,
            'Paid' => 0,
        ];

        // Fetch sl_no based on user role
        if ($authId) {
            $fromLogs = DB::table('bank_letter_log_priority')->join('bank_letter_approval_logs', 'bank_letter_log_priority.bank_letter_approval_log_id', '=', 'bank_letter_approval_logs.id')->join('bank_letter_approval_priorities', 'bank_letter_log_priority.priority_id', '=', 'bank_letter_approval_priorities.id')->where('bank_letter_approval_priorities.reviewer_id', $authId)->pluck('bank_letter_approval_logs.sl_no')->toArray();

            // Get sl_no from payments where user_id = user
            $fromPayments = DB::table('payments_new')->where('user_id', $authId)->pluck('sl_no')->toArray();

            // Merge and get unique sl_no
            // $allSlNos = array_unique(array_merge($fromLogs, $fromPayments));

            $slNos = array_unique(array_merge($fromLogs, $fromPayments));
        } else {
            // Admin: fetch all unique sl_no
            $slNos = Payment::select('sl_no')->groupBy('sl_no')->pluck('sl_no')->toArray();
        }

        // Load all payment records for those sl_no
        $allPayments = Payment::whereIn('sl_no', $slNos)->get();

        // Date filter
        $currentMonth = Carbon::now()->format('Y-m');
        $currentMonthNotes = $allPayments->filter(fn($note) => optional($note->created_at)->format('Y-m') === $currentMonth);

        // Initialize count arrays
        $tillDateCounts = $initCounts;
        $currentMonthCounts = $initCounts;

        // Group latest per sl_no
        $tillGrouped = $allPayments->groupBy('sl_no')->map->last();
        foreach ($tillGrouped as $note) {
            $status = $statusList[$note->status] ?? null;
            if ($status && isset($tillDateCounts[$status])) {
                $tillDateCounts[$status]++;
            }
        }

        $currentGrouped = $currentMonthNotes->groupBy('sl_no')->map->last();
        foreach ($currentGrouped as $note) {
            $status = $statusList[$note->status] ?? null;
            if ($status && isset($currentMonthCounts[$status])) {
                $currentMonthCounts[$status]++;
            }
        }

        // Format for chart/data usage
        $tillDateFormatted = collect($tillDateCounts)->map(fn($value, $key) => ['name' => $key, 'value' => $value])->values();

        $currentMonthFormatted = collect($currentMonthCounts)->map(fn($value, $key) => ['name' => $key, 'value' => $value])->values();

        return [
            'till_date' => $tillDateFormatted,
            'current_month' => $currentMonthFormatted,
        ];
    }
    // private function getBankLetterStatusAdminCounts()
    // {
    //     $startOfMonth = Carbon::now()->startOfMonth();
    //     $endOfMonth = Carbon::now()->endOfMonth();
    //     $baseQuery = PaymentNote::with('paymentApprovalLogs.logPriorities.priority');

    //     // Separate variables for Current Month and Till Date
    //     $currentMonthPaymentNotes = (clone $baseQuery)->whereBetween('created_at', [$startOfMonth, $endOfMonth])->get();

    //     $tillDatePaymentNotes = $baseQuery->get();

    //     // payment bank
    //     // $slNos = Payment::select('sl_no')->groupBy('sl_no')->pluck('sl_no');
    //     $slNos = Payment::select('sl_no')->groupBy('sl_no')->pluck('sl_no');

    //     // Fetch all matching payments
    //     $allPayments = Payment::whereIn('sl_no', $slNos)->get();

    //     // Current month filter
    //     $currentMonth = Carbon::now()->format('Y-m');

    //     // Split till date and current month
    //     $tillDateNotes = $allPayments;
    //     $currentMonthNotes = $allPayments->filter(function ($note) use ($currentMonth) {
    //         return optional($note->created_at)->format('Y-m') === $currentMonth;
    //     });

    //     $statusList = [
    //         'D' => 'Draft',
    //         'S' => 'Pending for Approval',
    //         'A' => 'Approved',
    //         'R' => 'Reject',
    //         'B' => 'RTGS/NEFT Created',
    //         'PNA' => 'Payment Note Approved',
    //         'PA' => 'Payment Approved',
    //         'PD' => 'Paid',
    //     ];

    //     // Initialize counters
    //     $tillDateBankCounts = [
    //         'Draft' => 0,
    //         'Pending for Approval' => 0,
    //         'Approved' => 0,
    //         'Reject' => 0,
    //         'Payment Ready' => 0,
    //         'RTGS/NEFT Created' => 0,
    //         'Payment Note Approved' => 0,
    //         'Payment Approved' => 0,
    //         'Paid' => 0,
    //     ];

    //     $currentMonthBankCounts = $tillDateBankCounts;

    //     // Count unique sl_no for Till Date
    //     $tillGrouped = $tillDateNotes->groupBy('sl_no')->map->last();
    //     foreach ($tillGrouped as $note) {
    //         $status = $statusList[$note->status] ?? null;
    //         if ($status && isset($tillDateBankCounts[$status])) {
    //             $tillDateBankCounts[$status]++;
    //         }
    //     }

    //     // Count unique sl_no for Current Month
    //     $currentGrouped = $currentMonthNotes->groupBy('sl_no')->map->last();
    //     foreach ($currentGrouped as $note) {
    //         $status = $statusList[$note->status] ?? null;
    //         if ($status && isset($currentMonthBankCounts[$status])) {
    //             $currentMonthBankCounts[$status]++;
    //         }
    //     }
    //     $tillDateFormatted = collect($tillDateBankCounts)
    //         ->map(function ($value, $key) {
    //             return ['name' => $key, 'value' => $value];
    //         })
    //         ->values();

    //     $currentMonthFormatted = collect($currentMonthBankCounts)
    //         ->map(function ($value, $key) {
    //             return ['name' => $key, 'value' => $value];
    //         })
    //         ->values();
    //     return [
    //         'till_date' => $tillDateFormatted,
    //         'current_month' => $currentMonthFormatted,
    //     ];
    // }
    private function getBankLetterStatusAdminCounts(): array
    {
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();
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

        // Initialize counts
        $tillDateCounts = array_fill_keys(array_values($statusList), 0);
        $currentMonthCounts = $tillDateCounts;

        $processedTillSlNos = [];
        $processedCurrentSlNos = [];

        Payment::select('id', 'sl_no', 'status', 'created_at')
            ->orderBy('created_at', 'desc')
            ->chunk(1000, function ($payments) use (&$tillDateCounts, &$currentMonthCounts, &$processedTillSlNos, &$processedCurrentSlNos, $statusList, $currentMonth) {
                foreach ($payments as $payment) {
                    $status = $statusList[$payment->status] ?? null;
                    if (!$status) {
                        continue;
                    }

                    // Unique Till Date
                    if (!isset($processedTillSlNos[$payment->sl_no])) {
                        $processedTillSlNos[$payment->sl_no] = true;
                        $tillDateCounts[$status]++;
                    }

                    // Unique Current Month
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

    private function getChunkedReimbursementCounts(?Carbon $startDate, ?Carbon $endDate, array $statusList, int $authId = null): array
    {
        $counts = collect([
            'Draft' => 0,
            'Pending for Approval' => 0,
            'Approved' => 0,
            'Reject' => 0,
            'Payment Ready' => 0,
            'RTGS/NEFT Created' => 0,
            'Payment Note Approved' => 0,
            'Payment Approved' => 0,
            'Paid' => 0,
        ]);

        // ✅ Query base
        $query = ReimbursementNote::orderBy('created_at', 'desc');
        // ->where('status', '!=', 'D');

        // ✅ Only apply user filter if authId is not null
        if ($authId !== null) {
            $query->where(function ($q) use ($authId) {
                $q->where('user_id', $authId)->orWhere('approver_id', $authId);
            });
        }

        // ✅ Apply date range filter if provided
        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }

        // ✅ Process in chunks
        $query->chunk(1000, function ($chunk) use (&$counts, $statusList) {
            $chunkCounts = $this->countReimbursementNotes($chunk, $statusList, false);
            foreach ($chunkCounts as $item) {
                $key = $item['name']; // key label
                $value = $item['value']; // numeric count
                if (isset($counts[$key])) {
                    $counts[$key] += $value;
                }
            }
        });

        return $counts->map(fn($value, $name) => ['name' => $name, 'value' => $value])->values()->toArray();
    }

    private function getUserApprovalData(): array
    {
        // Use dynamic role service to get users with approval roles
        $users = \App\Services\RoleService::getUsersWithApprovalRoles();

        $statusLabels = [
            'S' => 'Pending for Approval',
            'PMPL' => 'Sent for PMC',
        ];

        $userData = [];
        $userIds = $users->pluck('id')->all();

        $paymentStatusMap = [
            'S' => 'Pending for Approval',
        ];
        $reimbursementStatusMap = [
            'S' => 'Pending for Approval',
        ];
        /**
         * 🟢 Process GreenNotes in chunks
         */
        $greenPerUser = [];
        GreenNote::with(['approvalLogs.approvalStep'])
            ->whereNotIn('status', ['A', 'B', 'PNA', 'PA', 'PD'])
            ->orWhere(function ($q) use ($userIds) {
                $q->where('status', 'PMPL')->whereIn('user_id', $userIds);
            })
            ->chunk(1000, function ($chunk) use (&$greenPerUser, $userIds) {
                foreach ($chunk as $note) {
                    // PMPL notes directly mapped by user_id
                    if ($note->status === 'PMPL' && in_array($note->user_id, $userIds)) {
                        $greenPerUser[$note->user_id][] = $note;
                        continue;
                    }

                    // Other green notes: check approvalLogs
                    $lastLog = $note->approvalLogs->last();
                    if ($lastLog && $lastLog->status === 'A') {
                        $approvalStep = $lastLog->approvalStep;
                        if ($approvalStep && in_array($approvalStep->next_on_approve, $userIds)) {
                            $greenPerUser[$approvalStep->next_on_approve][] = $note;
                        }
                    }
                }
            });

        /**
         * 🟣 Process PaymentNotes in chunks
         */
        $paymentPerUser = [];

        // PaymentNote::with(['paymentApprovalLogs.logPriorities.priority'])
        //     ->chunk(1000, function ($chunk) use (&$paymentPerUser) {
        //         foreach ($chunk as $note) {
        //             $approvalLogs = $note->paymentApprovalLogs;
        //             if ($approvalLogs->isEmpty()) continue;
        //             foreach ($approvalLogs as $approvalLog) {
        //                 if (!$approvalLog->logPriorities) continue;

        //                 foreach ($approvalLog->logPriorities as $log) {
        //                     $priority = $log->priority;
        //                     if ($priority && $priority->reviewer_id) {
        //                         $paymentPerUser[$priority->reviewer_id][] = $note;
        //                     }
        //                 }
        //             }
        //         }
        //     });
        PaymentNote::with(['paymentApprovalLogs.logPriorities.priority'])->chunk(1000, function ($chunk) use (&$paymentPerUser) {
            foreach ($chunk as $note) {
                // 🔍 Get latest approval log based on created_at or ID
                $latestLog = $note->paymentApprovalLogs->sortByDesc('created_at')->first(); // Or use sortByDesc('id') if you trust id order

                if (!$latestLog || !$latestLog->logPriorities) {
                    continue;
                }

                foreach ($latestLog->logPriorities as $log) {
                    $priority = $log->priority;
                    if ($priority && $priority->reviewer_id) {
                        $paymentPerUser[$priority->reviewer_id][] = $note;
                    }
                }
            }
        });

        // dd($paymentPerUser);

        // 🔵 ReimbursementNotes

        $reimbursementPerUser = [];

        ReimbursementNote::where('status', 'S')
            ->whereIn('approver_id', $userIds)
            ->chunk(1000, function ($chunk) use (&$reimbursementPerUser) {
                foreach ($chunk as $note) {
                    $approverId = $note->approver_id;
                    if ($approverId) {
                        $reimbursementPerUser[$approverId][] = $note;
                    }
                }
            });

        // 🟣 Bank Letters (latest status per sl_no)

        $bankLetterPerUser = [];
        Payment::with(['approvalLogs.logPriorities.priority.user'])
            ->where('status', 'S')
            ->chunk(1000, function ($chunk) use (&$bankLetterPerUser) {
                foreach ($chunk as $payment) {
                    $approvalLogs = $payment->approvalLogs;

                    if ($approvalLogs->isEmpty()) {
                        continue;
                    }

                    foreach ($approvalLogs as $approvalLog) {
                        if (!$approvalLog->logPriorities) {
                            continue;
                        }

                        foreach ($approvalLog->logPriorities as $log) {
                            $priority = $log->priority;
                            if ($priority && $priority->reviewer_id) {
                                $bankLetterPerUser[$priority->reviewer_id][$payment->sl_no] = $payment;
                            }
                        }
                    }
                }
            });

        foreach ($users as $user) {
            $userId = $user->id;

            // 🟢 Green Notes
            $greenNotes = collect($greenPerUser[$userId] ?? []);
            $greenGrouped = $greenNotes->isEmpty()
                ? '-'
                : $greenNotes
                    ->groupBy('status')
                    ->map(function ($group, $status) use ($statusLabels) {
                        return ($statusLabels[$status] ?? $status) . ' (' . $group->count() . ')';
                    })
                    ->values()
                    ->implode(', ');

            // 🟠 Payment Notes
            $paymentNotes = collect($paymentPerUser[$userId] ?? [])->filter(function ($note) {
                return $note->status === 'S';
            });

            $paymentGrouped = $paymentNotes->isEmpty()
                ? '-'
                : $paymentNotes
                    ->groupBy('status')
                    ->map(function ($group, $status) use ($paymentStatusMap) {
                        return ($paymentStatusMap[$status] ?? $status) . ' (' . $group->count() . ')';
                    })
                    ->values()
                    ->implode(', ');

            // 🔵 Reimbursement Notes
            $reimbursementNotes = collect($reimbursementPerUser[$userId] ?? []);
            $reimbursementGrouped = $reimbursementNotes->isEmpty()
                ? '-'
                : $reimbursementNotes
                    ->groupBy('status')
                    ->map(function ($group, $status) use ($reimbursementStatusMap) {
                        return ($reimbursementStatusMap[$status] ?? $status) . ' (' . $group->count() . ')';
                    })
                    ->values()
                    ->implode(', ');

            // 🟣 Bank Letters
            $pendingPayments = collect($bankLetterPerUser[$userId] ?? [])->unique('sl_no');
            $bankLetterIds = $pendingPayments->pluck('sl_no')->values()->all();
            $bankLetterGrouped = $pendingPayments->isEmpty()
                ? '-'
                : $pendingPayments
                    ->groupBy('status')
                    ->map(function ($group, $status) use ($paymentStatusMap) {
                        $label = $paymentStatusMap[$status] ?? $status;
                        return "$label (" . $group->count() . ')';
                    })
                    ->values()
                    ->implode(', ');

            if ($greenNotes->isEmpty() && $paymentNotes->isEmpty() && $reimbursementNotes->isEmpty() && $pendingPayments->isEmpty()) {
                continue;
            }
            // ✅ Always add user entry — even if all sections empty
            $userData[] = [
                'id' => $userId,
                'name' => $user->name,
                'green_statuses' => $greenGrouped,
                'green_ids' => $greenNotes->pluck('id')->all(),
                'payment_statuses' => $paymentGrouped,
                'payment_ids' => $paymentNotes->pluck('id')->all(),
                'reimbursement_statuses' => $reimbursementGrouped,
                'reimbursement_ids' => $reimbursementNotes->pluck('id')->all(),
                'bankLetter_statuses' => $bankLetterGrouped,
                'bankLetter_ids' => $bankLetterIds,
            ];
        }

        return $userData;
    }
    private function aggregateChunkedStatusCounts($query): array
    {
        static $statusMap = [
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

        $query->select(['id', 'status'])->chunk(1000, function ($chunk) use (&$counts, $statusMap) {
            foreach ($chunk as $note) {
                if (isset($statusMap[$note->status])) {
                    $counts[$statusMap[$note->status]]++;
                }
            }
        });

        return array_map(fn($name, $value) => ['name' => $name, 'value' => $value], array_keys($counts), $counts);
    }

    public function index(Request $request)
    {
        $authId = Auth::id();
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        $userRoles = auth()->user()->getRoleNames();
        
        // Get current organization for cache key specificity
        $currentOrg = auth()->user()->currentOrganization();
        $orgId = $currentOrg ? $currentOrg->id : 'default';
        
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

        // Default counts
        $baseCounts = array_fill_keys(array_values($statusList), 0);
        $baseCounts['Payment Ready'] = 0;
        if (
            $authId === 1 ||
            $userRoles->contains('PN User') ||
            auth()->user()->hasAnyPermission(['all-reimbursement-note', 'all-note', 'all-payment-note'])
        ) {
            $allData = Cache::remember("dash_admin_org_{$orgId}_gn_all", 180, fn () => $this->getGreenNoteCounts($initialCounts));
            $monthlyData = Cache::remember("dash_admin_org_{$orgId}_gn_month_" . now()->format('Y-m'), 180, fn () => $this->getGreenNoteCounts($initialCounts, $startOfMonth, $endOfMonth));

            $tillDateReimbursementData = Cache::remember("dash_admin_org_{$orgId}_reim_all", 180, fn () => $this->getChunkedReimbursementCounts(null, null, $statusList, null));
            $monthlyReimbursementData = Cache::remember("dash_admin_org_{$orgId}_reim_month_" . now()->format('Y-m'), 180, fn () => $this->getChunkedReimbursementCounts($startOfMonth, $endOfMonth, $statusList, null));

            $currentMonthPaymentCounts = Cache::remember("dash_admin_org_{$orgId}_pay_counts_month_" . now()->format('Y-m'), 180, fn () => $this->aggregateChunkedStatusCounts(PaymentNote::with('paymentApprovalLogs.logPriorities.priority')->whereBetween('created_at', [$startOfMonth, $endOfMonth])));

            $tillDatePaymentCounts = Cache::remember("dash_admin_org_{$orgId}_pay_counts_all", 180, fn () => $this->aggregateChunkedStatusCounts(PaymentNote::with('paymentApprovalLogs.logPriorities.priority')));

            $bankCounts = Cache::remember("dash_admin_org_{$orgId}_bank_counts_" . now()->format('Y-m'), 180, fn () => $this->getBankLetterStatusAdminCounts());

            $tillPaymentDateData = $bankCounts['till_date'];
            $currentPaymentMonthData = $bankCounts['current_month'];
        } else {
            $allData = Cache::remember("dash_user_{$authId}_org_{$orgId}_gn_all", 180, fn () => $this->getFilteredGreenNoteCounts($initialCounts, $authId));
            $monthlyData = Cache::remember("dash_user_{$authId}_org_{$orgId}_gn_month_" . now()->format('Y-m'), 180, fn () => $this->getFilteredGreenNoteCounts($initialCounts, $authId, $startOfMonth, $endOfMonth));

            $tillDateReimbursementData = Cache::remember("dash_user_{$authId}_org_{$orgId}_reim_all", 180, fn () => $this->getChunkedReimbursementCounts(null, null, $statusList, $authId));
            $monthlyReimbursementData = Cache::remember("dash_user_{$authId}_org_{$orgId}_reim_month_" . now()->format('Y-m'), 180, fn () => $this->getChunkedReimbursementCounts($startOfMonth, $endOfMonth, $statusList, $authId));

            $currentMonthPaymentCounts = Cache::remember("dash_user_{$authId}_org_{$orgId}_pay_counts_month_" . now()->format('Y-m'), 180, function () use ($authId, $startOfMonth, $endOfMonth) {
                return $this->aggregateChunkedStatusCounts(
                    PaymentNote::whereHas('paymentApprovalLogs.logPriorities.priority', function ($q) use ($authId) {
                        $q->where('reviewer_id', $authId);
                    })
                        ->with('paymentApprovalLogs.logPriorities.priority')
                        ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
                        ->orWhere('user_id', $authId),
                );
            });

            $tillDatePaymentCounts = Cache::remember("dash_user_{$authId}_org_{$orgId}_pay_counts_all", 180, function () use ($authId) {
                return $this->aggregateChunkedStatusCounts(
                    PaymentNote::whereHas('paymentApprovalLogs.logPriorities.priority', function ($q) use ($authId) {
                        $q->where('reviewer_id', $authId);
                    })
                        ->with('paymentApprovalLogs.logPriorities.priority')
                        ->orWhere('user_id', $authId),
                );
            });

            $bankCounts = Cache::remember("dash_user_{$authId}_org_{$orgId}_bank_counts_" . now()->format('Y-m'), 180, fn () => $this->getBankCounts($authId));

            $tillPaymentDateData = $bankCounts['till_date'];
            $currentPaymentMonthData = $bankCounts['current_month'];
        }

        $userData = Cache::remember("user-approval-data-org_{$orgId}", 300, function () {
            return $this->getUserApprovalData(); // your optimized function
        });
        return view('backend.dashboard.index', ['dataTill' => $allData, 'dataCurrent' => $monthlyData, 'dataReimbursementTill' => $tillDateReimbursementData, 'dataReimbursementCurrent' => $monthlyReimbursementData, 'dataPaymentCurrent' => $currentMonthPaymentCounts, 'dataPaymentTill' => $tillDatePaymentCounts, 'currentMonthFormatted' => $currentPaymentMonthData, 'tillDateFormatted' => $tillPaymentDateData, 'userData' => $userData]);
    }
    public function filter($id)
    {
        try {
            $user = User::find($id);

            $greenStatusMap = [
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
            ];

            $paymentStatusMap = [
                'D' => 'Draft',
                'S' => 'Pending for Approval',
                'A' => 'Approved',
                'P' => 'Pending',
                'R' => 'Rejected',
                'B' => 'RTGS/NEFT Created',
                'PNA' => 'Payment Note Approved',
                'PA' => 'Payment Approved',
                'PD' => 'Paid',
            ];
            $reimbursementStatusMap = [
                'D' => 'Draft',
                'S' => 'Pending for Approval',
                'A' => 'Approved',
                'B' => 'RTGS/NEFT Created',
                'PNA' => 'Payment Note Approved',
                'PA' => 'Payment Approved',
                'PD' => 'Paid',
            ];
            $greenNotes = GreenNote::where(function ($query) use ($id) {
                $query
                    ->where('user_id', $id)
                    ->where('status', 'PMPL')
                    ->orWhereHas('approvalLogs', function ($q) use ($id) {
                        $q->whereHas('approvalStep', function ($q2) use ($id) {
                            $q2->where('next_on_approve', $id);
                        });
                    });
            })->get();

            $greenGrouped = $greenNotes
                ->groupBy('status')
                ->map(function ($group, $status) use ($greenStatusMap) {
                    $label = $greenStatusMap[$status] ?? $status;
                    return "$label (" . $group->count() . ')';
                })
                ->values()
                ->implode(', ');

            // Payment Notes
            $paymentNotes = PaymentNote::where(function ($query) use ($id) {
                $query->where('user_id', $id)->orWhereHas('paymentApprovalLogs.logPriorities.priority', function ($q) use ($id) {
                    $q->where('reviewer_id', $id);
                });
            })->get();

            $paymentGrouped = $paymentNotes
                ->groupBy('status')
                ->map(function ($group, $status) use ($paymentStatusMap) {
                    $label = $paymentStatusMap[$status] ?? $status;
                    return "$label (" . $group->count() . ')';
                })
                ->values()
                ->implode(', ');

            // Reimbursement Notes
            $reimbursementNotes = ReimbursementNote::where(function ($query) use ($id) {
                $query->where('user_id', $id)->orWhere('approver_id', $id);
            })
                ->where('status', '!=', 'R') // if you want to exclude deleted/rejected
                ->get();

            $reimbursementGrouped = $reimbursementNotes
                ->groupBy('status')
                ->map(function ($group, $status) use ($reimbursementStatusMap) {
                    $label = $reimbursementStatusMap[$status] ?? $status;
                    return "$label (" . $group->count() . ')';
                })
                ->values()
                ->implode(', ');

            $userData = [
                'id' => $user->id,
                'name' => $user->name,
                'green_statuses' => $greenGrouped,
                'payment_statuses' => $paymentGrouped,
                'reimbursement_statuses' => $reimbursementGrouped,
            ];

            // dd($userData);
            return view('backend.dashboard.show', ['userData' => $userData]);
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }

    public function showUserNotes(Request $request)
    {
        $ids = explode(',', $request->get('ids', ''));
        $notes = GreenNote::whereIn('id', $ids)->get();

        return view('backend.dashboard.allGreenNote', compact('notes'));
    }
    public function showPaymentNotes(Request $request)
    {
        $ids = explode(',', $request->get('ids', ''));
        $notes = PaymentNote::whereIn('id', $ids)->get();

        return view('backend.dashboard.allPaymentNote', compact('notes'));
    }
    public function showReimbursementNotes(Request $request)
    {
        $ids = explode(',', $request->get('ids', ''));
        $notes = ReimbursementNote::whereIn('id', $ids)->get();
        return view('backend.dashboard.allReimbursementNote', compact('notes'));
    }
    public function showBankLetterNotes(Request $request)
    {
        $slNos = explode(',', $request->get('ids', ''));

        if ($request->ajax()) {
            $data = Payment::whereIn('sl_no', $slNos)->groupBy('sl_no')->orderBy('id', 'desc')->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $btn = '';
                    if (Auth::id() == 1) {
                        $btn =
                            '<form action="' .
                            route('backend.payments.destroy', $row->id) .
                            '" method="post" style="display:inline;">' .
                            csrf_field() .
                            '<input type="hidden" name="_method" value="DELETE">
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
                            '<a href="' .
                            route('backend.payments.editPaymentRequest', $row->sl_no) .
                            '" class="btn btn-outline-primary btn-xs">
                            <i class="bi bi-pencil-square"></i>
                        </a>';
                    }

                    $btn .=
                        '<form action="' .
                        route('backend.templates.templateCommon', $row->template_type) .
                        '" method="post" style="display:inline;">' .
                        csrf_field() .
                        '
                        <input type="hidden" name="slno" value="' .
                        $row->sl_no .
                        '">
                        <button type="submit" class="btn btn-outline-info btn-xs" onclick="return confirm(\'Do you want to preview/generate PDF??\');">
                            <i class="bi bi-eye"></i>
                        </button>
                    </form>';
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
        // dd($slNos, $notes);
        return view('backend.dashboard.allBankLetterNote', compact('notes'));
    }

    // ==================== FAST TAB APIS (AJAX + CACHED) ====================
    public function apiExpense(Request $request)
    {
        $userId = Auth::id();

        $query = GreenNote::select('id', 'user_id', 'status', 'created_at')
            ->where(function ($q) use ($userId) {
                $q->where('user_id', $userId)
                    ->orWhereHas('approvalLogs.approvalStep', function ($qq) use ($userId) {
                        $qq->where('next_on_approve', $userId);
                    });
            })
            ->orderByDesc('created_at');

        return DataTables::eloquent($query)
            ->addIndexColumn()
            ->addColumn('date', function ($row) {
                return optional($row->created_at)->setTimezone('Asia/Kolkata')->format('d/m/Y h:i A');
            })
            ->addColumn('status', function ($row) {
                $labels = [
                    'D' => '<span class="badge bg-dark">Draft</span>',
                    'P' => '<span class="badge bg-warning">Pending</span>',
                    'S' => '<span class="badge bg-secondary">Pending for Approval</span>',
                    'A' => '<span class="badge bg-success">Approved</span>',
                    'R' => '<span class="badge bg-danger">Rejected</span>',
                    'B' => '<span class="badge bg-black">RTGS/NEFT Created</span>',
                ];
                return $labels[$row->status] ?? $row->status;
            })
            ->rawColumns(['status'])
            ->toJson();
    }

    public function apiPaymentNotes(Request $request)
    {
        $userId = Auth::id();

        $query = PaymentNote::select('id', 'user_id', 'status', 'created_at', 'green_note_id', 'reimbursement_note_id', 'net_payable_round_off')
            ->with([
                'greenNote.vendor',
                'greenNote.supplier',
                'reimbursementNote.project',
                'reimbursementNote.selectUser',
                'reimbursementNote.user',
                'paymentApprovalLogs.logPriorities.priority.user',
            ])
            ->where(function ($q) use ($userId) {
                $q->where('user_id', $userId)
                    ->orWhereHas('paymentApprovalLogs.logPriorities.priority', function ($qq) use ($userId) {
                        $qq->where('reviewer_id', (int) $userId);
                    });
            })
            ->orderByDesc('created_at');

        return DataTables::eloquent($query)
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
            ->addColumn('status', function ($row) {
                $statusLabels = [
                    'D' => '<span class="badge bg-dark">Draft</span>',
                    'P' => '<span class="badge bg-warning">Pending</span>',
                    'A' => '<span class="badge bg-success">Approved</span>',
                    'R' => '<span class="badge bg-danger">Rejected</span>',
                    'S' => '<span class="badge bg-secondary">Sent for Approval</span>',
                    'B' => '<span class="badge bg-black">RTGS/NEFT Created</span>',
                ];
                return $statusLabels[$row->status] ?? '-';
            })
            ->addColumn('action', function ($row) {
                $actions = '<a href="' . route('backend.payment-note.show', $row->id) . '"><i class="bi bi-eye"></i></a>';
                if (auth()->id() == $row->user_id && $row->status == 'D') {
                    $actions .= ' | <a href="' . route('backend.payment-note.edit', $row->id) . '"><i class="bi bi-pencil-square"></i></a>';
                }
                return $actions;
            })
            ->rawColumns(['status', 'action'])
            ->toJson();
    }

    public function apiReimbursements(Request $request)
    {
        $userId = Auth::id();

        $query = ReimbursementNote::select('id', 'user_id', 'approver_id', 'status', 'created_at', 'project_id')
            ->with(['project', 'selectUser', 'user', 'expenses'])
            ->where(function ($q) use ($userId) {
                $q->where('user_id', $userId)->orWhere('approver_id', $userId);
            })
            ->orderByDesc('created_at');

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
                return \App\Helpers\Helper::formatIndianNumber($netPayable) ?: '-';
            })
            ->addColumn('date', function ($row) {
                return optional($row->created_at)->setTimezone('Asia/Kolkata')->format('d/m/Y h:i A');
            })
            ->addColumn('status', function ($row) {
                $statusLabels = [
                    'D' => '<span class="badge bg-dark">Draft</span>',
                    'P' => '<span class="badge bg-warning">Pending</span>',
                    'A' => '<span class="badge bg-success">Approved</span>',
                    'R' => '<span class="badge bg-danger">Rejected</span>',
                    'S' => '<span class="badge bg-secondary">Sent for Approval</span>',
                    'B' => '<span class="badge bg-black">RTGS/NEFT Created</span>',
                    'PNA' => '<span class="badge bg-info">Payment Note Approved</span>',
                    'PA' => '<span class="badge bg-black">Payment Approved</span>',
                ];
                return $statusLabels[$row->status] ?? '-';
            })
            ->addColumn('action', function ($row) {
                $actions = '<a href="' . route('backend.reimbursement-note.show', $row->id) . '"><i class="bi bi-eye"></i></a>';
                if (auth()->id() == $row->user_id && auth()->user()->can('edit-reimbursement-note') && $row->status == 'D') {
                    $actions .= ' | <a href="' . route('backend.reimbursement-note.edit', $row->id) . '"><i class="bi bi-pencil-square"></i></a>';
                }
                return $actions;
            })
            ->rawColumns(['status', 'action'])
            ->toJson();
    }

    public function apiBank(Request $request)
    {
        $userId = Auth::id();

        $query = Payment::select('id', 'sl_no', 'user_id', 'status', 'created_at', 'template_type')
            ->with(['bankLetterApprovalLogs.logPriorities.priority.user'])
            ->when($userId !== 1, function ($q) use ($userId) {
                $q->where('user_id', $userId)
                    ->orWhereHas('bankLetterApprovalLogs.logPriorities.priority', function ($qq) use ($userId) {
                        $qq->where('reviewer_id', $userId);
                    });
            })
            ->groupBy('sl_no')
            ->orderByDesc('created_at');

        return DataTables::eloquent($query)
            ->addIndexColumn()
            ->addColumn('date', function ($row) {
                return optional($row->created_at)->setTimezone('Asia/Kolkata')->format('d/m/Y h:i A');
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
                ];
                $status = $labels[$row->status] ?? $row->status;

                $approvers = BankLetterApprovalLog::with('logPriorities.priority.user')->where('sl_no', $row->sl_no)->get();
                $nextApprover = '';
                if ($approvers->last()?->logPriorities->last()?->priority) {
                    $nextApprover .= '<div class="mt-2 text-start"><strong>Next Approver:</strong> ';
                    foreach ($approvers->last()->logPriorities as $log) {
                        $nextApprover .= optional(optional($log->priority)->user)->name . ', ';
                    }
                    $nextApprover = rtrim($nextApprover, ', ') . '</div>';
                }
                return $status . $nextApprover;
            })
            ->addColumn('action', function ($row) {
                $btn = '';
                if (Auth::id() == 1) {
                    $btn = '<form action="' . route('backend.payments.destroy', $row->id) . '" method="post" style="display:inline;">' . csrf_field() . '<input type="hidden" name="_method" value="DELETE"><input type="hidden" name="sl_no" value="' . e($row->sl_no) . '"><button type="submit" class="btn btn-outline-danger btn-xs" onclick="return confirm(\'Do you want to delete this role?\');"><i class="bi bi-trash"></i></button></form> <a href="' . route('backend.payments.editPaymentRequest', $row->sl_no) . '" class="btn btn-outline-primary btn-xs"><i class="bi bi-pencil-square"></i></a>';
                } elseif ($row->user_id == Auth::id() && $row->status == 'D') {
                    $btn .= '<a href="' . route('backend.payments.editPaymentRequest', $row->sl_no) . '" class="btn btn-outline-primary btn-xs"><i class="bi bi-pencil-square"></i></a>';
                }
                $btn .= '<form action="' . route('backend.templates.templateCommon', $row->template_type) . '" method="post" style="display:inline;">' . csrf_field() . '<input type="hidden" name="slno" value="' . e($row->sl_no) . '"><button type="submit" class="btn btn-outline-info btn-xs" onclick="return confirm(\'Do you want to preview/generate PDF??\');"><i class="bi bi-eye"></i></button></form>';
                return $btn;
            })
            ->rawColumns(['status', 'shortcut_name', 'action'])
            ->toJson();
    }
}
