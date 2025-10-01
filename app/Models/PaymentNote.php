<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PaymentNote extends Model
{
    use HasFactory;

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
    ];

    protected $casts = [
        'add_particulars' => 'array',
        'less_particulars' => 'array',
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
}
