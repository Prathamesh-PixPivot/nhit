<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ReimbursementExpenseDetail extends Model
{
    use HasFactory;
    protected $fillable = [
        'reimbursement_note_id', //
        'expense_type',
        'bill_date',
        'bill_number',
        'vendor_name',
        'bill_amount',
        'supporting_available',
        'remarks',
    ];

    public function travelExpense()
    {
        return $this->belongsTo(ReimbursementNote::class);
    }
}
