<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Builder;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    /*  protected $fillable = [
        'name',
        'email',
        'password',
    ]; */

    protected $guarded = [];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = ['password', 'remember_token'];
    /**
     * The event map for the model.
     *
     * @var array<string, string>
     */
    /* protected $dispatchesEvents = [
        'updated' => \App\Events\UserUpdated::class,
    ]; */

    /**
     * The "booted" method of the model.
     */
    /* protected static function booted(): void
    {
        static::updated(function (User $user) {
            // ...
            dd("static::updated User Model");
        });
    } */

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        // using seperate scope class
        // static::addGlobalScope(new HasActiveScope);
        // you can do the same thing using anonymous function
        // let's add another scope using anonymous function
        /* static::addGlobalScope('status', function (Builder $builder) {
            $builder->where('status', 'Active');
        }); */
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function userLoginHistory()
    {
        return $this->hasMany(UserLoginHistory::class);
    }
    public function designation()
    {
        return $this->belongsTo(Designation::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }
    public function approvalSteps()
    {
        return $this->belongsToMany(PaymentNoteApprovalStep::class, 'payment_note_approval_priorities', 'reviewer_id', 'approval_step_id');
    }
}
