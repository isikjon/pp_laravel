<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    protected $fillable = [
        'code',
        'name',
        'subdomain',
        'is_active',
        'girls_count',
        'masseuses_count',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
