<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BarberShop extends Model
{
    protected $fillable = [
        'name',
        'address',
        'hours',
        'phone',
        'rating',
        'lat',
        'lng',
        'is_active',
    ];

    protected $casts = [
        'lat' => 'float',
        'lng' => 'float',
        'rating' => 'float',
        'is_active' => 'boolean',
    ];
}
