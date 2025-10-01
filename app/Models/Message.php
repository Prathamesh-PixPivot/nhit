<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;
    protected $fillable = ['conversation_id', 'sender_id', 'recipient_id', 'body', 'is_read', 'attachment'];

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function recipient()
    {
        return $this->belongsTo(User::class, 'recipient_id');
    }

    public function conversation()
    {
        return $this->belongsTo(Conversation::class);
    }

    public function scopeNotTrashed($query)
    {
        return $query->whereNull('trashed_at');
    }

    public function scopeOnlyTrashed($query)
    {
        return $query->whereNotNull('trashed_at');
    }
    public function folders()
    {
        return $this->belongsToMany(Folder::class);
    }
    public function labels()
    {
        return $this->belongsToMany(Label::class);
    }
}
