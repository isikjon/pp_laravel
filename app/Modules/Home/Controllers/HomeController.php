<?php

namespace App\Modules\Home\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Girl;
use App\Models\HomePageSettings;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $query = Girl::query();
        
        $selectedCity = $request->input('city', $request->cookie('selectedCity', 'moscow'));
        
        if ($request->has('city')) {
            cookie()->queue('selectedCity', $selectedCity, 525600);
        }
        
        $cityName = $selectedCity === 'spb' ? 'Санкт-Петербург' : 'Москва';
        $query->where('city', $cityName);
        
        $filterServices = [];
        $filterPlaces = [];
        $filterFinish = [];
        
        if ($request->filled('age_from')) {
            $ageFrom = (int)$request->age_from;
            $query->whereRaw('CAST(REPLACE(REPLACE(REPLACE(age, " ", ""), "лет", ""), "года", "") AS INTEGER) >= ?', [$ageFrom]);
        }
        
        if ($request->filled('age_to')) {
            $ageTo = (int)$request->age_to;
            $query->whereRaw('CAST(REPLACE(REPLACE(REPLACE(age, " ", ""), "лет", ""), "года", "") AS INTEGER) <= ?', [$ageTo]);
        }
        
        if ($request->filled('height_from')) {
            $heightFrom = (int)$request->height_from;
            $query->where('height', '>=', $heightFrom);
        }
        
        if ($request->filled('height_to')) {
            $heightTo = (int)$request->height_to;
            $query->where('height', '<=', $heightTo);
        }
        
        if ($request->filled('weight_from')) {
            $weightFrom = (int)$request->weight_from;
            $query->where('weight', '>=', $weightFrom);
        }
        
        if ($request->filled('weight_to')) {
            $weightTo = (int)$request->weight_to;
            $query->where('weight', '<=', $weightTo);
        }
        
        if ($request->filled('bust_from')) {
            $bustFrom = (int)$request->bust_from;
            $query->where('bust', '>=', $bustFrom);
        }
        
        if ($request->filled('bust_to')) {
            $bustTo = (int)$request->bust_to;
            $query->where('bust', '<=', $bustTo);
        }
        
        if ($request->filled('has_video')) {
            $query->whereNotNull('media_video')->where('media_video', '!=', '')->where('media_video', '!=', 'null');
        }
        
        if ($request->filled('has_reviews')) {
            $query->whereNotNull('reviews_comments')
                  ->where('reviews_comments', '!=', '')
                  ->where('reviews_comments', '!=', '[]')
                  ->where('reviews_comments', 'NOT LIKE', '%никто не оставлял%')
                  ->where('reviews_comments', 'NOT LIKE', '%не оставляли комментарии%');
        }
        
        if ($request->filled('metro')) {
            $metro = $request->metro;
            $query->where(function($q) use ($metro) {
                $q->where('metro', $metro)
                  ->orWhere('metro', 'м. ' . $metro);
            });
        }
        
        // Фильтры по доп параметрам
        if ($request->filled('hair_color')) {
            $query->where('hair_color', $request->hair_color);
        }
        
        if ($request->filled('nationality')) {
            $query->where('nationality', $request->nationality);
        }
        
        if ($request->filled('intimate_trim')) {
            $query->where('intimate_trim', $request->intimate_trim);
        }
        
        if ($request->filled('district')) {
            $query->where('district', $request->district);
        }
        
        $filterVerified = $request->filled('verified');
        
        if ($request->has('service')) {
            $services = $request->input('service');
            if (is_array($services)) {
                $filterServices = $services;
            }
        }
        
        if ($request->has('place')) {
            $places = $request->input('place');
            if (is_array($places)) {
                $filterPlaces = $places;
            }
        }
        
        if ($request->has('finish')) {
            $finish = $request->input('finish');
            if (is_array($finish)) {
                $filterFinish = $finish;
            }
        }
        
        $perPage = 20;
        $page = $request->get('page', 1);
        
        $needsCollectionFilter = !empty($filterServices) || !empty($filterPlaces) || !empty($filterFinish) || $filterVerified || $request->filled('price_1h_from') || $request->filled('price_1h_to') || $request->filled('price_2h_from') || $request->filled('price_2h_to');
        
        if (!$needsCollectionFilter) {
            $total = $query->count();
            $girls = $query->skip(($page - 1) * $perPage)->take($perPage)->get();
        } else {
            $allGirls = $query->get();
            
            if (!empty($filterServices)) {
                $allGirls = $allGirls->filter(function($girl) use ($filterServices) {
                    $services = $girl->services ?? [];
                    foreach ($filterServices as $service) {
                        $parts = explode('_', $service, 2);
                        if (count($parts) === 2) {
                            $category = $parts[0];
                            $serviceType = $parts[1];
                            
                            $found = false;
                            
                            if (isset($services[$service]) && ($services[$service] === 'да' || $services[$service] === true)) {
                                $found = true;
                            }
                            
                            if (!$found && isset($services[$category]) && is_array($services[$category])) {
                                if (isset($services[$category][$serviceType]) && ($services[$category][$serviceType] === 'да' || $services[$category][$serviceType] === true)) {
                                    $found = true;
                                }
                            }
                            
                            if (!$found) {
                                return false;
                            }
                        }
                    }
                    return true;
                });
            }
            
            if (!empty($filterPlaces)) {
                $allGirls = $allGirls->filter(function($girl) use ($filterPlaces) {
                    $places = $girl->meeting_places ?? [];
                    foreach ($filterPlaces as $place) {
                        if (!isset($places[$place]) || ($places[$place] !== 'да' && $places[$place] !== true)) {
                            return false;
                        }
                    }
                    return true;
                });
            }
            
            if (!empty($filterFinish)) {
                $allGirls = $allGirls->filter(function($girl) use ($filterFinish) {
                    $services = $girl->services ?? [];
                    foreach ($filterFinish as $finish) {
                        $found = false;
                        
                        $flatKey = 'Окончание_' . $finish;
                        if (isset($services[$flatKey]) && ($services[$flatKey] === 'да' || $services[$flatKey] === true)) {
                            $found = true;
                        }
                        
                        if (!$found && isset($services['Окончание']) && is_array($services['Окончание'])) {
                            if (isset($services['Окончание'][$finish]) && ($services['Окончание'][$finish] === 'да' || $services['Окончание'][$finish] === true)) {
                                $found = true;
                            }
                        }
                        
                        if (!$found) {
                            return false;
                        }
                    }
                    return true;
                });
            }
            
            if ($filterVerified) {
                $allGirls = $allGirls->filter(function($girl) {
                    $images = $girl->media_images ?? [];
                    return is_array($images) && count($images) >= 3;
                });
            }
            
            if ($request->filled('price_1h_from') || $request->filled('price_1h_to')) {
                $priceFrom = $request->filled('price_1h_from') ? (int)$request->price_1h_from : 0;
                $priceTo = $request->filled('price_1h_to') ? (int)$request->price_1h_to : PHP_INT_MAX;
                
                $allGirls = $allGirls->filter(function($girl) use ($priceFrom, $priceTo) {
                    $tariffs = $girl->tariffs ?? [];
                    $price1h = $this->extractPrice($tariffs, '1 час');
                    if ($price1h) {
                        $priceValue = (int)preg_replace('/[^0-9]/', '', $price1h);
                        return $priceValue >= $priceFrom && $priceValue <= $priceTo;
                    }
                    return false;
                });
            }
            
            if ($request->filled('price_2h_from') || $request->filled('price_2h_to')) {
                $priceFrom = $request->filled('price_2h_from') ? (int)$request->price_2h_from : 0;
                $priceTo = $request->filled('price_2h_to') ? (int)$request->price_2h_to : PHP_INT_MAX;
                
                $allGirls = $allGirls->filter(function($girl) use ($priceFrom, $priceTo) {
                    $tariffs = $girl->tariffs ?? [];
                    $price2h = $this->extractPrice($tariffs, '2 часа');
                    if ($price2h) {
                        $priceValue = (int)preg_replace('/[^0-9]/', '', $price2h);
                        return $priceValue >= $priceFrom && $priceValue <= $priceTo;
                    }
                    return false;
                });
            }
            
            $total = $allGirls->count();
            $girls = $allGirls->forPage($page, $perPage);
        }
        
        $girlsFormatted = $girls->map(function ($girl) {
            return $this->formatGirlForCard($girl);
        })->values();

        $initialRenderCount = 9;
        $initialGirls = $girlsFormatted;
        $preloadedGirls = collect();

        if ($page == 1 && $girlsFormatted->count() > $initialRenderCount) {
            $initialGirls = $girlsFormatted->slice(0, $initialRenderCount)->values();
            $preloadedGirls = $girlsFormatted->slice($initialRenderCount)->values();
        }

        if ($page > 1) {
            $initialGirls = $girlsFormatted;
        }
        
        if ($request->ajax() || $request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            return response()->json([
                'girls' => $girlsFormatted,
                'hasMore' => ($page * $perPage) < $total,
                'nextPage' => $page + 1,
                'total' => $total,
            ]);
        }
        
        $paginatorGirls = new \Illuminate\Pagination\LengthAwarePaginator(
            $girlsFormatted,
            $total,
            $perPage,
            $page,
            ['path' => $request->url()]
        );
        
        $paginatorGirls->appends($request->except('page'));
        
        $metros = Girl::select('metro')
            ->distinct()
            ->where('city', $cityName)
            ->whereNotNull('metro')
            ->where('metro', '!=', '')
            ->pluck('metro')
            ->filter()
            ->sort()
            ->values();
        
        $hasMoreInitial = ($page * $perPage) < $total;
        if ($page == 1) {
            $hasMoreInitial = $preloadedGirls->isNotEmpty() || $hasMoreInitial;
        }

        try {
            $homeSettings = HomePageSettings::first();
            $pageTitle = $homeSettings->title ?? 'ProstitutkiMoscow';
            $pageDescription = $homeSettings->description ?? 'Каталог анкет с подробными фильтрами и проверенными предложениями в Москве и Санкт-Петербурге.';
        } catch (\Exception $e) {
            $pageTitle = 'ProstitutkiMoscow';
            $pageDescription = 'Каталог анкет с подробными фильтрами и проверенными предложениями в Москве и Санкт-Петербурге.';
        }
        
        return view('home::index', [
            'girls' => $paginatorGirls,
            'initialGirls' => $initialGirls,
            'preloadedGirls' => $preloadedGirls,
            'hasMoreInitial' => $hasMoreInitial,
            'metros' => $metros,
            'cityName' => $cityName,
            'pageTitle' => $pageTitle,
            'pageDescription' => $pageDescription,
        ]);
    }
    
    private function formatGirlForCard($girl)
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
            'photo' => !empty($images) ? $this->formatImageUrl($images[0]) : asset('img/noimage.png'),
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
    
    private function extractParameter($girl, $paramName, $default)
    {
        $description = strtolower($girl->description ?? '');
        
        switch($paramName) {
            case 'рост':
                if (preg_match('/рост[:\s]+(\d{3})/iu', $description, $matches)) {
                    return (int)$matches[1];
                }
                break;
            case 'вес':
                if (preg_match('/вес[:\s]+(\d{2,3})/iu', $description, $matches)) {
                    return (int)$matches[1];
                }
                break;
            case 'грудь':
                if (preg_match('/грудь[:\s]+(\d)/iu', $description, $matches)) {
                    return (int)$matches[1];
                }
                if (preg_match('/(\d)\s*размер/iu', $description, $matches)) {
                    return (int)$matches[1];
                }
                break;
        }
        
        return $default;
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
