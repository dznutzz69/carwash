<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function appointments()
    {
        return $this->hasMany(Appointment::class, 'user_id');
    }

    public function payments()
    {
        return $this->hasManyThrough(
            Payment::class,
            Appointment::class,
            'user_id',
            'appointment_id'
        );
    }
}
