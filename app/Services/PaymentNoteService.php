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
        // Find the appropriate reviewer for the first step
        // This should be based on your approval flow configuration
        $initialReviewer = $this->getInitialReviewer($paymentNote);

        if ($initialReviewer) {
            PaymentNoteApprovalLog::create([
                'payment_note_id' => $paymentNote->id,
                'priority_id' => 1, // First priority
                'reviewer_id' => $initialReviewer->id,
                'status' => 'P', // Pending
                'comments' => 'Auto-created draft payment note',
            ]);
        }
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
