<?php

namespace App\Modules\GirlCard\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Girl;

class GirlCardController extends Controller
{
    public function show($id)
    {
        $selectedCity = request()->input('city', request()->cookie('selectedCity', 'moscow'));
        $girlData = Girl::forCity($selectedCity)->where('anketa_id', $id)->first();
        
        if (!$girlData) {
            abort(404, 'Девушка не найдена');
        }
        
        $images = $girlData->media_images ?? [];
        $tariffs = $girlData->tariffs ?? [];
        $services = $girlData->services ?? [];
        
        $meetingPlaces = $girlData->meeting_places ?? [];
        $outcallPlaces = [];
        foreach ($meetingPlaces as $place => $available) {
            if ($available === 'да') {
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
            'outcall' => isset($meetingPlaces['Выезд']) && $meetingPlaces['Выезд'] === 'да',
            'apartment' => isset($meetingPlaces['Апартаменты']) && $meetingPlaces['Апартаменты'] === 'да',
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
        
        return view('girlcard::show', compact('girl', 'similarGirls'));
    }
    
    private function extractPrice($tariffs, $key)
    {
        if (!is_array($tariffs)) {
            return null;
        }
        
        if (isset($tariffs[$key])) {
            $price = trim($tariffs[$key]);
            if ($price !== '—' && $price !== '-' && !empty($price)) {
                return $price;
            }
            return null;
        }
        
        $isOutcall = stripos($key, 'Выезд') !== false;
        $duration = str_replace('Выезд_', '', $key);
        
        foreach ($tariffs as $tariffKey => $price) {
            if (is_array($price)) {
                $categoryClean = preg_replace('/[\s\n\r\t]+/', ' ', trim($tariffKey));
                $isCategoryOutcall = stripos($categoryClean, 'Выезд') !== false;
                $isCategoryApartment = stripos($categoryClean, 'Аппартаменты') !== false || stripos($categoryClean, 'Апартаменты') !== false;
                
                if (($isOutcall && $isCategoryOutcall) || (!$isOutcall && $isCategoryApartment)) {
                    foreach ($price as $dur => $priceVal) {
                        $durationClean = trim($dur);
                        $priceClean = trim($priceVal);
                        
                        if ($priceClean === '—' || $priceClean === '-' || empty($priceClean)) {
                            continue;
                        }
                        
                        if (stripos($durationClean, $duration) !== false) {
                            return $priceClean;
                        }
                    }
                }
            } else {
                $tariffKeyClean = trim($tariffKey);
                $priceClean = trim($price);
                
                if ($priceClean === '—' || $priceClean === '-' || empty($priceClean)) {
                    continue;
                }
                
                if (stripos($tariffKeyClean, $key) !== false) {
                    return $priceClean;
                }
                
                if ($isOutcall && stripos($tariffKeyClean, 'Выезд') !== false && stripos($tariffKeyClean, $duration) !== false) {
                    return $priceClean;
                }
                
                if (!$isOutcall && stripos($tariffKeyClean, $duration) !== false && stripos($tariffKeyClean, 'Выезд') === false) {
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
                } elseif (stripos($key, 'секс') !== false && is_array($value)) {
                    foreach ($value as $serviceName => $available) {
                        if (stripos($serviceName, 'анальный') !== false && ($available === 'да' || $available === true)) {
                            $hasAnalService = true;
                            break 2;
                        }
                    }
                }
            }
        }
        
        if (!$hasAnalService) {
            return null;
        }
        
        if (is_array($tariffs)) {
            foreach ($tariffs as $tariffCategory => $prices) {
                if (!is_array($prices)) {
                    continue;
                }
                
                foreach ($prices as $dur => $price) {
                    $durationClean = strtolower(trim($dur));
                    $priceClean = trim($price);
                    
                    if ($priceClean === '—' || $priceClean === '-' || empty($priceClean)) {
                        continue;
                    }
                    
                    if (stripos($durationClean, 'анал') !== false) {
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
            } else {
                if (!isset($formatted[$key])) {
                    $formatted[$key] = [];
                }
                
                if (is_array($value)) {
                    foreach ($value as $serviceName => $available) {
                        $formatted[$key][] = [
                            'name' => $serviceName,
                            'available' => $available === 'да' || $available === true,
                            'extra' => false,
                        ];
                    }
                }
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
        
        if (count($data) === 1 && is_string($data[0])) {
            if (stripos($data[0], 'никто не оставлял отзыв') !== false || 
                stripos($data[0], 'не оставляли комментарии') !== false) {
                return [];
            }
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
            } elseif (is_array($review)) {
                $text = $review['text'] ?? $review['comment'] ?? '';
                if (empty($text) || 
                    stripos($text, 'никто не оставлял') !== false || 
                    stripos($text, 'не оставляли комментарии') !== false) {
                    continue;
                }
                
                $reviews[] = [
                    'author' => $review['author'] ?? $review['name'] ?? 'Аноним',
                    'date' => $review['date'] ?? date('d.m.Y'),
                    'text' => $text,
                    'rating' => $review['rating'] ?? 5,
                ];
            }
        }
        
        return $reviews;
    }
    
    private function extractParameter($girlData, $paramName, $default)
    {
        $description = strtolower($girlData->description ?? '');
        
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
        $currentAge = (int)preg_replace('/[^\d]/', '', $currentGirl->age ?? '25');
        $currentTariffs = $currentGirl->tariffs ?? [];
        
        $currentPrice = null;
        if (is_array($currentTariffs)) {
            foreach ($currentTariffs as $key => $price) {
                if ($price && $price !== '—' && $price !== '-') {
                    $currentPrice = $price;
                    break;
                }
            }
        }
        
        if (!$currentPrice) {
            $currentPrice = $this->extractPrice($currentTariffs, '1 час');
        }
        if (!$currentPrice) {
            $currentPrice = $this->extractPrice($currentTariffs, 'Выезд_1 час');
        }
        if (!$currentPrice) {
            $currentPrice = $this->extractPrice($currentTariffs, '2 часа');
        }
        if (!$currentPrice) {
            $currentPrice = $this->extractPrice($currentTariffs, 'Выезд_2 часа');
        }
        
        $currentPriceInt = $currentPrice ? (int)str_replace(' ', '', $currentPrice) : null;
        
        $selectedCity = request()->input('city', request()->cookie('selectedCity', 'moscow'));
        $similarGirls = Girl::forCity($selectedCity)
            ->where('anketa_id', '!=', $currentGirl->anketa_id)
            ->whereNotNull('media_images')
            ->where('media_images', '!=', '')
            ->where('media_images', '!=', '[]')
            ->where('media_images', '!=', 'null')
            ->inRandomOrder()
            ->limit(50)
            ->get();
        
        if ($currentPriceInt) {
            $priceMin = $currentPriceInt * 0.7;
            $priceMax = $currentPriceInt * 1.3;
            
            $similarGirls = $similarGirls->filter(function($girl) use ($priceMin, $priceMax) {
                $tariffs = $girl->tariffs ?? [];
                if (!is_array($tariffs)) {
                    return true;
                }
                
                foreach ($tariffs as $key => $price) {
                    if ($price && $price !== '—' && $price !== '-') {
                        $priceInt = (int)str_replace(' ', '', $price);
                        if ($priceInt >= $priceMin && $priceInt <= $priceMax) {
                            return true;
                        }
                    }
                }
                return false;
            });
        }
        
        $result = $similarGirls
            ->take(6)
            ->map(function($girl) {
                return $this->formatGirlForCard($girl);
            })
            ->values();
        
        return $result;
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
}
