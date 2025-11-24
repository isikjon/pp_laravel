<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Masseuse extends Model
{
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

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        
        $cityCode = getSelectedCity();
        $this->setTable("masseuses_{$cityCode}");
    }

    public function getTable()
    {
        if (!isset($this->table)) {
            $cityCode = getSelectedCity();
            $this->setTable("masseuses_{$cityCode}");
        }
        
        return parent::getTable();
    }
}
