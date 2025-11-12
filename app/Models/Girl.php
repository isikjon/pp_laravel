<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Girl extends Model
{
    protected $table = 'girls_moscow';

    protected $fillable = [
        'anketa_id',
        'sort_order',
        'title',
        'name',
        'age',
        'height',
        'weight',
        'bust',
        'phone',
        'call_availability',
        'city',
        'metro',
        'district',
        'map_link',
        'hair_color',
        'nationality',
        'intimate_trim',
        'description',
        'meeting_places',
        'tariffs',
        'services',
        'media_images',
        'media_video',
        'original_url',
        'reviews_comments',
    ];

    protected $casts = [
        'meeting_places' => 'array',
        'tariffs' => 'array',
        'services' => 'array',
        'media_images' => 'array',
    ];

    public static function forCity($city = 'moscow')
    {
        $model = new static();
        $tableName = $city === 'spb' ? 'girls_spb' : 'girls_moscow';
        $model->setTable($tableName);
        return $model;
    }
}
