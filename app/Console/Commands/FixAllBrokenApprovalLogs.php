<?php

namespace App\Console\Commands;

use App\Models\PaymentNote;
use App\Models\PaymentNoteApprovalLog;
use App\Models\PaymentNoteApprovalPriority;
use App\Models\PaymentNoteApprovalStep;
use Illuminate\Console\Command;

class FixAllBrokenApprovalLogs extends Command
{
    protected $signature = 'payment-notes:fix-all-broken';
    protected $description = 'Fix all payment notes with broken/missing priority IDs';

    public function handle()
    {
        $this->info('=== Scanning for payment notes with broken approval logs ===');
        
        // Get all pending or submitted payment notes
        $notes = PaymentNote::whereIn('status', ['P', 'S'])->get();
        
        $fixed = 0;
        $skipped = 0;
        $errors = 0;
        
        foreach ($notes as $note) {
            $this->info("\nChecking Payment Note ID: {$note->id} ({$note->note_no}) - Amount: ₹" . number_format($note->net_payable_round_off, 2));
            
            // Find the correct approval step for this amount
            $approvalStep = PaymentNoteApprovalStep::where('min_amount', '<=', $note->net_payable_round_off)
                ->where(function ($query) use ($note) {
                    $query->where('max_amount', '>=', $note->net_payable_round_off)
                          ->orWhereNull('max_amount');
                })
                ->orderBy('min_amount', 'desc')
                ->first();
            
            if (!$approvalStep) {
                $this->warn("  ⚠ No approval step found for this amount - skipping");
                $skipped++;
                continue;
            }
            
            // Get all logs for this note
            $logs = PaymentNoteApprovalLog::where('payment_note_id', $note->id)
                ->orderBy('created_at')
                ->get();
            
            if ($logs->isEmpty()) {
                $this->warn("  ⚠ No approval logs found - skipping");
                $skipped++;
                continue;
            }
            
            // Check for broken pending logs
            $pendingLogs = $logs->where('status', 'P');
            $hasBrokenLog = false;
            
            foreach ($pendingLogs as $log) {
                $priority = PaymentNoteApprovalPriority::find($log->priority_id);
                if (!$priority) {
                    $this->error("  ✗ Found broken pending log ID {$log->id} with missing priority ID {$log->priority_id}");
                    $hasBrokenLog = true;
                } elseif ($priority->approval_step_id != $approvalStep->id) {
                    $this->error("  ✗ Found pending log with wrong approval step (has: {$priority->approval_step_id}, should be: {$approvalStep->id})");
                    $hasBrokenLog = true;
                }
            }
            
            if (!$hasBrokenLog) {
                $this->info("  ✓ All logs are correct");
                $skipped++;
                continue;
            }
            
            // Fix the broken logs
            try {
                // Calculate what the current level should be
                $approvedCount = $logs->where('status', 'A')->count();
                $currentLevel = $approvedCount + 1;
                
                $this->info("  Approved count: {$approvedCount}, Current level should be: {$currentLevel}");
                
                // Get approvers for the current level
                $currentLevelApprovers = PaymentNoteApprovalPriority::where('approval_step_id', $approvalStep->id)
                    ->where('approver_level', $currentLevel)
                    ->get();
                
                if ($currentLevelApprovers->isEmpty()) {
                    $this->warn("  ⚠ No approvers configured for level {$currentLevel} - checking if fully approved");
                    
                    // Check if all levels are done
                    $maxLevel = PaymentNoteApprovalPriority::where('approval_step_id', $approvalStep->id)
                        ->max('approver_level');
                    
                    if ($approvedCount >= $maxLevel) {
                        // Delete all pending logs - this note is fully approved
                        $deleted = PaymentNoteApprovalLog::where('payment_note_id', $note->id)
                            ->where('status', 'P')
                            ->delete();
                        
                        $this->info("  ✓ Deleted {$deleted} stale pending log(s) - note is fully approved");
                        $fixed++;
                    } else {
                        $this->error("  ✗ Cannot fix - no approvers for level {$currentLevel}");
                        $errors++;
                    }
                    continue;
                }
                
                $approverNames = [];
                foreach ($currentLevelApprovers as $approver) {
                    $user = \App\Models\User::find($approver->reviewer_id);
                    if ($user) {
                        $approverNames[] = $user->name;
                    }
                }
                
                $this->info("  Level {$currentLevel} approvers: " . implode(', ', $approverNames));
                
                // Delete all broken pending logs
                $deleted = PaymentNoteApprovalLog::where('payment_note_id', $note->id)
                    ->where('status', 'P')
                    ->delete();
                
                $this->info("  Deleted {$deleted} broken pending log(s)");
                
                // Create new correct pending log
                $firstApprover = $currentLevelApprovers->first();
                $newLog = PaymentNoteApprovalLog::create([
                    'payment_note_id' => $note->id,
                    'priority_id' => $firstApprover->id,
                    'reviewer_id' => $firstApprover->reviewer_id,
                    'status' => 'P',
                    'comments' => 'System corrected - pending approval',
                ]);
                
                // Link all current level approvers
                $approverIds = $currentLevelApprovers->pluck('id')->toArray();
                if (!empty($approverIds)) {
                    try {
                        \DB::table('payment_note_log_priority')->insert(
                            array_map(function($priorityId) use ($newLog) {
                                return [
                                    'payment_note_approval_log_id' => $newLog->id,
                                    'priority_id' => $priorityId,
                                    'created_at' => now(),
                                    'updated_at' => now(),
                                ];
                            }, $approverIds)
                        );
                    } catch (\Exception $e) {
                        $this->warn("  Could not link priorities: " . $e->getMessage());
                    }
                }
                
                $this->info("  ✅ Created new pending log for Level {$currentLevel}: " . implode(', ', $approverNames));
                $fixed++;
                
            } catch (\Exception $e) {
                $this->error("  ✗ Error fixing: " . $e->getMessage());
                $errors++;
            }
        }
        
        $this->info("\n=== Summary ===");
        $this->info("Fixed: {$fixed}");
        $this->info("Skipped (already correct): {$skipped}");
        $this->info("Errors: {$errors}");
        $this->info("Total checked: " . $notes->count());
        
        return 0;
    }
}
