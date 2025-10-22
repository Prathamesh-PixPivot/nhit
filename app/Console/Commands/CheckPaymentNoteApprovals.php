<?php

namespace App\Console\Commands;

use App\Models\PaymentNote;
use App\Models\PaymentNoteApprovalLog;
use App\Models\PaymentNoteApprovalPriority;
use App\Models\PaymentNoteApprovalStep;
use Illuminate\Console\Command;

class CheckPaymentNoteApprovals extends Command
{
    protected $signature = 'payment-notes:check {id}';
    protected $description = 'Check approval status of a specific payment note';

    public function handle()
    {
        $id = $this->argument('id');
        
        $note = PaymentNote::find($id);
        
        if (!$note) {
            $this->error("Payment Note ID {$id} not found!");
            return 1;
        }
        
        $this->info("=== Payment Note ID: {$note->id} ===");
        $this->info("Note No: {$note->note_no}");
        $this->info("Amount: ₹" . number_format($note->net_payable_round_off, 2));
        $this->info("Status: {$note->status}");
        $this->info("");
        
        // Find approval step
        $approvalStep = PaymentNoteApprovalStep::where('min_amount', '<=', $note->net_payable_round_off)
            ->where(function ($query) use ($note) {
                $query->where('max_amount', '>=', $note->net_payable_round_off)
                      ->orWhereNull('max_amount');
            })
            ->orderBy('min_amount', 'desc')
            ->first();
        
        if ($approvalStep) {
            $this->info("Approval Step: ₹{$approvalStep->min_amount} - " . ($approvalStep->max_amount ? "₹{$approvalStep->max_amount}" : "unlimited"));
            
            $allPriorities = PaymentNoteApprovalPriority::where('approval_step_id', $approvalStep->id)
                ->orderBy('approver_level')
                ->with('user')
                ->get();
            
            $this->info("Configured Approvers:");
            foreach ($allPriorities->groupBy('approver_level') as $level => $priorities) {
                $this->info("  Level {$level}: " . $priorities->pluck('user.name')->join(', '));
            }
        } else {
            $this->error("No approval step found!");
        }
        
        $this->info("");
        $this->info("=== Approval Logs ===");
        
        $logs = PaymentNoteApprovalLog::where('payment_note_id', $note->id)
            ->orderBy('created_at')
            ->get();
        
        if ($logs->isEmpty()) {
            $this->warn("No approval logs found!");
        } else {
            foreach ($logs as $index => $log) {
                $logNumber = $index + 1;
                $this->info("Log #{$logNumber} (ID: {$log->id}):");
                $this->info("  Priority ID: {$log->priority_id}");
                
                // Manually load the priority to avoid relationship issues
                $priority = PaymentNoteApprovalPriority::find($log->priority_id);
                if ($priority) {
                    $this->info("  Priority Level: {$priority->approver_level}");
                    $priorityUser = \App\Models\User::find($priority->reviewer_id);
                    if ($priorityUser) {
                        $this->info("  Priority User: {$priorityUser->name}");
                    }
                } else {
                    $this->error("  ⚠ Priority ID {$log->priority_id} NOT FOUND!");
                }
                
                $this->info("  Reviewer ID: {$log->reviewer_id}");
                $reviewer = \App\Models\User::find($log->reviewer_id);
                if ($reviewer) {
                    $this->info("  Reviewer Name: {$reviewer->name}");
                }
                $this->info("  Status: {$log->status}");
                $this->info("  Comments: " . ($log->comments ?? 'None'));
                $this->info("  Created: {$log->created_at}");
                $this->info("  Updated: {$log->updated_at}");
                
                // Check linked priorities from pivot table
                try {
                    $pivotRecords = \DB::table('payment_note_log_priority')
                        ->where('payment_note_approval_log_id', $log->id)
                        ->get();
                    
                    if ($pivotRecords->isNotEmpty()) {
                        $linkedNames = [];
                        foreach ($pivotRecords as $pivot) {
                            $linkedPriority = PaymentNoteApprovalPriority::find($pivot->priority_id);
                            if ($linkedPriority) {
                                $linkedUser = \App\Models\User::find($linkedPriority->reviewer_id);
                                if ($linkedUser) {
                                    $linkedNames[] = "{$linkedUser->name} (Level {$linkedPriority->approver_level})";
                                }
                            }
                        }
                        if (!empty($linkedNames)) {
                            $this->info("  Linked to: " . implode(', ', $linkedNames));
                        }
                    }
                } catch (\Exception $e) {
                    $this->warn("  Could not check pivot table: " . $e->getMessage());
                }
                
                $this->info("");
            }
        }
        
        // Check what the view would show
        $this->info("=== What Next Approver Should Show ===");
        $approvedCount = $logs->where('status', 'A')->count();
        $pendingCount = $logs->where('status', 'P')->count();
        
        $this->info("Approved: {$approvedCount}");
        $this->info("Pending: {$pendingCount}");
        $this->info("Current Level: " . ($approvedCount + 1));
        
        $pendingLogs = $logs->where('status', 'P');
        if ($pendingLogs->isNotEmpty()) {
            $this->info("Pending log points to:");
            foreach ($pendingLogs as $pLog) {
                $pPriority = PaymentNoteApprovalPriority::find($pLog->priority_id);
                if ($pPriority) {
                    $pUser = \App\Models\User::find($pPriority->reviewer_id);
                    if ($pUser) {
                        $this->info("  - {$pUser->name} (Level {$pPriority->approver_level})");
                    }
                }
            }
        } else {
            $this->info("No pending logs - should show nothing or final approval");
        }
        
        return 0;
    }
}
