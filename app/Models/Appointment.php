<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Service;
use App\Models\Payment;

class Appointment extends Model
{
    protected $fillable = [
        'user_id',
        'customer_id',
        'service_id',
        'date',
        'time',
        'status'
    ];

    // Registered Flutter user
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Walk-in customer created by admin
    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    // Service booked
    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    // Payment associated with appointment
    public function payment()
    {
        return $this->hasOne(Payment::class, 'appointment_id');
    }
}
