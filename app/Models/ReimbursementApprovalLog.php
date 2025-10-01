<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ReimbursementApprovalLog extends Model
{
    use HasFactory;
    protected $fillable = [
        'reimbursement_note_id', //
        'reviewer_id',
        'status',
        'comments',
    ];

    public function reimbursementNote()
    {
        return $this->belongsTo(ReimbursementNote::class);
    }
    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }
}
