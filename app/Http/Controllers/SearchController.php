<?php

namespace App\Http\Controllers;

use App\Models\Girl;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function search(Request $request)
    {
        $query = $request->input('query');
        
        if (empty($query) || strlen($query) < 2) {
            return response()->json([
                'success' => false,
                'message' => 'Query too short'
            ]);
        }
        
        $selectedCity = $request->input('city', $request->cookie('selectedCity', 'moscow'));
        
        $girls = Girl::forCity($selectedCity)
            ->where('name', 'LIKE', '%' . $query . '%')
            ->whereNotNull('media_images')
            ->where('media_images', '!=', '')
            ->where('media_images', '!=', '[]')
            ->where('media_images', '!=', 'null')
            ->limit(10)
            ->get()
            ->map(function($girl) {
                $metro = $girl->metro ?? 'м. Центр';
                if (is_string($metro) && strpos($metro, 'м. ') === 0) {
                    $metro = substr($metro, 3);
                }
                
                $images = $girl->media_images ?? [];
                $photo = !empty($images) ? $this->formatImageUrl($images[0]) : asset('img/noimage.png');
                
                return [
                    'id' => $girl->anketa_id,
                    'name' => $girl->name,
                    'phone' => $girl->phone,
                    'metro' => 'м. ' . $metro,
                    'photo' => $photo,
                ];
            });
        
        return response()->json([
            'success' => true,
            'girls' => $girls
        ]);
    }
    
    private function formatImageUrl($imageUrl)
    {
        // Проверка на пустое значение
        if (empty($imageUrl)) {
            return asset('img/noimage.png');
        }
        
        // Проверка на g_deleted.png и другие несуществующие изображения
        if (stripos($imageUrl, 'g_deleted.png') !== false || 
            stripos($imageUrl, 'deleted') !== false ||
            stripos($imageUrl, 'noimage') !== false) {
            return asset('img/noimage.png');
        }
        
        // Полный URL
        if (filter_var($imageUrl, FILTER_VALIDATE_URL)) {
            return $imageUrl;
        }
        
        // URL с upload
        if (strpos($imageUrl, '/upload') === 0 || strpos($imageUrl, 'upload') === 0) {
            return 'https://msk-z.prostitutki-today.site' . (strpos($imageUrl, '/') === 0 ? '' : '/') . $imageUrl;
        }
        
        return asset($imageUrl);
    }
}

