<?php

namespace App\Modules\Home\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Girl;
use App\Services\GirlQueryService;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class HomeController extends Controller
{
    public function __construct(protected GirlQueryService $girlsQuery)
    {
    }

    public function index(Request $request)
    {
        $selectedCity = $request->input('city', $request->cookie('selectedCity', 'moscow'));

        if ($request->has('city')) {
            cookie()->queue('selectedCity', $selectedCity, 525600);
        }

        $cityName = $selectedCity === 'spb' ? 'Санкт-Петербург' : 'Москва';

        $filters = $this->buildFilters($request, $cityName);

        $perPage = 20;
        $page = (int) $request->input('page', 1);

        $data = $this->girlsQuery->paginate($filters, $perPage, $page);

        $items = collect($data['items']);
        $formatted = $items->map(fn ($girl) => $this->formatGirlForCard($girl))->values();

        $paginatorGirls = new LengthAwarePaginator(
            $formatted,
            $data['meta']['total'],
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        $paginatorGirls->appends($request->except('page'));

        $initialRenderCount = 6;
        $initialGirls = $formatted;
        $preloadedGirls = collect();

        if ($page === 1 && $formatted->count() > $initialRenderCount) {
            $initialGirls = $formatted->slice(0, $initialRenderCount)->values();
            $preloadedGirls = $formatted->slice($initialRenderCount)->values();
        }

        if ($page > 1) {
            $initialGirls = $formatted;
        }

        $hasMoreInitial = $data['meta']['has_more'] ?? false;

        if ($page === 1) {
            $hasMoreInitial = $preloadedGirls->isNotEmpty() || $hasMoreInitial;
        }

        if ($request->ajax() || $request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            return response()->json([
                'girls' => $formatted,
                'hasMore' => $data['meta']['has_more'],
                'nextPage' => $data['meta']['next_page'],
                'total' => $data['meta']['total'],
            ]);
        }

        $metros = $this->girlsQuery->metroList($cityName);

        return view('home::index', [
            'girls' => $paginatorGirls,
            'initialGirls' => $initialGirls,
            'preloadedGirls' => $preloadedGirls,
            'hasMoreInitial' => $hasMoreInitial,
            'metros' => $metros,
            'cityName' => $cityName,
        ]);
    }

    protected function buildFilters(Request $request, string $cityName): array
    {
        return [
            'city' => $cityName,
            'age_from' => $request->filled('age_from') ? (int) $request->input('age_from') : null,
            'age_to' => $request->filled('age_to') ? (int) $request->input('age_to') : null,
            'height_from' => $request->filled('height_from') ? (int) $request->input('height_from') : null,
            'height_to' => $request->filled('height_to') ? (int) $request->input('height_to') : null,
            'weight_from' => $request->filled('weight_from') ? (int) $request->input('weight_from') : null,
            'weight_to' => $request->filled('weight_to') ? (int) $request->input('weight_to') : null,
            'bust_from' => $request->filled('bust_from') ? (int) $request->input('bust_from') : null,
            'bust_to' => $request->filled('bust_to') ? (int) $request->input('bust_to') : null,
            'has_video' => $request->boolean('has_video'),
            'has_reviews' => $request->boolean('has_reviews'),
            'metro' => $request->input('metro'),
            'hair_color' => $request->input('hair_color'),
            'nationality' => $request->input('nationality'),
            'intimate_trim' => $request->input('intimate_trim'),
            'district' => $request->input('district'),
            'verified' => $request->boolean('verified'),
            'services' => $this->extractArray($request->input('service')),
            'places' => $this->extractArray($request->input('place')),
            'finish' => $this->extractArray($request->input('finish')),
            'price_1h_from' => $request->filled('price_1h_from') ? (int) $request->input('price_1h_from') : null,
            'price_1h_to' => $request->filled('price_1h_to') ? (int) $request->input('price_1h_to') : null,
            'price_2h_from' => $request->filled('price_2h_from') ? (int) $request->input('price_2h_from') : null,
            'price_2h_to' => $request->filled('price_2h_to') ? (int) $request->input('price_2h_to') : null,
        ];
    }

    protected function extractArray(mixed $value): array
    {
        if (!is_array($value)) {
            return [];
        }

        return collect($value)
            ->filter(fn ($item) => is_string($item) && $item !== '')
            ->values()
            ->all();
    }
    
    private function formatGirlForCard($girl)
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
        $heightValue = (int) ($payload['height'] ?? 165);
        $weightValue = (int) ($payload['weight'] ?? 55);
        $bustValue = (int) ($payload['bust'] ?? 2);

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
            'height' => $heightValue ?: 165,
            'weight' => $weightValue ?: 55,
            'bust' => $bustValue ?: 2,
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
    
    private function extractParameter($girl, $paramName, $default)
    {
        $payload = $this->normalizeGirlPayload($girl);
        $description = strtolower($this->normalizeString(data_get($payload, 'description'), ''));

        switch ($paramName) {
            case 'рост':
                if (preg_match('/рост[:\s]+(\d{3})/iu', $description, $matches)) {
                    return (int) $matches[1];
                }
                break;
            case 'вес':
                if (preg_match('/вес[:\s]+(\d{2,3})/iu', $description, $matches)) {
                    return (int) $matches[1];
                }
                break;
            case 'грудь':
                if (preg_match('/грудь[:\s]+(\d)/iu', $description, $matches)) {
                    return (int) $matches[1];
                }
                if (preg_match('/(\d)\s*размер/iu', $description, $matches)) {
                    return (int) $matches[1];
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
