<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\File;

class Girl extends Model
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
    ];

    protected function mediaImages(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                // Пробуем получить локальные картинки по anketa_id
                $localImages = $this->getLocalImages();
                if (!empty($localImages)) {
                    return $localImages;
                }
                
                // Fallback на старые URL через прокси
                $images = json_decode($value, true) ?? [];
                return array_map(function ($url) {
                    return $this->proxyImageUrl($url);
                }, $images);
            },
        );
    }

    /**
     * Получить локальные картинки из папки images/{anketa_id}/
     */
    protected function getLocalImages(): array
    {
        $anketaId = $this->anketa_id;
        if (!$anketaId) {
            return [];
        }

        $imagesPath = public_path("images/{$anketaId}");
        
        if (!File::isDirectory($imagesPath)) {
            return [];
        }

        $files = File::files($imagesPath);
        $images = [];

        foreach ($files as $file) {
            $extension = strtolower($file->getExtension());
            if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                $images[] = asset("images/{$anketaId}/" . $file->getFilename());
            }
        }

        // Сортируем по имени файла (01_, 02_, 03_...)
        sort($images);

        return $images;
    }

    protected function proxyImageUrl($url)
    {
        $proxyDomain = config('app.image_proxy_url');
        
        if (!$proxyDomain) {
            return $url;
        }
        
        return str_replace(
            'https://files.prostitutki-today.site/',
            rtrim($proxyDomain, '/') . '/proxy/',
            $url
        );
    }

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        
        $cityCode = getSelectedCity();
        $this->setTable("girls_{$cityCode}");
    }

    public function getTable()
    {
        if (!isset($this->table)) {
            $cityCode = getSelectedCity();
            $this->setTable("girls_{$cityCode}");
        }
        
        return parent::getTable();
    }
}
