<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\UsesSharedDatabase;

class Designation extends Model
{
    use UsesSharedDatabase;
    
    /**
     * The connection name for the model.
     * Always use main database for designations
     *
     * @var string
     */
    protected $connection = 'mysql';
    
    protected $fillable = ['name', 'description'];
    public function users()
    {
        return $this->hasMany(User::class);
    }
}
