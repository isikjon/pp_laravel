<?php

namespace App\Modules\StripClubs\Controllers;

use App\Http\Controllers\Controller;
use App\Models\StripClub;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class StripClubsController extends Controller
{
    public function index(Request $request)
    {
        $selectedCity = $request->cookie('selectedCity', 'moscow');
        
        $cityName = $selectedCity === 'spb' ? 'Санкт-Петербург' : 'Москва';
        
        $query = StripClub::where('city', $cityName);
        
        // СТРОГО сортировка по позиции по возрастанию 1,2,3,4,5...
        if (\Schema::hasColumn('strip_clubs', 'sort_order')) {
            $query->orderBy('sort_order', 'asc');
        } else {
            $query->orderBy('id', 'asc');
        }
        
        $clubs = $query->paginate(12);
        
        return view('stripclubs::index', compact('clubs', 'cityName', 'selectedCity'));
    }
    
    public function show($id)
    {
        $club = StripClub::where('club_id', $id)->firstOrFail();
        
        $images = $club->images ?? [];
        $phones = $club->phones ?? [];
        $tariffs = $club->tariffs ?? [];
        
        $clubData = [
            'id' => $club->club_id,
            'name' => $club->name,
            'title' => $club->title,
            'phones' => $phones,
            'schedule' => $club->schedule ?? 'Круглосуточно',
            'city' => $club->city ?? 'Москва',
            'metro' => $club->metro,
            'district' => $club->district,
            'coordinates' => $club->coordinates,
            'map_link' => $club->map_link,
            'description' => $club->description,
            'images' => $this->formatImagesArray($images),
            'tariffs' => $tariffs,
            'reviews' => $club->reviews ?? [],
        ];
        
        return view('stripclubs::show', compact('clubData'));
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
