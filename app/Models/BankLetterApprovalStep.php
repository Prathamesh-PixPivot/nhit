<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\UsesOrganizationDatabase;

class BankLetterApprovalStep extends Model
{
    use UsesOrganizationDatabase;
    
    /**
     * The connection name for the model.
     * Uses organization database for bank letter approval steps
     *
     * @var string
     */
    protected $connection = 'organization';
    
    protected $fillable = ['min_amount', 'max_amount'];

    public function approvers()
    {
        return $this->hasMany(BankLetterApprovalPriority::class, 'approval_step_id');
    }
    public function reviewers()
    {
        return $this->belongsToMany(User::class, 'payment_note_approval_priorities', 'approval_step_id', 'reviewer_id');
    }
}
