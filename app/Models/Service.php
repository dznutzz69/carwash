<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $fillable = [
        'name',
        'price',
        'description',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'price'     => 'integer',
    ];

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }
}
