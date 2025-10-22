<?php

namespace App\Services;

use App\Models\GreenNote;
use App\Models\PaymentNote;
use App\Models\PaymentNoteApprovalLog;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentNoteService
{
    /**
     * Auto-create draft payment note when green note is approved
     */
    public function createDraftOnApproval(GreenNote $greenNote, User $approver)
    {
        try {
            DB::beginTransaction();

            // Check if draft payment note already exists
            $existingDraft = PaymentNote::where('green_note_id', $greenNote->id)
                ->where('is_draft', true)
                ->first();

            if ($existingDraft) {
                Log::info("Draft payment note already exists for Green Note ID: {$greenNote->id}");
                return $existingDraft;
            }

            // Create draft payment note
            $paymentNote = PaymentNote::createDraftFromGreenNote($greenNote, $approver->id);

            // Create initial approval log entry
            $this->createInitialApprovalLog($paymentNote, $approver);

            DB::commit();

            Log::info("Draft payment note created successfully for Green Note ID: {$greenNote->id}, Payment Note ID: {$paymentNote->id}");

            return $paymentNote;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Failed to create draft payment note for Green Note ID: {$greenNote->id}. Error: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Create initial approval log for the draft payment note
     */
    private function createInitialApprovalLog(PaymentNote $paymentNote, User $approver)
    {
        // Find the correct approval step based on payment amount
        $approvalStep = \App\Models\PaymentNoteApprovalStep::where('min_amount', '<=', $paymentNote->net_payable_round_off)
            ->where(function ($query) use ($paymentNote) {
                $query->where('max_amount', '>=', $paymentNote->net_payable_round_off)
                      ->orWhereNull('max_amount');
            })
            ->orderBy('min_amount', 'desc')
            ->first();

        if (!$approvalStep) {
            Log::warning("No approval step found for payment amount: {$paymentNote->net_payable_round_off}");
            return;
        }

        // Get Level 1 approvers for this amount range
        $level1Approvers = \App\Models\PaymentNoteApprovalPriority::where('approval_step_id', $approvalStep->id)
            ->where('approver_level', 1)
            ->get();

        if ($level1Approvers->isEmpty()) {
            Log::warning("No Level 1 approvers found for approval step {$approvalStep->id}");
            return;
        }

        // Create approval log for the first Level 1 approver
        $firstApprover = $level1Approvers->first();
        $log = PaymentNoteApprovalLog::create([
            'payment_note_id' => $paymentNote->id,
            'priority_id' => $firstApprover->id,
            'reviewer_id' => $firstApprover->reviewer_id,
            'status' => 'P', // Pending
            'comments' => 'Auto-created for approval',
        ]);

        // Attach all Level 1 approvers to this log
        $level1ApproverIds = $level1Approvers->pluck('id')->toArray();
        if (!empty($level1ApproverIds)) {
            $log->priorities()->attach($level1ApproverIds);
        }

        Log::info("Initial approval log created for Payment Note {$paymentNote->id} with Level 1 approvers");
    }

    /**
     * Get the initial reviewer for payment note approval
     */
    private function getInitialReviewer(PaymentNote $paymentNote)
    {
        // This should be configured based on your business logic
        // For now, returning a default reviewer (you may need to adjust this)
        
        // Option 1: Use the same department head as the green note
        if ($paymentNote->greenNote && $paymentNote->greenNote->department) {
            // Find department head or accounts team member
            $reviewer = User::whereHas('roles', function ($query) {
                $query->where('name', 'Accounts Team');
            })->first();

            if ($reviewer) {
                return $reviewer;
            }
        }

        // Option 2: Use a default accounts user
        return User::whereHas('roles', function ($query) {
            $query->whereIn('name', ['Accounts Team', 'Finance Team']);
        })->first();
    }

    /**
     * Convert draft payment note to active
     */
    public function convertDraftToActive(PaymentNote $paymentNote, User $user)
    {
        if (!$paymentNote->isDraft()) {
            throw new \Exception('Payment note is not in draft status');
        }

        try {
            DB::beginTransaction();

            $paymentNote->convertToActive();

            // Update approval logs if needed
            $paymentNote->paymentApprovalLogs()
                ->where('status', 'P')
                ->update(['status' => 'P']); // Keep as pending for review

            DB::commit();

            Log::info("Payment note converted from draft to active. ID: {$paymentNote->id}");

            return $paymentNote;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Failed to convert payment note to active. ID: {$paymentNote->id}. Error: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Handle hold functionality for payment notes
     */
    public function putOnHold(PaymentNote $paymentNote, string $reason, User $user)
    {
        try {
            DB::beginTransaction();

            $paymentNote->update([
                'status' => 'H', // Hold status
                'hold_reason' => $reason,
                'hold_date' => now(),
                'hold_by' => $user->id,
            ]);

            // Log the hold action
            PaymentNoteApprovalLog::create([
                'payment_note_id' => $paymentNote->id,
                'reviewer_id' => $user->id,
                'status' => 'H',
                'comments' => "Put on hold: {$reason}",
            ]);

            DB::commit();

            Log::info("Payment note put on hold. ID: {$paymentNote->id}, Reason: {$reason}");

            return $paymentNote;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Failed to put payment note on hold. ID: {$paymentNote->id}. Error: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Remove payment note from hold
     */
    public function removeFromHold(PaymentNote $paymentNote, User $user, string $newStatus = 'P')
    {
        try {
            DB::beginTransaction();

            $paymentNote->update([
                'status' => $newStatus,
                'hold_reason' => null,
                'hold_date' => null,
                'hold_by' => null,
            ]);

            // Log the removal from hold
            PaymentNoteApprovalLog::create([
                'payment_note_id' => $paymentNote->id,
                'reviewer_id' => $user->id,
                'status' => $newStatus,
                'comments' => "Removed from hold",
            ]);

            DB::commit();

            Log::info("Payment note removed from hold. ID: {$paymentNote->id}");

            return $paymentNote;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Failed to remove payment note from hold. ID: {$paymentNote->id}. Error: " . $e->getMessage());
            throw $e;
        }
    }
}
