<?php

namespace App\Http\Controllers;

use App\Models\Girl;
use App\Services\GirlQueryService;
use Illuminate\Http\Request;

class MetroController extends Controller
{
    public function __construct(protected GirlQueryService $girlsQuery)
    {
    }

    public function getMetroList(Request $request)
    {
        $selectedCity = $request->input('city', $request->cookie('selectedCity', 'moscow'));
        $cityName = $selectedCity === 'spb' ? 'Санкт-Петербург' : 'Москва';

        $metroList = $this->girlsQuery->metroList($cityName);

        return response()->json([
            'success' => true,
            'metros' => $metroList,
        ]);
    }

    public function getGirlsByMetro(Request $request)
    {
        $metro = $request->input('metro');

        if (!$metro) {
            return response()->json([
                'success' => false,
                'message' => 'Metro parameter is required',
            ], 400);
        }

        $selectedCity = $request->input('city', $request->cookie('selectedCity', 'moscow'));
        $cityName = $selectedCity === 'spb' ? 'Санкт-Петербург' : 'Москва';

        $data = $this->girlsQuery->paginate([
            'city' => $cityName,
            'metro' => $metro,
            'has_video' => false,
            'has_reviews' => false,
            'verified' => false,
            'services' => [],
            'places' => [],
            'finish' => [],
            'age_from' => null,
            'age_to' => null,
            'height_from' => null,
            'height_to' => null,
            'weight_from' => null,
            'weight_to' => null,
            'bust_from' => null,
            'bust_to' => null,
            'hair_color' => null,
            'nationality' => null,
            'intimate_trim' => null,
            'district' => null,
            'price_1h_from' => null,
            'price_1h_to' => null,
            'price_2h_from' => null,
            'price_2h_to' => null,
        ], 50, 1);

        $girls = Girl::hydrate($data['items'])
            ->map(fn (Girl $girl) => $this->formatGirl($girl));

        return response()->json([
            'success' => true,
            'girls' => $girls,
            'metro' => $metro,
        ]);
    }

    private function formatGirl(Girl $girl): array
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
            'price1h' => $price1h ? (int) str_replace(' ', '', $price1h) : 5000,
            'price2h' => $price2h ? (int) str_replace(' ', '', $price2h) : 10000,
            'priceAnal' => null,
            'priceNight' => $priceNight ? (int) str_replace(' ', '', $priceNight) : 20000,
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

    private function formatImageUrl($imageUrl)
    {
        if (empty($imageUrl)) {
            return asset('img/noimage.png');
        }

        if (stripos($imageUrl, 'g_deleted.png') !== false ||
            stripos($imageUrl, 'deleted') !== false ||
            stripos($imageUrl, 'noimage') !== false) {
            return asset('img/noimage.png');
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

