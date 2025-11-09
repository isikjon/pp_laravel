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

        $hydrated = Girl::hydrate($data['items']);
        $formatted = $hydrated->map(fn (Girl $girl) => $this->formatGirlForCard($girl));

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
                'girls' => $formatted->values(),
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
