<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class GreenNote extends Model
{
    
    /**
     * The connection name for the model.
     * Always use organization database for green notes
     *
     * @var string
     */
    protected $connection = 'organization';
    
    protected $fillable = [
        'user_id', //
        'vendor_id',
        'department_id',
        'order_date',
        'base_value',
        'gst',
        'after_tax',
        'other_charges',
        'total_amount',
        'supplier_id',
        'msme_classification',
        'activity_type',
        'protest_note_raised',
        'brief_of_goods_services',
        'invoice_number',
        'invoice_date',
        'invoice_base_value',
        'invoice_gst',
        'invoice_value',
        'invoice_other_charges',
        'invoices', // Multiple invoices support
        'delayed_damages',
        'contract_start_date',
        'contract_end_date',
        'appointed_start_date',
        'supply_period_start',
        'supply_period_end',
        'whether_contract',
        'expense_category',
        'extension_contract_period',
        'approval_for',
        'budget_expenditure',
        'actual_expenditure',
        'expenditure_over_budget',
        'nature_of_expenses',
        'documents_workdone_supply',
        'documents_discrepancy',
        'amount_submission_non',
        'remarks',
        'auditor_remarks',
        'required_submitted',
        'expense_amount_within_contract',
        'milestone_status',
        'milestone_remarks',
        'deviations',
        'specify_deviation',
        'status',
        'hold_reason',
        'hold_date',
        'hold_by',
    ];

    protected $casts = [
        'invoices' => 'array',
        'hold_date' => 'datetime',
    ];
    public function department()
    {
        return $this->belongsTo(Department::class);
    }
    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }
    public function supplier()
    {
        return $this->belongsTo(Vendor::class, 'supplier_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
    public function getFormattedOrderNoAttribute()
    {
        // $currentYear = date('Y');
        // $previousYear = $currentYear - 1;
        // $shortCurrentYear = substr($currentYear, -2);
        // $shortPreviousYear = substr($previousYear, -2);

        // $formattedId = str_pad($this->id, 4, '0', STR_PAD_LEFT);

        // return "W/{$shortPreviousYear}-{$shortCurrentYear}/EN/{$formattedId}";
        $currentMonth = date('n'); // Get current month (1-12)
        $currentYear = date('Y');
        $previousYear = $currentYear - 1;

        // Determine financial year range (April - March)
        if ($currentMonth >= 4) {
            $financialStartYear = $currentYear;
            $financialEndYear = $currentYear + 1;
        } else {
            $financialStartYear = $previousYear;
            $financialEndYear = $currentYear;
        }

        // Extract last two digits of the financial years
        $shortStartYear = substr($financialStartYear, -2);
        $shortEndYear = substr($financialEndYear, -2);

        // Format the ID with four leading zeros
        $formattedId = str_pad($this->id, 4, '0', STR_PAD_LEFT);

        // Generate the final formatted string
        return config('app.note_icon') . "/{$shortStartYear}-{$shortEndYear}/EN/{$formattedId}";
    }
    public function approvalLogs()
    {
        return $this->hasMany(ApprovalLog::class, 'green_note_id');
    }
    public function paymentNotes()
    {
        return $this->hasMany(PaymentNote::class, 'green_note_id');
    }
    public function paymentOneNotes()
    {
        return $this->hasOne(PaymentNote::class, 'green_note_id');
        // return $this->hasMany(PaymentNote::class, 'green_note_id');
    }

    /**
     * Get the user who put this note on hold
     */
    public function holdBy()
    {
        return $this->belongsTo(User::class, 'hold_by');
    }

    /**
     * Check if the note is on hold
     */
    public function isOnHold()
    {
        return $this->status === 'H';
    }

    /**
     * Put the note on hold
     */
    public function putOnHold($reason, $userId)
    {
        $this->update([
            'status' => 'H',
            'hold_reason' => $reason,
            'hold_date' => now(),
            'hold_by' => $userId,
        ]);
    }

    /**
     * Remove the note from hold
     */
    public function removeFromHold($newStatus = 'P')
    {
        $this->update([
            'status' => $newStatus,
            'hold_reason' => null,
            'hold_date' => null,
            'hold_by' => null,
        ]);
    }

    /**
     * Get total invoice value from multiple invoices
     */
    public function getTotalInvoiceValueAttribute()
    {
        if (!empty($this->invoices) && is_array($this->invoices)) {
            return collect($this->invoices)->sum('invoice_value');
        }
        
        return $this->invoice_value ?? 0;
    }

    /**
     * Get invoice count
     */
    public function getInvoiceCountAttribute()
    {
        if (!empty($this->invoices) && is_array($this->invoices)) {
            return count($this->invoices);
        }
        
        return $this->invoice_number ? 1 : 0;
    }
}
