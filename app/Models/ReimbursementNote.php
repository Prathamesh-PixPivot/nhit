<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ReimbursementNote extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id', //
        'select_user_id', //
        'project_id',
        'note_no',
        'date_of_travel',
        'mode_of_travel',
        'travel_mode_eligibility',
        'approver_id',
        'approver_designation',
        'approval_date',
        'purpose_of_travel',
        'adjusted',
        'account_holder',
        'bank_name',
        'bank_account',
        'IFSC_code',
        'status',
        'file_path',
    ];
    protected $casts = [
        'file_path' => 'array',
    ];
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function selectUser()
    {
        return $this->belongsTo(User::class, 'select_user_id');
    }
    public function approver()
    {
        return $this->belongsTo(User::class, 'approver_id');
    }
    public function expenses()
    {
        return $this->hasMany(ReimbursementExpenseDetail::class);
    }
    public function approvals()
    {
        return $this->hasMany(ReimbursementApprovalLog::class);
    }
    public function latestApproval()
    {
        return $this->hasOne(ReimbursementApprovalLog::class)->latest();
    }

    public function project()
    {
        return $this->belongsTo(Vendor::class, 'project_id');
    }
    public function paymentNote()
    {
        return $this->hasMany(PaymentNote::class, 'reimbursement_note_id');
    }
    public function paymentOneNote()
    {
        return $this->hasOne(PaymentNote::class, 'reimbursement_note_id');
    }
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
}
