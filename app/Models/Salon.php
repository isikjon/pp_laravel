<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Salon extends Model
{
    protected $fillable = [
        'salon_id',
        'url',
        'title',
        'name',
        'phones',
        'schedule',
        'city',
        'metro',
        'district',
        'coordinates',
        'map_link',
        'tariffs',
        'description',
        'images',
        'girls',
        'reviews',
    ];

    protected $casts = [
        'phones' => 'array',
        'tariffs' => 'array',
        'images' => 'array',
        'girls' => 'array',
        'reviews' => 'array',
    ];
}
