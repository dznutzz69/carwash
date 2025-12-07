<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

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

    // Flutter Account
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Walk-in Client
    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}
