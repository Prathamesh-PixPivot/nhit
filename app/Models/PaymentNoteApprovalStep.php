<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\UsesOrganizationDatabase;

class PaymentNoteApprovalStep extends Model
{
    use UsesOrganizationDatabase;
    
    /**
     * The connection name for the model.
     * Uses organization database
     *
     * @var string
     */
    protected $connection = 'organization';
    
    protected $fillable = ['min_amount', 'max_amount'];

    public function priorities()
    {
        return $this->hasMany(PaymentNoteApprovalPriority::class, 'approval_step_id');
    }
    public function approvers()
    {
        return $this->hasMany(PaymentNoteApprovalPriority::class, 'approval_step_id');
    }
    public function reviewers()
    {
        return $this->belongsToMany(User::class, 'payment_note_approval_priorities', 'approval_step_id', 'reviewer_id');
    }
}
