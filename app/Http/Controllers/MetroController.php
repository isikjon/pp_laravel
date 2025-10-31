<?php

namespace App\Http\Controllers;

use App\Models\Girl;
use Illuminate\Http\Request;

class MetroController extends Controller
{
    public function getMetroList()
    {
        $metroList = Girl::select('metro')
            ->whereNotNull('metro')
            ->where('metro', '!=', '')
            ->get()
            ->pluck('metro')
            ->map(function($metro) {
                if (is_string($metro) && strpos($metro, 'м. ') === 0) {
                    return trim(substr($metro, 3));
                }
                return trim($metro);
            })
            ->filter(function($metro) {
                return !empty($metro) && strlen($metro) > 1;
            })
            ->unique()
            ->sort()
            ->values();
        
        return response()->json([
            'success' => true,
            'metros' => $metroList
        ]);
    }
    
    public function getGirlsByMetro(Request $request)
    {
        $metro = $request->input('metro');
        
        if (!$metro) {
            return response()->json([
                'success' => false,
                'message' => 'Metro parameter is required'
            ], 400);
        }
        
        $girls = Girl::where(function($query) use ($metro) {
                $query->where('metro', $metro)
                      ->orWhere('metro', 'м. ' . $metro);
            })
            ->whereNotNull('media_images')
            ->where('media_images', '!=', '')
            ->where('media_images', '!=', '[]')
            ->where('media_images', '!=', 'null')
            ->limit(50)
            ->get()
            ->map(function($girl) {
                return $this->formatGirl($girl);
            });
        
        return response()->json([
            'success' => true,
            'girls' => $girls,
            'metro' => $metro
        ]);
    }
    
    private function formatGirl($girl)
    {
        $images = $girl->media_images ?? [];
        $tariffs = $girl->tariffs ?? [];
        
        $price1h = $this->extractPrice($tariffs, '1 час');
        $price2h = $this->extractPrice($tariffs, '2 часа');
        $priceNight = $this->extractPrice($tariffs, 'Ночь');
        
        $metro = $girl->metro ?? 'м. Центр';
        if (is_string($metro) && strpos($metro, 'м. ') === 0) {
            $metro = substr($metro, 3);
        }
        
        $city = $girl->city ?? 'г. Москва';
        if (strpos($city, 'г. ') === 0) {
            $city = substr($city, 3);
        }
        
        $meetingPlaces = $girl->meeting_places ?? [];
        $hasOutcall = isset($meetingPlaces['Выезд']) && $meetingPlaces['Выезд'] === 'да';
        $hasApartment = isset($meetingPlaces['Апартаменты']) && $meetingPlaces['Апартаменты'] === 'да';
        
        return [
            'id' => $girl->anketa_id,
            'name' => $girl->name,
            'age' => preg_replace('/[^\d]/', '', $girl->age ?? '18'),
            'photo' => !empty($images) ? $this->formatImageUrl($images[0]) : asset('img/photoGirl-1.png'),
            'hasStatus' => !empty($girl->media_video),
            'hasVideo' => !empty($girl->media_video),
            'favorite' => false,
            'phone' => $girl->phone,
            'city' => $city,
            'metro' => 'м. ' . $metro,
            'height' => $girl->height ?? 165,
            'weight' => $girl->weight ?? 55,
            'bust' => $girl->bust ?? 2,
            'price1h' => $price1h ? (int)str_replace(' ', '', $price1h) : 5000,
            'price2h' => $price2h ? (int)str_replace(' ', '', $price2h) : 10000,
            'priceAnal' => null,
            'priceNight' => $priceNight ? (int)str_replace(' ', '', $priceNight) : 20000,
            'verified' => (!empty($images) && count($images) >= 3) ? 'Фото проверены' : null,
            'outcall' => $hasOutcall,
            'apartment' => $hasApartment,
        ];
    }
    
    private function extractPrice($tariffs, $key)
    {
        if (!is_array($tariffs)) {
            return null;
        }
        
        $searchKeys = [];
        
        if ($key === '1 час') {
            $searchKeys = ['Апартаменты_1 час', 'Аппартаменты_1 час', 'Выезд_1 час', '1 час'];
        } elseif ($key === '2 часа') {
            $searchKeys = ['Апартаменты_2 часа', 'Аппартаменты_2 часа', 'Выезд_2 часа', '2 часа'];
        } elseif ($key === 'Ночь') {
            $searchKeys = ['Апартаменты_Ночь', 'Аппартаменты_Ночь', 'Выезд_Ночь', 'Ночь'];
        }
        
        foreach ($searchKeys as $searchKey) {
            if (isset($tariffs[$searchKey])) {
                $price = trim($tariffs[$searchKey]);
                if ($price !== '—' && $price !== '-' && !empty($price) && $price !== 'null') {
                    return $price;
                }
            }
        }
        
        foreach ($tariffs as $tariffKey => $price) {
            if (stripos($tariffKey, $key) !== false) {
                $priceClean = trim($price);
                if ($priceClean !== '—' && $priceClean !== '-' && !empty($priceClean) && $priceClean !== 'null') {
                    return $priceClean;
                }
            }
        }
        
        return null;
    }
    
    private function extractParameter($girlData, $paramName, $default)
    {
        $parameters = $girlData->parameters ?? [];
        
        if (!is_array($parameters)) {
            return $default;
        }
        
        foreach ($parameters as $param) {
            if (!is_array($param)) continue;
            
            $name = strtolower($param['name'] ?? '');
            $value = $param['value'] ?? null;
            
            if (strpos($name, strtolower($paramName)) !== false) {
                if (is_numeric($value)) {
                    return (int)$value;
                }
                
                preg_match('/\d+/', $value, $matches);
                if (!empty($matches)) {
                    return (int)$matches[0];
                }
            }
        }
        
        return $default;
    }
    
    private function formatImageUrl($imageUrl)
    {
        if (empty($imageUrl)) {
            return asset('img/photoGirl-1.png');
        }
        
        if (filter_var($imageUrl, FILTER_VALIDATE_URL)) {
            return $imageUrl;
        }
        
        if (strpos($imageUrl, '/upload') === 0 || strpos($imageUrl, 'upload') === 0) {
            return 'https://msk-z.prostitutki-today.site' . (strpos($imageUrl, '/') === 0 ? '' : '/') . $imageUrl;
        }
        
        return asset($imageUrl);
    }
}

