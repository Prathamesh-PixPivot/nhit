<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Comment extends Model
{
    use HasFactory;
    protected $fillable = ['green_note_id', 'payment_note_id', 'reimbursement_note_id', 'user_id', 'comment', 'parent_id'];
    public function parent()
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }
    public function replies()
    {
        return $this->hasMany(Comment::class, 'parent_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function note()
    {
        return $this->belongsTo(GreenNote::class);
    }
    public function paymentNote()
    {
        return $this->belongsTo(PaymentNote::class);
    }
    public function reimbursementNote()
    {
        return $this->belongsTo(ReimbursementNote::class);
    }
}
