<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\UsesOrganizationDatabase;

class PaymentNote extends Model
{
    use HasFactory, UsesOrganizationDatabase;
    
    /**
     * The connection name for the model.
     * Always use organization database for payment notes
     *
     * @var string
     */
    protected $connection = 'organization';

    protected $fillable = [
        'green_note_id', //
        'reimbursement_note_id', //
        'user_id',
        'note_no',
        'subject',
        'vendor_code',
        'recommendation_of_payment',
        'add_particulars',
        'less_particulars',
        'net_payable_round_off',
        'status',
        'utr_no',
        'utr_date',
        'is_draft',
        'auto_created',
        'created_by',
    ];

    protected $casts = [
        'add_particulars' => 'array',
        'less_particulars' => 'array',
        'is_draft' => 'boolean',
        'auto_created' => 'boolean',
    ];
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function greenNote()
    {
        return $this->belongsTo(GreenNote::class);
    }
    public function reimbursementNote()
    {
        return $this->belongsTo(ReimbursementNote::class);
    }
    public function getFormattedOrderNoAttribute()
    {
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
        return config('app.note_icon') . "/{$shortStartYear}-{$shortEndYear}/PN/{$formattedId}";
    }
    public function paymentApprovalLogs()
    {
        return $this->hasMany(PaymentNoteApprovalLog::class, 'payment_note_id');
    }
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Get the user who created this payment note
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Create a draft payment note from an approved green note
     */
    public static function createDraftFromGreenNote($greenNote, $userId = null)
    {
        $userId = $userId ?? auth()->id();
        
        // Generate payment note number
        $orderNumber = static::generateOrderNumber();
        
        // Create draft payment note
        $paymentNote = static::create([
            'green_note_id' => $greenNote->id,
            'user_id' => $userId,
            'created_by' => $userId,
            'note_no' => $orderNumber,
            'subject' => 'Payment for ' . $greenNote->brief_of_goods_services,
            'recommendation_of_payment' => 'Proposed to release the payment',
            'status' => 'D', // Draft status
            'is_draft' => true,
            'auto_created' => true,
        ]);

        return $paymentNote;
    }

    /**
     * Generate payment note order number
     */
    public static function generateOrderNumber()
    {
        $currentMonth = date('n');
        $currentYear = date('Y');
        $previousYear = $currentYear - 1;

        if ($currentMonth >= 4) {
            $financialStartYear = $currentYear;
            $financialEndYear = $currentYear + 1;
        } else {
            $financialStartYear = $previousYear;
            $financialEndYear = $currentYear;
        }

        $shortStartYear = substr($financialStartYear, -2);
        $shortEndYear = substr($financialEndYear, -2);

        // Get the last payment note ID for sequence
        $lastNote = static::orderBy('id', 'desc')->first();
        $nextId = $lastNote ? $lastNote->id + 1 : 1;
        $formattedId = str_pad($nextId, 4, '0', STR_PAD_LEFT);

        return config('app.note_icon', 'W') . "/{$shortStartYear}-{$shortEndYear}/PN/{$formattedId}";
    }

    /**
     * Check if payment note is a draft
     */
    public function isDraft()
    {
        return $this->is_draft || $this->status === 'D';
    }

    /**
     * Convert draft to active payment note
     */
    public function convertToActive()
    {
        $this->update([
            'is_draft' => false,
            'status' => 'P', // Pending status
        ]);
    }
}
