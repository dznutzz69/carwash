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

    /** 
     * Default role assignment
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($user) {
            if (!$user->role) {
                $user->role = 'customer'; // default for Flutter
            }
        });
    }

    /**
     * Relationship: A user can have many appointments
     */
    public function appointments()
    {
        return $this->hasMany(Appointment::class, 'user_id');
    }

    /**
     * Relationship: A user can have many payments through appointments
     */
    public function payments()
    {
        return $this->hasManyThrough(
            Payment::class,
            Appointment::class,
            'user_id',
            'appointment_id'
        );
    }

    /**
     * Helper: Check if user is admin
     */
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    /**
     * Helper: Check if user is customer
     */
    public function isCustomer()
    {
        return $this->role === 'customer';
    }
}
