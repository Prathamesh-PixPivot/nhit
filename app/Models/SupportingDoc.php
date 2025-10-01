<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SupportingDoc extends Model
{
    use HasFactory;

    protected $fillable = ['green_note_id', 'user_id', 'name', 'file_path'];

    public function greenNote()
    {
        return $this->belongsTo(GreenNote::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}