<?php

namespace App\Console\Commands;

use App\Models\PaymentNote;
use App\Models\PaymentNoteApprovalLog;
use App\Models\PaymentNoteApprovalPriority;
use App\Models\PaymentNoteApprovalStep;
use Illuminate\Console\Command;

class FixPaymentNoteApprovalLogs extends Command
{
    protected $signature = 'payment-notes:fix-approval-logs';
    protected $description = 'Fix payment note approval logs to show correct next approvers';

    public function handle()
    {
        $this->info('Starting to fix payment note approval logs...');
        
        // Get all payment notes that are pending (status = 'P' or 'S')
        $notes = PaymentNote::whereIn('status', ['P', 'S'])->get();
        
        $fixed = 0;
        $skipped = 0;
        
        foreach ($notes as $note) {
            $this->info("Processing Payment Note ID: {$note->id} ({$note->note_no}) - Amount: ₹" . number_format($note->net_payable_round_off, 2));
            
            // Find the correct approval step based on amount
            $approvalStep = PaymentNoteApprovalStep::where('min_amount', '<=', $note->net_payable_round_off)
                ->where(function ($query) use ($note) {
                    $query->where('max_amount', '>=', $note->net_payable_round_off)
                          ->orWhereNull('max_amount');
                })
                ->orderBy('min_amount', 'desc')
                ->first();
            
            if (!$approvalStep) {
                $this->warn("  ⚠ No approval step found for amount ₹{$note->net_payable_round_off}");
                $skipped++;
                continue;
            }
            
            // Count approved logs
            $approvedCount = $note->paymentApprovalLogs->where('status', 'A')->count();
            $currentLevel = $approvedCount + 1;
            
            $this->info("  Approved: {$approvedCount}, Current Level: {$currentLevel}");
            
            // Get pending logs
            $pendingLogs = $note->paymentApprovalLogs->where('status', 'P');
            
            if ($pendingLogs->isEmpty()) {
                $this->info("  No pending logs - creating new one for level {$currentLevel}");
                
                // Get current level approvers
                $currentLevelApprovers = PaymentNoteApprovalPriority::where('approval_step_id', $approvalStep->id)
                    ->where('approver_level', $currentLevel)
                    ->get();
                
                if ($currentLevelApprovers->isEmpty()) {
                    $this->warn("  ⚠ No approvers configured for level {$currentLevel}");
                    $skipped++;
                    continue;
                }
                
                // Create pending log for current level
                $firstApprover = $currentLevelApprovers->first();
                $log = PaymentNoteApprovalLog::create([
                    'payment_note_id' => $note->id,
                    'priority_id' => $firstApprover->id,
                    'reviewer_id' => $firstApprover->reviewer_id,
                    'status' => 'P',
                    'comments' => 'System corrected - pending approval',
                ]);
                
                // Attach all current level approvers
                $approverIds = $currentLevelApprovers->pluck('id')->toArray();
                if (!empty($approverIds)) {
                    $log->priorities()->attach($approverIds);
                }
                
                $this->info("  ✅ Created pending log for: " . $firstApprover->user->name);
                $fixed++;
            } else {
                // Check if pending log is for correct level
                $pendingLog = $pendingLogs->first();
                $pendingPriority = $pendingLog->paymentNoteApprovalPriority;
                
                if ($pendingPriority && $pendingPriority->approver_level != $currentLevel) {
                    $this->warn("  ⚠ Pending log is for wrong level (has: {$pendingPriority->approver_level}, should be: {$currentLevel})");
                    
                    // Delete wrong pending logs
                    PaymentNoteApprovalLog::where('payment_note_id', $note->id)
                        ->where('status', 'P')
                        ->delete();
                    
                    // Create correct pending log
                    $currentLevelApprovers = PaymentNoteApprovalPriority::where('approval_step_id', $approvalStep->id)
                        ->where('approver_level', $currentLevel)
                        ->get();
                    
                    if ($currentLevelApprovers->isNotEmpty()) {
                        $firstApprover = $currentLevelApprovers->first();
                        $log = PaymentNoteApprovalLog::create([
                            'payment_note_id' => $note->id,
                            'priority_id' => $firstApprover->id,
                            'reviewer_id' => $firstApprover->reviewer_id,
                            'status' => 'P',
                            'comments' => 'System corrected - pending approval at correct level',
                        ]);
                        
                        $approverIds = $currentLevelApprovers->pluck('id')->toArray();
                        if (!empty($approverIds)) {
                            $log->priorities()->attach($approverIds);
                        }
                        
                        $this->info("  ✅ Fixed pending log for: " . $firstApprover->user->name . " (Level {$currentLevel})");
                        $fixed++;
                    } else {
                        $this->warn("  ⚠ No approvers configured for level {$currentLevel}");
                        $skipped++;
                    }
                } else {
                    $this->info("  ✓ Pending log is already correct");
                    $skipped++;
                }
            }
        }
        
        $this->info("\n=== Summary ===");
        $this->info("Fixed: {$fixed}");
        $this->info("Skipped: {$skipped}");
        $this->info("Total: " . ($fixed + $skipped));
        
        return 0;
    }
}
