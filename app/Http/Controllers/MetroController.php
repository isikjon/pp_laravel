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

        $girls = collect($data['items'])
            ->map(fn ($girl) => $this->formatGirl($girl))
            ->values();

        return response()->json([
            'success' => true,
            'girls' => $girls,
            'metro' => $metro,
        ]);
    }

    private function formatGirl($girl): array
    {
        $payload = $this->normalizeGirlPayload($girl);

        $images = $this->ensureArray(data_get($payload, 'media_images'));
        $tariffs = $this->ensureArray(data_get($payload, 'tariffs'));
        $meetingPlaces = $this->ensureArray(data_get($payload, 'meeting_places'));

        $price1h = $this->extractPrice($tariffs, '1 час');
        $price2h = $this->extractPrice($tariffs, '2 часа');
        $priceNight = $this->extractPrice($tariffs, 'Ночь');

        $metroRaw = $this->normalizeString(data_get($payload, 'metro'), 'м. Центр');
        $metro = str_starts_with($metroRaw, 'м. ') ? substr($metroRaw, 3) : $metroRaw;

        $cityRaw = $this->normalizeString(data_get($payload, 'city'), 'г. Москва');
        $city = str_starts_with($cityRaw, 'г. ') ? substr($cityRaw, 3) : $cityRaw;

        $hasOutcall = $this->meetingPlaceAvailable($meetingPlaces, 'Выезд');
        $hasApartment = $this->meetingPlaceAvailable($meetingPlaces, 'Апартаменты');

        $ageValue = $this->normalizeString(data_get($payload, 'age'), '18');
        $photoSource = $this->resolvePrimaryMedia($images);

        return [
            'id' => $payload['anketa_id'] ?? null,
            'name' => $this->normalizeString(data_get($payload, 'name'), 'Без имени'),
            'age' => preg_replace('/[^\d]/', '', $ageValue),
            'photo' => $photoSource ? $this->formatImageUrl($photoSource) : asset('img/noimage.png'),
            'hasStatus' => $this->hasMediaValue(data_get($payload, 'media_video')),
            'hasVideo' => $this->hasMediaValue(data_get($payload, 'media_video')),
            'favorite' => false,
            'phone' => $this->normalizeString(data_get($payload, 'phone'), ''),
            'city' => $city,
            'metro' => 'м. ' . $metro,
            'height' => (int) ($payload['height'] ?? 165) ?: 165,
            'weight' => (int) ($payload['weight'] ?? 55) ?: 55,
            'bust' => (int) ($payload['bust'] ?? 2) ?: 2,
            'price1h' => $price1h ? (int) str_replace(' ', '', $price1h) : 5000,
            'price2h' => $price2h ? (int) str_replace(' ', '', $price2h) : 10000,
            'priceAnal' => null,
            'priceNight' => $priceNight ? (int) str_replace(' ', '', $priceNight) : 20000,
            'verified' => count($images) >= 3 ? 'Фото проверены' : null,
            'outcall' => $hasOutcall,
            'apartment' => $hasApartment,
        ];
    }

    private function extractPrice($tariffs, $key)
    {
        $tariffs = $this->ensureArray($tariffs);

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
                $price = $this->normalizeString($tariffs[$searchKey]);

                if ($price !== '' && $price !== '—' && $price !== '-' && $price !== 'null') {
                    return $price;
                }
            }
        }

        foreach ($tariffs as $tariffKey => $price) {
            $priceClean = $this->normalizeString($price);

            if ($priceClean === '' || $priceClean === '—' || $priceClean === '-' || $priceClean === 'null') {
                continue;
            }

            if (is_string($tariffKey) && stripos($tariffKey, $key) !== false) {
                return $priceClean;
            }

            if (is_array($price)) {
                foreach ($price as $nestedKey => $nestedPrice) {
                    $nestedClean = $this->normalizeString($nestedPrice);

                    if ($nestedClean === '' || $nestedClean === '—' || $nestedClean === '-' || $nestedClean === 'null') {
                        continue;
                    }

                    if (is_string($nestedKey) && stripos($nestedKey, $key) !== false) {
                        return $nestedClean;
                    }
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

    private function normalizeGirlPayload($girl): array
    {
        if ($girl instanceof Girl) {
            return $girl->toArray();
        }

        if ($girl instanceof \Illuminate\Contracts\Support\Arrayable) {
            return $girl->toArray();
        }

        if (is_string($girl)) {
            $decoded = json_decode($girl, true);

            if (is_array($decoded)) {
                return $decoded;
            }
        }

        if (is_object($girl)) {
            return (array) $girl;
        }

        if (!is_array($girl)) {
            return [];
        }

        return $girl;
    }

    private function ensureArray(mixed $value): array
    {
        if (is_array($value)) {
            return $value;
        }

        if (is_string($value) && $value !== '') {
            $decoded = json_decode($value, true);

            if (is_array($decoded)) {
                return $decoded;
            }
        }

        return [];
    }

    private function normalizeString(mixed $value, string $default = ''): string
    {
        if (is_string($value)) {
            return $value;
        }

        if (is_numeric($value)) {
            return (string) $value;
        }

        return $default;
    }

    private function meetingPlaceAvailable(array $places, string $key): bool
    {
        if (!isset($places[$key])) {
            return false;
        }

        $value = $places[$key];

        if (is_bool($value)) {
            return $value;
        }

        if (is_string($value)) {
            return in_array(mb_strtolower($value), ['да', 'yes', 'true', '1'], true);
        }

        return false;
    }

    private function resolvePrimaryMedia(array $media): ?string
    {
        foreach ($media as $entry) {
            if (is_string($entry) && $entry !== '' && $entry !== 'null') {
                return $entry;
            }

            if (is_array($entry)) {
                foreach (['full', 'url', 'preview', 'path', 0] as $key) {
                    if (isset($entry[$key]) && is_string($entry[$key]) && $entry[$key] !== '' && $entry[$key] !== 'null') {
                        return $entry[$key];
                    }
                }
            }
        }

        return null;
    }

    private function hasMediaValue(mixed $value): bool
    {
        if (is_bool($value)) {
            return $value;
        }

        if (is_string($value)) {
            return trim($value) !== '' && $value !== 'null';
        }

        return false;
    }
}

