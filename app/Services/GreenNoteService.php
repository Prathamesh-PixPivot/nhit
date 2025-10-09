<?php

namespace App\Services;

use App\Models\GreenNote;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GreenNoteService
{
    /**
     * Handle multiple invoices for a green note
     */
    public function updateInvoices(GreenNote $greenNote, array $invoicesData)
    {
        try {
            DB::beginTransaction();

            // Validate invoice data
            $validatedInvoices = $this->validateInvoicesData($invoicesData);

            // Update the green note with multiple invoices
            $greenNote->update([
                'invoices' => $validatedInvoices,
                // Calculate totals from all invoices
                'invoice_value' => collect($validatedInvoices)->sum('invoice_value'),
                'invoice_base_value' => collect($validatedInvoices)->sum('invoice_base_value'),
                'invoice_gst' => collect($validatedInvoices)->sum('invoice_gst'),
                'invoice_other_charges' => collect($validatedInvoices)->sum('invoice_other_charges'),
            ]);

            DB::commit();

            Log::info("Multiple invoices updated for Green Note ID: {$greenNote->id}");

            return $greenNote;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Failed to update invoices for Green Note ID: {$greenNote->id}. Error: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Validate invoices data
     */
    private function validateInvoicesData(array $invoicesData)
    {
        $validatedInvoices = [];

        foreach ($invoicesData as $index => $invoice) {
            // Validate required fields
            if (empty($invoice['invoice_number'])) {
                throw new \Exception("Invoice number is required for invoice #" . ($index + 1));
            }

            if (empty($invoice['invoice_date'])) {
                throw new \Exception("Invoice date is required for invoice #" . ($index + 1));
            }

            if (!isset($invoice['invoice_value']) || $invoice['invoice_value'] <= 0) {
                throw new \Exception("Valid invoice value is required for invoice #" . ($index + 1));
            }

            $validatedInvoices[] = [
                'invoice_number' => $invoice['invoice_number'],
                'invoice_date' => $invoice['invoice_date'],
                'invoice_value' => (float) $invoice['invoice_value'],
                'invoice_base_value' => (float) ($invoice['invoice_base_value'] ?? 0),
                'invoice_gst' => (float) ($invoice['invoice_gst'] ?? 0),
                'invoice_other_charges' => (float) ($invoice['invoice_other_charges'] ?? 0),
                'description' => $invoice['description'] ?? '',
            ];
        }

        return $validatedInvoices;
    }

    /**
     * Put green note on hold
     */
    public function putOnHold(GreenNote $greenNote, string $reason, User $user)
    {
        try {
            DB::beginTransaction();

            $greenNote->putOnHold($reason, $user->id);

            // Log the hold action in approval logs if needed
            // You may want to create an approval log entry here

            DB::commit();

            Log::info("Green note put on hold. ID: {$greenNote->id}, Reason: {$reason}");

            return $greenNote;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Failed to put green note on hold. ID: {$greenNote->id}. Error: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Remove green note from hold
     */
    public function removeFromHold(GreenNote $greenNote, User $user, string $newStatus = 'P')
    {
        try {
            DB::beginTransaction();

            $greenNote->removeFromHold($newStatus);

            // Log the removal from hold
            // You may want to create an approval log entry here

            DB::commit();

            Log::info("Green note removed from hold. ID: {$greenNote->id}");

            return $greenNote;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Failed to remove green note from hold. ID: {$greenNote->id}. Error: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Handle green note approval and trigger payment note creation
     */
    public function approveGreenNote(GreenNote $greenNote, User $approver, string $comments = null)
    {
        try {
            DB::beginTransaction();

            // Update green note status to approved
            $greenNote->update(['status' => 'A']);

            // Create approval log
            // This should integrate with your existing approval log system

            // Auto-create draft payment note
            $paymentNoteService = new PaymentNoteService();
            $draftPaymentNote = $paymentNoteService->createDraftOnApproval($greenNote, $approver);

            DB::commit();

            Log::info("Green note approved and draft payment note created. Green Note ID: {$greenNote->id}, Payment Note ID: {$draftPaymentNote->id}");

            return [
                'greenNote' => $greenNote,
                'paymentNote' => $draftPaymentNote
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Failed to approve green note. ID: {$greenNote->id}. Error: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get invoice summary for a green note
     */
    public function getInvoiceSummary(GreenNote $greenNote)
    {
        $summary = [
            'total_invoices' => $greenNote->invoice_count,
            'total_value' => $greenNote->total_invoice_value,
            'invoices' => []
        ];

        if (!empty($greenNote->invoices) && is_array($greenNote->invoices)) {
            $summary['invoices'] = $greenNote->invoices;
        } elseif ($greenNote->invoice_number) {
            // Handle single invoice (backward compatibility)
            $summary['invoices'] = [[
                'invoice_number' => $greenNote->invoice_number,
                'invoice_date' => $greenNote->invoice_date,
                'invoice_value' => $greenNote->invoice_value,
                'invoice_base_value' => $greenNote->invoice_base_value,
                'invoice_gst' => $greenNote->invoice_gst,
                'invoice_other_charges' => $greenNote->invoice_other_charges,
            ]];
        }

        return $summary;
    }
}
