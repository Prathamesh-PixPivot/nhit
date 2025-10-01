<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ApprovalFlow extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'name', 'vendor_id', 'department_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function approvalSteps()
    {
        return $this->hasMany(ApprovalStep::class, 'approval_flow_id');
    }
    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }
    public function department()
    {
        return $this->belongsTo(Department::class);
    }
}
