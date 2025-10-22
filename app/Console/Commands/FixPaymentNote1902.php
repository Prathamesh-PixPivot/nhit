<?php

namespace App\Console\Commands;

use App\Models\PaymentNote;
use App\Models\PaymentNoteApprovalLog;
use App\Models\PaymentNoteApprovalPriority;
use App\Models\PaymentNoteApprovalStep;
use Illuminate\Console\Command;

class FixPaymentNote1902 extends Command
{
    protected $signature = 'payment-notes:fix-1902';
    protected $description = 'Fix payment note 1902 with broken priority IDs';

    public function handle()
    {
        $noteId = 1902;
        
        $note = PaymentNote::find($noteId);
        
        if (!$note) {
            $this->error("Payment Note {$noteId} not found!");
            return 1;
        }
        
        $this->info("=== Fixing Payment Note {$noteId} ===");
        $this->info("Note No: {$note->note_no}");
        $this->info("Amount: ₹" . number_format($note->net_payable_round_off, 2));
        $this->info("Status: {$note->status}");
        $this->info("");
        
        // Find the correct approval step
        $approvalStep = PaymentNoteApprovalStep::where('min_amount', '<=', $note->net_payable_round_off)
            ->where(function ($query) use ($note) {
                $query->where('max_amount', '>=', $note->net_payable_round_off)
                      ->orWhereNull('max_amount');
            })
            ->orderBy('min_amount', 'desc')
            ->first();
        
        if (!$approvalStep) {
            $this->error("No approval step found!");
            return 1;
        }
        
        $this->info("Found approval step: ₹{$approvalStep->min_amount} - " . 
                    ($approvalStep->max_amount ? "₹{$approvalStep->max_amount}" : "unlimited"));
        
        // Get current logs
        $currentLogs = PaymentNoteApprovalLog::where('payment_note_id', $noteId)->get();
        $approvedCount = $currentLogs->where('status', 'A')->count();
        
        $this->info("Current approved logs: {$approvedCount}");
        $this->info("Current level should be: " . ($approvedCount + 1));
        
        // Check if Ravi has approved
        $raviApproved = $currentLogs->where('reviewer_id', 15)->where('status', 'A')->isNotEmpty();
        
        if ($raviApproved) {
            $this->info("✓ Ravi Kant Vij (Level 1) has approved");
            
            // Next should be Level 2: Sunil Kumar
            $level2Approvers = PaymentNoteApprovalPriority::where('approval_step_id', $approvalStep->id)
                ->where('approver_level', 2)
                ->get();
            
            if ($level2Approvers->isEmpty()) {
                $this->error("No Level 2 approvers configured!");
                return 1;
            }
            
            $this->info("Level 2 approvers: " . $level2Approvers->pluck('user.name')->join(', '));
            
            // Delete all pending logs with broken priority IDs
            $deleted = PaymentNoteApprovalLog::where('payment_note_id', $noteId)
                ->where('status', 'P')
                ->delete();
            
            $this->info("Deleted {$deleted} broken pending log(s)");
            
            // Create new correct pending log for Level 2
            $firstLevel2 = $level2Approvers->first();
            $newLog = PaymentNoteApprovalLog::create([
                'payment_note_id' => $noteId,
                'priority_id' => $firstLevel2->id,
                'reviewer_id' => $firstLevel2->reviewer_id,
                'status' => 'P',
                'comments' => 'Corrected - awaiting Level 2 approval',
            ]);
            
            $this->info("✅ Created new pending log for: " . $firstLevel2->user->name . " (Level 2)");
            
            // Attach all Level 2 approvers to the log
            $level2Ids = $level2Approvers->pluck('id')->toArray();
            if (!empty($level2Ids)) {
                try {
                    \DB::table('payment_note_log_priority')->insert(
                        array_map(function($priorityId) use ($newLog) {
                            return [
                                'payment_note_approval_log_id' => $newLog->id,
                                'priority_id' => $priorityId,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ];
                        }, $level2Ids)
                    );
                    $this->info("✅ Linked all Level 2 approvers to the pending log");
                } catch (\Exception $e) {
                    $this->warn("Could not link priorities: " . $e->getMessage());
                }
            }
            
            $this->info("");
            $this->info("=== Fix Complete ===");
            $this->info("Next approver should now show: " . $level2Approvers->pluck('user.name')->join(', '));
            
        } else {
            $this->warn("Ravi has not approved yet - no fix needed");
        }
        
        return 0;
    }
}
