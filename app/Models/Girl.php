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
        $tableName = $city === 'spb' ? 'girls_spb' : 'girls_moscow';
        return (new static())->setTable($tableName);
    }

    public function newQuery()
    {
        if (!$this->getTable() || $this->getTable() === 'girls_moscow') {
            $city = request()->input('city', request()->cookie('selectedCity', 'moscow'));
            $tableName = $city === 'spb' ? 'girls_spb' : 'girls_moscow';
            $this->setTable($tableName);
        }
        return parent::newQuery();
    }
}
