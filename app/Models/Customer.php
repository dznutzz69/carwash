<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone',
    ];

    public function appointments()
    {
        return $this->hasMany(Appointment::class, 'customer_id');
    }

    public function payments()
    {
        return $this->hasManyThrough(
            Payment::class,
            Appointment::class,
            'customer_id',
            'appointment_id'
        );
    }
}
