<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Port;

class City extends Model
{
    use HasFactory;
    protected $table = 'cities';

    /* public function port() {
        return $this->belongsTo(Port::class, 'country', 'code');
    } */

} 
