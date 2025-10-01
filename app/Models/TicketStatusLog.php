<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketStatusLog extends Model
{
    protected $fillable = ['ticket_id', 'status', 'changed_by'];

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    public function changedBy()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
