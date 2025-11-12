<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StripClub extends Model
{
    protected $fillable = [
        'club_id',
        'sort_order',
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
        'reviews',
    ];

    protected $casts = [
        'phones' => 'array',
        'tariffs' => 'array',
        'images' => 'array',
        'reviews' => 'array',
    ];
}
