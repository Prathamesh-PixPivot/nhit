<?php

namespace App\Services;

use App\Models\BankLetterApprovalStep;
use App\Models\BankLetterApprovalPriority;
use App\Models\BankLetterApprovalLog;
use App\Models\Payment;
use App\Models\PaymentNote;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\NoteStatusChangeMail;
use Carbon\Carbon;

class BankLetterService
{
    /**
     * Create bank letter from payment notes
     */
    public function createBankLetterFromNotes(array $noteIds, $userId)
    {
        try {
            DB::beginTransaction();
            
            $notes = PaymentNote::whereIn('id', $noteIds)
                ->where('status', 'A') // Only approved notes
                ->get();
                
            if ($notes->isEmpty()) {
                throw new \Exception('No approved payment notes found.');
            }
            
            $totalAmount = $notes->sum('net_payable_round_off');
            $slNo = $this->generateSerialNumber();
            
            // Create payment entries for bank letter
            $payments = [];
            foreach ($notes as $note) {
                $payment = $this->createPaymentFromNote($note, $slNo, $userId);
                $payments[] = $payment;
            }
            
            // Determine approval workflow
            $approvalStep = $this->getApprovalStepForAmount($totalAmount);
            if (!$approvalStep) {
                throw new \Exception('No approval step configured for this amount.');
            }
            
            // Create initial approval log
            $this->createInitialApprovalLog($slNo, $approvalStep, $userId);
            
            // Send notification emails
            $this->sendBankLetterNotifications($slNo, $totalAmount, $approvalStep, $payments[0]);
            
            DB::commit();
            
            return [
                'success' => true,
                'sl_no' => $slNo,
                'total_amount' => $totalAmount,
                'payments_count' => count($payments)
            ];
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    
    /**
     * Get approval step for amount
     */
    public function getApprovalStepForAmount($amount)
    {
        return BankLetterApprovalStep::where('min_amount', '<=', $amount)
            ->where(function ($query) use ($amount) {
                $query->where('max_amount', '>=', $amount)
                      ->orWhereNull('max_amount');
            })
            ->orderBy('min_amount', 'desc')
            ->with('approvers.user')
            ->first();
    }
    
    /**
     * Create payment from note
     */
    private function createPaymentFromNote($note, $slNo, $userId)
    {
        $payment = new Payment();
        $payment->sl_no = $slNo;
        $payment->payment_note_id = $note->id;
        $payment->user_id = $userId;
        $payment->status = 'S'; // Sent for approval
        
        // Set payment details based on note type
        if ($note->greenNote) {
            $payment->project = $note->greenNote->vendor->project ?? '';
            $payment->name_of_beneficiary = $note->greenNote->supplier->vendor_name ?? '';
            $payment->account_number = $note->greenNote->supplier->account_number ?? '';
            $payment->name_of_bank = $note->greenNote->supplier->bank_name ?? '';
            $payment->ifsc_code = $note->greenNote->supplier->ifsc_code ?? '';
        } elseif ($note->reimbursementNote) {
            $payment->project = $note->reimbursementNote->project->project ?? '';
            $payment->name_of_beneficiary = $note->reimbursementNote->selectUser 
                ? $note->reimbursementNote->selectUser->name 
                : $note->reimbursementNote->user->name;
        }
        
        $payment->amount = $note->net_payable_round_off;
        $payment->purpose = $this->generatePurpose($note);
        $payment->template_type = 'any-bank-internal-external-bulk';
        $payment->from_account_type = 'Internal';
        $payment->to_account_type = 'External';
        $payment->date = Carbon::now();
        
        $payment->save();
        
        return $payment;
    }
    
    /**
     * Generate purpose text
     */
    private function generatePurpose($note)
    {
        if ($note->greenNote) {
            return "Payment for invoice(s) - " . ($note->greenNote->supplier->vendor_name ?? 'Vendor');
        } elseif ($note->reimbursementNote) {
            return "Reimbursement payment - " . ($note->reimbursementNote->selectUser 
                ? $note->reimbursementNote->selectUser->name 
                : $note->reimbursementNote->user->name);
        }
        
        return "Payment Note ID: " . $note->id;
    }
    
    /**
     * Create initial approval log
     */
    private function createInitialApprovalLog($slNo, $approvalStep, $userId)
    {
        $firstLevelApprovers = $approvalStep->approvers->where('approver_level', 1);
        
        if ($firstLevelApprovers->isEmpty()) {
            throw new \Exception('No first level approvers configured.');
        }
        
        $firstApprover = $firstLevelApprovers->first();
        
        $log = BankLetterApprovalLog::create([
            'sl_no' => $slNo,
            'priority_id' => $firstApprover->id,
            'reviewer_id' => $userId,
            'status' => 'P', // Pending
            'comments' => 'Bank letter created and sent for approval'
        ]);
        
        // Attach all first level approvers
        $approverIds = $firstLevelApprovers->pluck('id')->toArray();
        if (!empty($approverIds)) {
            $log->priorities()->attach($approverIds);
        }
        
        return $log;
    }
    
    /**
     * Process approval/rejection
     */
    public function processApproval($slNo, $status, $comments, $userId)
    {
        try {
            DB::beginTransaction();
            
            $payments = Payment::where('sl_no', $slNo)->get();
            if ($payments->isEmpty()) {
                throw new \Exception('No payments found for this bank letter.');
            }
            
            $totalAmount = $payments->sum('amount');
            $firstPayment = $payments->first();
            
            // Check if user already submitted approval
            $existingLog = BankLetterApprovalLog::where('sl_no', $slNo)
                ->where('reviewer_id', $userId)
                ->where('status', 'A')
                ->exists();
                
            if ($existingLog) {
                throw new \Exception('You have already submitted your approval for this bank letter.');
            }
            
            // Get current approval step
            $approvalStep = $this->getApprovalStepForAmount($totalAmount);
            if (!$approvalStep) {
                throw new \Exception('No approval step found for this amount.');
            }
            
            $existingLogsCount = BankLetterApprovalLog::where('sl_no', $slNo)->count();
            $currentLevel = $existingLogsCount;
            $nextLevel = $currentLevel + 1;
            
            if ($status === 'A') {
                // Check if this is the final approval
                $nextLevelApprovers = $approvalStep->approvers->where('approver_level', $nextLevel);
                
                if ($nextLevelApprovers->isEmpty()) {
                    // Final approval - mark payments as approved
                    $this->finalizeApproval($payments, $slNo, $userId, $comments);
                } else {
                    // Create next level approval
                    $this->createNextLevelApproval($slNo, $nextLevelApprovers, $userId, $comments);
                }
                
                $this->sendApprovalNotifications($slNo, $totalAmount, $firstPayment, $status, $nextLevelApprovers ?? collect());
                
            } else {
                // Rejection
                $this->processRejection($payments, $slNo, $userId, $comments);
                $this->sendRejectionNotifications($slNo, $totalAmount, $firstPayment, $comments);
            }
            
            DB::commit();
            
            return [
                'success' => true,
                'message' => $status === 'A' ? 'Bank letter approved successfully.' : 'Bank letter rejected successfully.'
            ];
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    
    /**
     * Finalize approval
     */
    private function finalizeApproval($payments, $slNo, $userId, $comments)
    {
        // Update payment status
        foreach ($payments as $payment) {
            $payment->status = 'A';
            $payment->save();
            
            // Update related payment notes
            if ($payment->paymentNote) {
                $payment->paymentNote->update(['status' => 'PA']); // Payment Approved
                
                if ($payment->paymentNote->greenNote) {
                    $payment->paymentNote->greenNote->update(['status' => 'PA']);
                }
                
                if ($payment->paymentNote->reimbursementNote) {
                    $payment->paymentNote->reimbursementNote->update(['status' => 'PA']);
                }
            }
        }
        
        // Create final approval log
        BankLetterApprovalLog::create([
            'sl_no' => $slNo,
            'priority_id' => null,
            'reviewer_id' => $userId,
            'status' => 'A',
            'comments' => $comments ?? 'Final approval completed'
        ]);
    }
    
    /**
     * Create next level approval
     */
    private function createNextLevelApproval($slNo, $nextLevelApprovers, $userId, $comments)
    {
        $firstNextApprover = $nextLevelApprovers->first();
        
        $log = BankLetterApprovalLog::create([
            'sl_no' => $slNo,
            'priority_id' => $firstNextApprover->id,
            'reviewer_id' => $userId,
            'status' => 'A',
            'comments' => $comments ?? 'Approved and forwarded to next level'
        ]);
        
        // Attach all next level approvers
        $approverIds = $nextLevelApprovers->pluck('id')->toArray();
        if (!empty($approverIds)) {
            $log->priorities()->attach($approverIds);
        }
    }
    
    /**
     * Process rejection
     */
    private function processRejection($payments, $slNo, $userId, $comments)
    {
        // Update payment status
        foreach ($payments as $payment) {
            $payment->status = 'R';
            $payment->save();
            
            // Update related payment notes
            if ($payment->paymentNote) {
                $payment->paymentNote->update(['status' => 'R']); // Rejected
                
                if ($payment->paymentNote->greenNote) {
                    $payment->paymentNote->greenNote->update(['status' => 'R']);
                }
                
                if ($payment->paymentNote->reimbursementNote) {
                    $payment->paymentNote->reimbursementNote->update(['status' => 'R']);
                }
            }
        }
        
        // Create rejection log
        BankLetterApprovalLog::create([
            'sl_no' => $slNo,
            'priority_id' => null,
            'reviewer_id' => $userId,
            'status' => 'R',
            'comments' => $comments ?? 'Bank letter rejected'
        ]);
    }
    
    /**
     * Send bank letter notifications
     */
    private function sendBankLetterNotifications($slNo, $totalAmount, $approvalStep, $payment)
    {
        $firstLevelApprovers = $approvalStep->approvers->where('approver_level', 1);
        
        $data = [
            'updated_by' => auth()->user()->email,
            'subject' => 'Bank RTGS/NEFT Letter of Rs ' . number_format($totalAmount) . ' has been Generated',
            'approver_name' => 'Approver',
            'maker' => auth()->user()->email . ' has generated a Bank RTGS/NEFT letter No. ' . $slNo . ' of Rs ' . number_format($totalAmount) . ' for ' . ($payment->project ?? 'Project') . ' & due for your review.',
            'end' => 'Login to the panel for review & process.'
        ];
        
        foreach ($firstLevelApprovers as $approver) {
            if ($approver->user && $approver->user->email) {
                try {
                    Mail::to($approver->user->email)->send(new NoteStatusChangeMail($data));
                } catch (\Exception $e) {
                    // Log email error but don't fail the process
                    \Log::error('Failed to send bank letter notification: ' . $e->getMessage());
                }
            }
        }
    }
    
    /**
     * Send approval notifications
     */
    private function sendApprovalNotifications($slNo, $totalAmount, $payment, $status, $nextLevelApprovers)
    {
        if ($nextLevelApprovers->isEmpty()) {
            // Final approval notification
            $data = [
                'updated_by' => auth()->user()->email,
                'subject' => 'Bank RTGS/NEFT Letter of Rs ' . number_format($totalAmount) . ' is Approved & due for Payment',
                'approver_name' => auth()->user()->name,
                'maker' => 'Bank RTGS/NEFT letter ' . $slNo . ' of Rs ' . number_format($totalAmount) . ' for ' . ($payment->project ?? 'Project') . ' has been fully approved.',
                'end' => 'Login to the panel for payment processing.'
            ];
            
            // Notify the creator
            if ($payment->user && $payment->user->email) {
                try {
                    Mail::to($payment->user->email)->send(new NoteStatusChangeMail($data));
                } catch (\Exception $e) {
                    \Log::error('Failed to send final approval notification: ' . $e->getMessage());
                }
            }
        } else {
            // Next level approval notification
            $data = [
                'updated_by' => auth()->user()->email,
                'subject' => 'Bank RTGS/NEFT Letter of Rs ' . number_format($totalAmount) . ' is due for Approval',
                'approver_name' => 'Next Approver',
                'maker' => 'Bank RTGS/NEFT letter ' . $slNo . ' of Rs ' . number_format($totalAmount) . ' for ' . ($payment->project ?? 'Project') . ' has been approved and forwarded for your review.',
                'end' => 'Login for review & approval'
            ];
            
            foreach ($nextLevelApprovers as $approver) {
                if ($approver->user && $approver->user->email) {
                    try {
                        Mail::to($approver->user->email)->send(new NoteStatusChangeMail($data));
                    } catch (\Exception $e) {
                        \Log::error('Failed to send next level approval notification: ' . $e->getMessage());
                    }
                }
            }
        }
    }
    
    /**
     * Send rejection notifications
     */
    private function sendRejectionNotifications($slNo, $totalAmount, $payment, $comments)
    {
        $data = [
            'updated_by' => auth()->user()->email,
            'subject' => 'Bank RTGS/NEFT Letter of Rs ' . number_format($totalAmount) . ' has been Rejected',
            'approver_name' => auth()->user()->name,
            'maker' => 'Bank RTGS/NEFT Letter No. ' . $slNo . ' of Rs ' . number_format($totalAmount) . ' for ' . ($payment->project ?? 'Project') . ' has been rejected.',
            'rejection' => $comments ?? 'No specific reason provided',
            'end' => 'Login to the panel for review & process.'
        ];
        
        // Notify the creator
        if ($payment->user && $payment->user->email) {
            try {
                Mail::to($payment->user->email)->send(new NoteStatusChangeMail($data));
            } catch (\Exception $e) {
                \Log::error('Failed to send rejection notification: ' . $e->getMessage());
            }
        }
    }
    
    /**
     * Generate serial number
     */
    private function generateSerialNumber()
    {
        return 'BL' . date('Ymd') . str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);
    }
    
    /**
     * Get bank letter statistics
     */
    public function getBankLetterStats()
    {
        $stats = [
            'total' => Payment::distinct('sl_no')->count(),
            'pending' => Payment::where('status', 'S')->distinct('sl_no')->count(),
            'approved' => Payment::where('status', 'A')->distinct('sl_no')->count(),
            'rejected' => Payment::where('status', 'R')->distinct('sl_no')->count(),
            'total_amount' => Payment::where('status', 'A')->sum('amount')
        ];
        
        return $stats;
    }
    
    /**
     * Get pending approvals for user
     */
    public function getPendingApprovalsForUser($userId)
    {
        $userApprovalPriorities = BankLetterApprovalPriority::where('reviewer_id', $userId)->pluck('id');
        
        $pendingLogs = BankLetterApprovalLog::whereIn('priority_id', $userApprovalPriorities)
            ->where('status', 'P')
            ->with(['bankLetterApprovalPriority.approvalStep'])
            ->get();
            
        $pendingBankLetters = [];
        
        foreach ($pendingLogs as $log) {
            $payments = Payment::where('sl_no', $log->sl_no)->get();
            if ($payments->isNotEmpty()) {
                $pendingBankLetters[] = [
                    'sl_no' => $log->sl_no,
                    'total_amount' => $payments->sum('amount'),
                    'payments_count' => $payments->count(),
                    'created_at' => $payments->first()->created_at,
                    'project' => $payments->first()->project,
                    'log' => $log
                ];
            }
        }
        
        return collect($pendingBankLetters);
    }
}
