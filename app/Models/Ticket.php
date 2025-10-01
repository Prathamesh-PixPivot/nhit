<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    protected $fillable = ['user_id', 'name', 'number', 'error', 'description', 'entity_name', 'priority', 'status', 'attachments'];
    protected $casts = [
        'attachments' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function getPriorityLabelAttribute()
    {
        return ['L' => 'Low', 'M' => 'Medium', 'H' => 'High'][$this->priority] ?? $this->priority;
    }

    public function getStatusLabelAttribute()
    {
        return ['O' => 'Open', 'IP' => 'In Progress', 'R' => 'Resolved', 'C' => 'Closed'][$this->status] ?? $this->status;
    }
    public function comments()
    {
        return $this->hasMany(TicketComment::class)->whereNull('parent_id')->latest();
    }
    public function statusLogs()
    {
        return $this->hasMany(TicketStatusLog::class);
    }
}
