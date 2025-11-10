<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomePageSettings extends Model
{
    protected $fillable = [
        'title',
        'description',
    ];
}
