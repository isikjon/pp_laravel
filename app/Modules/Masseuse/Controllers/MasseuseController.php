<?php

namespace App\Modules\Masseuse\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Masseuse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class MasseuseController extends Controller
{
    public function index(Request $request)
    {
        $selectedCity = $request->input('city', $request->cookie('selectedCity', 'moscow'));
        
        if ($request->has('city')) {
            cookie()->queue('selectedCity', $selectedCity, 525600);
        }
        
        $cityName = $selectedCity === 'spb' ? 'Санкт-Петербург' : 'Москва';
        $tableName = $selectedCity === 'spb' ? 'masseuses_spb' : 'masseuses_moscow';
        
        // Жестко используем DB::table() чтобы не было переключения
        $query = DB::table($tableName)->orderBy('sort_order', 'asc');
        
        $perPage = 20;
        $page = $request->get('page', 1);
        
        $total = $query->count();
        $girls = $query->skip(($page - 1) * $perPage)->take($perPage)->get();
        
        // Преобразуем stdClass в объект модели для совместимости
        $girlsFormatted = $girls->map(function ($girl) {
            $girlObj = new Masseuse();
            foreach ((array)$girl as $key => $value) {
                // Декодируем JSON поля
                if (in_array($key, ['meeting_places', 'tariffs', 'services', 'media_images']) && is_string($value)) {
                    $decoded = json_decode($value, true);
                    $girlObj->$key = $decoded !== null ? $decoded : $value;
                } else {
                    $girlObj->$key = $value;
                }
            }
            return $this->formatGirlForCard($girlObj);
        })->values();
        
        if ($request->ajax() || $request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            return response()->json([
                'girls' => $girlsFormatted,
                'hasMore' => ($page * $perPage) < $total,
                'nextPage' => $page + 1,
                'total' => $total,
            ]);
        }
        
        $girls = new \Illuminate\Pagination\LengthAwarePaginator(
            $girlsFormatted,
            $total,
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );
        
        // Сохраняем параметр city в пагинации
        $girls->appends(['city' => $selectedCity]);
        
        return view('masseuse::index', compact('girls', 'cityName', 'selectedCity'));
    }
    
    public function show($id)
    {
        $selectedCity = request()->input('city', request()->cookie('selectedCity', 'moscow'));
        $tableName = $selectedCity === 'spb' ? 'masseuses_spb' : 'masseuses_moscow';
        
        // Жестко используем DB::table() чтобы не было переключения
        $girlDataRaw = DB::table($tableName)->where('anketa_id', $id)->first();
        
        if (!$girlDataRaw) {
            abort(404, 'Девушка не найдена');
        }
        
        // Преобразуем в объект модели
        $girlData = new Masseuse();
        foreach ((array)$girlDataRaw as $key => $value) {
            // Декодируем JSON поля
            if (in_array($key, ['meeting_places', 'tariffs', 'services', 'media_images']) && is_string($value)) {
                $decoded = json_decode($value, true);
                $girlData->$key = $decoded !== null ? $decoded : $value;
            } else {
                $girlData->$key = $value;
            }
        }
        
        $images = $girlData->media_images ?? [];
        $tariffs = $girlData->tariffs ?? [];
        $services = $girlData->services ?? [];
        
        $meetingPlaces = $girlData->meeting_places ?? [];
        $outcallPlaces = [];
        foreach ($meetingPlaces as $place => $available) {
            if ($available === 'да' || $available === true) {
                $outcallPlaces[] = $place;
            }
        }
        
        $metro = $girlData->metro;
        if (is_string($metro) && strpos($metro, 'м. ') === 0) {
            $metro = substr($metro, 3);
        }
        
        $district = $girlData->district;
        if (is_string($district)) {
            $districtParts = explode(',', $district);
            $district = trim(end($districtParts));
        }
        
        $city = $girlData->city ?? 'Москва';
        if (strpos($city, 'г. ') === 0) {
            $city = substr($city, 3);
        }
        
        $girl = [
            'id' => $girlData->anketa_id,
            'name' => $girlData->name,
            'age' => preg_replace('/[^\d]/', '', $girlData->age ?? '18'),
            'mainPhoto' => !empty($images) ? $this->formatImageUrl($images[0]) : asset('img/noimage.png'),
            'hasStatus' => !empty($girlData->media_video),
            'hasVideo' => !empty($girlData->media_video),
            'favorite' => false,
            'verified' => !empty($images) && count($images) >= 3,
            'phone' => $girlData->phone,
            'schedule' => str_replace('можно звонить: ', '', $girlData->call_availability ?? 'круглосуточно'),
            'city' => $city,
            'metro' => $metro ?? 'Центр',
            'district' => $district ?? 'Центральный',
            'hairColor' => $girlData->hair_color ?? 'Брюнетка',
            'intimHaircut' => $girlData->intimate_trim ?? 'Полная депиляция',
            'nationality' => $girlData->nationality ?? 'Русская',
            'height' => $girlData->height ?? 165,
            'weight' => $girlData->weight ?? 55,
            'bust' => $girlData->bust ?? 2,
            'outcall' => isset($meetingPlaces['Выезд']) && ($meetingPlaces['Выезд'] === 'да' || $meetingPlaces['Выезд'] === true),
            'apartment' => isset($meetingPlaces['Апартаменты']) && ($meetingPlaces['Апартаменты'] === 'да' || $meetingPlaces['Апартаменты'] === true),
            'outcallPlaces' => $outcallPlaces,
            'prices' => [
                'outcall' => [
                    '1h' => $this->extractPrice($tariffs, 'Выезд_1 час') ? (int)str_replace(' ', '', $this->extractPrice($tariffs, 'Выезд_1 час')) : null,
                    '2h' => $this->extractPrice($tariffs, 'Выезд_2 часа') ? (int)str_replace(' ', '', $this->extractPrice($tariffs, 'Выезд_2 часа')) : null,
                    'night' => $this->extractPrice($tariffs, 'Выезд_Ночь') ? (int)str_replace(' ', '', $this->extractPrice($tariffs, 'Выезд_Ночь')) : null,
                ],
                'apartment' => [
                    '1h' => $this->extractPrice($tariffs, '1 час') ? (int)str_replace(' ', '', $this->extractPrice($tariffs, '1 час')) : null,
                    '2h' => $this->extractPrice($tariffs, '2 часа') ? (int)str_replace(' ', '', $this->extractPrice($tariffs, '2 часа')) : null,
                    'night' => $this->extractPrice($tariffs, 'Ночь') ? (int)str_replace(' ', '', $this->extractPrice($tariffs, 'Ночь')) : null,
                ],
                'anal' => $this->extractAnalPrice($tariffs, $services),
            ],
            'description' => $girlData->description ?? 'Описание отсутствует',
            'photos' => $this->formatPhotosArray($images),
            'video' => !empty($girlData->media_video) && $girlData->media_video !== 'null' ? $this->formatImageUrl($girlData->media_video) : null,
            'videoPoster' => asset('img/poster.png'),
            'services' => $this->formatServices($services),
            'reviews' => $this->parseReviews($girlData->reviews_comments),
        ];
        
        $similarGirls = $this->getSimilarGirls($girlData);
        
        return view('masseuse::show', compact('girl', 'similarGirls'));
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
        $hasOutcall = isset($meetingPlaces['Выезд']) && ($meetingPlaces['Выезд'] === 'да' || $meetingPlaces['Выезд'] === true);
        $hasApartment = isset($meetingPlaces['Апартаменты']) && ($meetingPlaces['Апартаменты'] === 'да' || $meetingPlaces['Апартаменты'] === true);
        
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
            'detailRoute' => route('masseuse.show', ['id' => $girl->anketa_id]),
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
            $searchKeys = ['Апартаменты_Ночь', 'Аппартаменты_Ночь', 'Выезд_Ночь', 'Ночь', 'ночь'];
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
    
    private function extractAnalPrice($tariffs, $services)
    {
        $hasAnalService = false;
        
        if (is_array($services)) {
            foreach ($services as $key => $value) {
                if (strpos($key, '_') !== false) {
                    list($category, $serviceName) = explode('_', $key, 2);
                    if (stripos($category, 'секс') !== false && stripos($serviceName, 'анальный') !== false) {
                        if ($value === true || $value === 'да') {
                            $hasAnalService = true;
                            break;
                        }
                    }
                }
            }
        }
        
        if (!$hasAnalService) {
            return null;
        }
        
        if (is_array($tariffs)) {
            foreach ($tariffs as $tariffKey => $price) {
                if (stripos($tariffKey, 'анал') !== false || stripos($tariffKey, 'Анал') !== false) {
                    $priceClean = trim($price);
                    if ($priceClean !== '—' && $priceClean !== '-' && !empty($priceClean)) {
                        return (int)str_replace(' ', '', $priceClean);
                    }
                }
            }
        }
        
        return 'by_phone';
    }
    
    private function formatServices($services)
    {
        $formatted = [];
        
        if (!is_array($services)) {
            return $formatted;
        }
        
        foreach ($services as $key => $value) {
            if (strpos($key, '_') !== false) {
                list($category, $serviceName) = explode('_', $key, 2);
                
                if (!isset($formatted[$category])) {
                    $formatted[$category] = [];
                }
                
                $formatted[$category][] = [
                    'name' => $serviceName,
                    'available' => $value === true || $value === 'да',
                    'extra' => false,
                ];
            }
        }
        
        return array_filter($formatted, function($items) {
            return !empty($items);
        });
    }
    
    private function parseReviews($reviewsText)
    {
        if (empty($reviewsText)) {
            return [];
        }
        
        $data = $reviewsText;
        if (is_string($reviewsText)) {
            if ($reviewsText === 'Пока еще никто не оставлял отзыв' || 
                stripos($reviewsText, 'не оставляли комментарии') !== false ||
                $reviewsText === '[]') {
                return [];
            }
            
            $decoded = json_decode($reviewsText, true);
            if (is_array($decoded)) {
                $data = $decoded;
            }
        }
        
        if (!is_array($data)) {
            return [];
        }
        
        $reviews = [];
        foreach ($data as $review) {
            if (is_string($review)) {
                if (stripos($review, 'никто не оставлял') !== false || 
                    stripos($review, 'не оставляли комментарии') !== false) {
                    continue;
                }
                
                $reviews[] = [
                    'author' => 'Аноним',
                    'date' => date('d.m.Y'),
                    'text' => $review,
                    'rating' => 5,
                ];
            }
        }
        
        return $reviews;
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
    
    private function formatPhotosArray($images)
    {
        if (empty($images) || !is_array($images)) {
            return [asset('img/noimage.png')];
        }
        
        $formattedPhotos = [];
        $validPhotos = array_slice($images, 0, 8);
        
        foreach ($validPhotos as $image) {
            if (empty($image) || $image === 'null') {
                continue;
            }
            
            $formattedUrl = $this->formatImageUrl($image);
            
            if ($formattedUrl !== asset('img/noimage.png')) {
                $formattedPhotos[] = $formattedUrl;
            }
        }
        
        if (empty($formattedPhotos)) {
            return [asset('img/noimage.png')];
        }
        
        return $formattedPhotos;
    }
    
    private function getSimilarGirls($currentGirl)
    {
        $selectedCity = request()->input('city', request()->cookie('selectedCity', 'moscow'));
        $tableName = $selectedCity === 'spb' ? 'masseuses_spb' : 'masseuses_moscow';
        
        // Жестко используем DB::table() чтобы не было переключения
        $similarGirlsRaw = DB::table($tableName)
            ->where('anketa_id', '!=', $currentGirl->anketa_id)
            ->whereNotNull('media_images')
            ->where('media_images', '!=', '')
            ->where('media_images', '!=', '[]')
            ->where('media_images', '!=', 'null')
            ->inRandomOrder()
            ->limit(6)
            ->get();
        
        return $similarGirlsRaw->map(function($girlRaw) {
            $girl = new Masseuse();
            foreach ((array)$girlRaw as $key => $value) {
                // Декодируем JSON поля
                if (in_array($key, ['meeting_places', 'tariffs', 'services', 'media_images']) && is_string($value)) {
                    $decoded = json_decode($value, true);
                    $girl->$key = $decoded !== null ? $decoded : $value;
                } else {
                    $girl->$key = $value;
                }
            }
            return $this->formatGirlForCard($girl);
        })->values();
    }
}
