<?php

namespace App\Modules\Salons\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Salon;
use Illuminate\Http\Request;

class SalonsController extends Controller
{
    public function index(Request $request)
    {
        $selectedCity = $request->input('city', $request->cookie('selectedCity', 'moscow'));
        
        if ($request->has('city')) {
            cookie()->queue('selectedCity', $selectedCity, 525600);
        }
        
        $cityName = $selectedCity === 'spb' ? 'Санкт-Петербург' : 'Москва';
        
        $salons = Salon::where('city', $cityName)
            ->orderBy('created_at', 'desc')
            ->paginate(12);
        
        return view('salons::index', compact('salons', 'cityName'));
    }
    
    public function show($id)
    {
        $salon = Salon::where('salon_id', $id)->firstOrFail();
        
        $images = $salon->images ?? [];
        $phones = $salon->phones ?? [];
        $tariffs = $salon->tariffs ?? [];
        
        $salonData = [
            'id' => $salon->salon_id,
            'name' => $salon->name,
            'title' => $salon->title,
            'phones' => $phones,
            'schedule' => $salon->schedule ?? 'Круглосуточно',
            'city' => $salon->city ?? 'Москва',
            'metro' => $salon->metro,
            'district' => $salon->district,
            'coordinates' => $salon->coordinates,
            'map_link' => $salon->map_link,
            'description' => $salon->description,
            'images' => $this->formatImagesArray($images),
            'tariffs' => $tariffs,
            'reviews' => $salon->reviews ?? [],
        ];
        
        return view('salons::show', compact('salonData'));
    }
    
    private function formatImagesArray($images)
    {
        if (empty($images) || !is_array($images)) {
            return [asset('img/noimage.png')];
        }
        
        $formattedImages = [];
        
        foreach ($images as $img) {
            $imageUrl = is_array($img) ? ($img['full'] ?? $img['preview'] ?? '') : $img;
            
            if (empty($imageUrl) || $imageUrl === 'null') {
                continue;
            }
            
            $formattedUrl = $this->formatImageUrl($imageUrl);
            
            if ($formattedUrl !== asset('img/noimage.png')) {
                $formattedImages[] = $formattedUrl;
            }
        }
        
        if (empty($formattedImages)) {
            return [asset('img/noimage.png')];
        }
        
        return $formattedImages;
    }
    
    private function formatImageUrl($imageUrl)
    {
        if (empty($imageUrl) || $imageUrl === 'null' || $imageUrl === null) {
            return asset('img/noimage.png');
        }
        
        if (stripos($imageUrl, 'g_deleted.png') !== false || 
            stripos($imageUrl, 'deleted') !== false ||
            stripos($imageUrl, 'noimage') !== false) {
            return asset('img/noimage.png');
        }
        
        if (strpos($imageUrl, 'http://') === 0 || strpos($imageUrl, 'https://') === 0) {
            if ($this->isValidImageUrl($imageUrl)) {
                return $imageUrl;
            }
            return asset('img/noimage.png');
        }
        
        if (strpos($imageUrl, '/upload') === 0 || strpos($imageUrl, 'upload') === 0) {
            $fullUrl = 'https://msk-z.prostitutki-today.site' . (strpos($imageUrl, '/') === 0 ? '' : '/') . $imageUrl;
            if ($this->isValidImageUrl($fullUrl)) {
                return $fullUrl;
            }
            return asset('img/noimage.png');
        }
        
        return asset($imageUrl);
    }
    
    private function isValidImageUrl($url)
    {
        if (empty($url) || $url === 'null') {
            return false;
        }
        
        if (stripos($url, 'deleted') !== false || stripos($url, 'noimage') !== false) {
            return false;
        }
        
        $parsedUrl = parse_url($url);
        if (!isset($parsedUrl['host']) || empty($parsedUrl['host'])) {
            return false;
        }
        
        return true;
    }
}
