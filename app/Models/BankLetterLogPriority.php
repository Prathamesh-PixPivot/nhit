<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;
use App\Traits\UsesOrganizationDatabase;

class BankLetterLogPriority extends Pivot
{
    use HasFactory, UsesOrganizationDatabase;
    
    /**
     * The connection name for the model.
     * Uses organization database
     *
     * @var string
     */
    protected $connection = 'organization';

    protected $table = 'bank_letter_log_priority';

    protected $fillable = ['bank_letter_approval_log_id', 'priority_id'];
    public function logs()
    {
        return $this->belongsToMany(BankLetterApprovalLog::class, 'bank_letter_log_priority', 'priority_id', 'bank_letter_approval_log_id')->withTimestamps();
    }
    public function priorities()
    {
        return $this->belongsToMany(BankLetterApprovalPriority::class, 'bank_letter_log_priority', 'bank_letter_approval_log_id', 'priority_id')->withTimestamps();
    }
    public function priority()
    {
        return $this->belongsTo(BankLetterApprovalPriority::class, 'priority_id');
    }
}
