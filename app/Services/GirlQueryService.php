<?php

namespace App\Services;

use App\Models\Girl;
use Illuminate\Contracts\Cache\Repository as CacheRepository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class GirlQueryService
{
    protected int $ttl = 300;

    public function __construct(protected CacheRepository $cache)
    {
    }

    public function paginate(array $filters, int $perPage, int $page): array
    {
        $payload = [
            'filters' => $filters,
            'per_page' => $perPage,
            'page' => $page,
        ];

        $cacheKey = 'girls:list:' . md5(serialize($payload));

        $cached = $this->cache->get($cacheKey);

        if ($cached !== null) {
            return $cached;
        }

        $query = Girl::query()
            ->select([
                'id',
                'anketa_id',
                'name',
                'age',
                'height',
                'weight',
                'bust',
                'phone',
                'call_availability',
                'city',
                'metro',
                'district',
                'hair_color',
                'nationality',
                'intimate_trim',
                'meeting_places',
                'tariffs',
                'services',
                'media_images',
                'media_video',
                'original_url',
                'reviews_comments',
                'description',
            ]);

        $this->applyFilters($query, $filters);

        $paginator = $query
            ->orderByDesc('id')
            ->paginate($perPage, ['*'], 'page', $page);

        $items = $paginator->getCollection()
            ->map(fn (Girl $girl) => $girl->attributesToArray())
            ->all();

        $payload = [
            'items' => $items,
            'meta' => [
                'total' => $paginator->total(),
                'per_page' => $paginator->perPage(),
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'next_page' => $paginator->currentPage() < $paginator->lastPage() ? $paginator->currentPage() + 1 : null,
                'has_more' => $paginator->hasMorePages(),
            ],
        ];

        $this->cache->put($cacheKey, $payload, $this->ttl);

        return $payload;
    }

    public function metroList(string $city): Collection
    {
        $cacheKey = 'girls:metro:' . md5($city);

        return $this->cache->remember($cacheKey, $this->ttl, function () use ($city) {
            return Girl::query()
                ->select('metro')
                ->where('city', $city)
                ->whereNotNull('metro')
                ->where('metro', '!=', '')
                ->pluck('metro')
                ->map(fn ($metro) => $this->trimMetroPrefix((string) $metro))
                ->filter(fn ($metro) => $metro !== '')
                ->unique()
                ->sort()
                ->values();
        });
    }

    protected function applyFilters(Builder $query, array $filters): void
    {
        $city = $filters['city'] ?? null;

        if ($city !== null && $city !== '') {
            $query->where('city', $city);
        }

        $ageFrom = $filters['age_from'] ?? null;
        $ageTo = $filters['age_to'] ?? null;

        if ($ageFrom !== null) {
            $query->whereRaw($this->ageExpression() . ' >= ?', [$ageFrom]);
        }

        if ($ageTo !== null) {
            $query->whereRaw($this->ageExpression() . ' <= ?', [$ageTo]);
        }

        $heightFrom = $filters['height_from'] ?? null;
        $heightTo = $filters['height_to'] ?? null;

        if ($heightFrom !== null) {
            $query->where('height', '>=', $heightFrom);
        }

        if ($heightTo !== null) {
            $query->where('height', '<=', $heightTo);
        }

        $weightFrom = $filters['weight_from'] ?? null;
        $weightTo = $filters['weight_to'] ?? null;

        if ($weightFrom !== null) {
            $query->where('weight', '>=', $weightFrom);
        }

        if ($weightTo !== null) {
            $query->where('weight', '<=', $weightTo);
        }

        $bustFrom = $filters['bust_from'] ?? null;
        $bustTo = $filters['bust_to'] ?? null;

        if ($bustFrom !== null) {
            $query->where('bust', '>=', $bustFrom);
        }

        if ($bustTo !== null) {
            $query->where('bust', '<=', $bustTo);
        }

        if (!empty($filters['has_video'])) {
            $query->whereNotNull('media_video')
                ->where('media_video', '!=', '')
                ->where('media_video', '!=', 'null');
        }

        if (!empty($filters['has_reviews'])) {
            $query->whereNotNull('reviews_comments')
                ->where('reviews_comments', '!=', '')
                ->where('reviews_comments', '!=', '[]')
                ->where('reviews_comments', 'NOT LIKE', '%никто не оставлял%')
                ->where('reviews_comments', 'NOT LIKE', '%не оставляли комментарии%');
        }

        $metroInput = $this->sanitizeString($filters['metro'] ?? '');

        if ($metroInput !== '') {
            $metroClean = $this->trimMetroPrefix($metroInput);

            $query->where(function (Builder $metroQuery) use ($metroInput, $metroClean) {
                $metroQuery->where('metro', $metroInput);

                if ($metroClean !== $metroInput) {
                    $metroQuery->orWhere('metro', $metroClean);
                }

                $metroQuery->orWhere('metro', 'м. ' . $metroClean);
            });
        }

        $hair = $this->sanitizeString($filters['hair_color'] ?? '');

        if ($hair !== '') {
            $query->where('hair_color', $hair);
        }

        $nationality = $this->sanitizeString($filters['nationality'] ?? '');

        if ($nationality !== '') {
            $query->where('nationality', $nationality);
        }

        $trim = $this->sanitizeString($filters['intimate_trim'] ?? '');

        if ($trim !== '') {
            $query->where('intimate_trim', $trim);
        }

        $district = $this->sanitizeString($filters['district'] ?? '');

        if ($district !== '') {
            $query->where('district', $district);
        }

        if (!empty($filters['verified'])) {
            $query->whereRaw('JSON_LENGTH(media_images) >= 3');
        }

        $services = $this->sanitizeArray($filters['services'] ?? []);
        $places = $this->sanitizeArray($filters['places'] ?? []);
        $finishes = $this->sanitizeArray($filters['finish'] ?? []);

        if (!empty($services)) {
            $this->applyServiceFilters($query, $services);
        }

        if (!empty($places)) {
            $this->applyMeetingPlaceFilters($query, $places);
        }

        if (!empty($finishes)) {
            $this->applyFinishFilters($query, $finishes);
        }

        $price1hFrom = $filters['price_1h_from'] ?? null;
        $price1hTo = $filters['price_1h_to'] ?? null;

        if ($price1hFrom !== null || $price1hTo !== null) {
            $this->applyPriceFilter($query, 'tariffs', [
                ['Апартаменты_1 час'],
                ['Аппартаменты_1 час'],
                ['Выезд_1 час'],
                ['1 час'],
            ], $price1hFrom, $price1hTo);
        }

        $price2hFrom = $filters['price_2h_from'] ?? null;
        $price2hTo = $filters['price_2h_to'] ?? null;

        if ($price2hFrom !== null || $price2hTo !== null) {
            $this->applyPriceFilter($query, 'tariffs', [
                ['Апартаменты_2 часа'],
                ['Аппартаменты_2 часа'],
                ['Выезд_2 часа'],
                ['2 часа'],
            ], $price2hFrom, $price2hTo);
        }
    }

    protected function applyServiceFilters(Builder $query, array $services): void
    {
        foreach ($services as $service) {
            $segments = $this->splitServiceKey($service);
            $paths = $this->serviceJsonPaths($segments);

            $query->where(function (Builder $serviceQuery) use ($paths) {
                foreach ($paths as $path) {
                    $serviceQuery->orWhereRaw($this->jsonBooleanWhere('services', $path));
                }
            });
        }
    }

    protected function applyMeetingPlaceFilters(Builder $query, array $places): void
    {
        foreach ($places as $place) {
            $path = [$place];
            $query->whereRaw($this->jsonBooleanWhere('meeting_places', $path));
        }
    }

    protected function applyFinishFilters(Builder $query, array $finishes): void
    {
        foreach ($finishes as $finish) {
            $segments = $this->splitServiceKey($finish);
            $paths = $this->finishJsonPaths($segments);

            $query->where(function (Builder $finishQuery) use ($paths) {
                foreach ($paths as $path) {
                    $finishQuery->orWhereRaw($this->jsonBooleanWhere('services', $path));
                }
            });
        }
    }

    protected function applyPriceFilter(Builder $query, string $column, array $paths, ?int $from, ?int $to): void
    {
        $expressions = [];

        foreach ($paths as $path) {
            $expressions[] = $this->numericJsonExtract($column, $path);
        }

        $query->where(function (Builder $priceQuery) use ($expressions, $from, $to) {
            foreach ($expressions as $expression) {
                $priceQuery->orWhere(function (Builder $exprQuery) use ($expression, $from, $to) {
                    $exprQuery->whereRaw($expression . ' IS NOT NULL');

                    if ($from !== null) {
                        $exprQuery->whereRaw($expression . ' >= ?', [$from]);
                    }

                    if ($to !== null) {
                        $exprQuery->whereRaw($expression . ' <= ?', [$to]);
                    }
                });
            }
        });
    }

    protected function ageExpression(): string
    {
        return "CAST(NULLIF(REPLACE(REPLACE(REPLACE(age, ' ', ''), 'лет', ''), 'года', ''), '') AS UNSIGNED)";
    }

    protected function jsonBooleanWhere(string $column, array $pathSegments): string
    {
        $path = $this->buildJsonPath($pathSegments);
        $value = "LOWER(JSON_UNQUOTE(JSON_EXTRACT($column, '$path')))";

        return '(' . $value . " IN ('да','true','1','yes'))";
    }

    protected function numericJsonExtract(string $column, array $pathSegments): string
    {
        $path = $this->buildJsonPath($pathSegments);
        $extracted = "JSON_UNQUOTE(JSON_EXTRACT($column, '$path'))";
        $clean = "REPLACE(REPLACE(REPLACE(REPLACE($extracted, ' ', ''), '₽', ''), 'руб', ''), 'р', '')";

        return "CAST(NULLIF($clean, '') AS UNSIGNED)";
    }

    protected function buildJsonPath(array $segments): string
    {
        $parts = array_map(function ($segment) {
            $escaped = str_replace(['\\', '"'], ['\\\\', '\"'], $segment);

            return '"' . $escaped . '"';
        }, $segments);

        return '$.' . implode('.', $parts);
    }

    protected function splitServiceKey(string $value): array
    {
        if (Str::contains($value, '_')) {
            $pieces = explode('_', $value, 2);

            return [
                trim($pieces[0]),
                trim($pieces[1]),
            ];
        }

        return [trim($value)];
    }

    protected function serviceJsonPaths(array $segments): array
    {
        $paths = [];

        if (count($segments) === 1) {
            $paths[] = [$segments[0]];

            return $paths;
        }

        $paths[] = [implode('_', $segments)];
        $paths[] = $segments;

        return $paths;
    }

    protected function finishJsonPaths(array $segments): array
    {
        $paths = [];

        if (count($segments) === 1) {
            $paths[] = ['Окончание_' . $segments[0]];
            $paths[] = ['Окончание', $segments[0]];

            return $paths;
        }

        $paths[] = ['Окончание_' . $segments[1]];
        $paths[] = ['Окончание', $segments[1]];
        $paths[] = $segments;

        return $paths;
    }

    protected function trimMetroPrefix(string $value): string
    {
        $value = trim($value);

        if (Str::startsWith($value, 'м. ')) {
            return trim(Str::after($value, 'м. '));
        }

        if (Str::startsWith($value, 'м.')) {
            return trim(Str::after($value, 'м.'));
        }

        return $value;
    }

    protected function sanitizeArray(array $values): array
    {
        return array_values(array_filter(array_map(fn ($value) => $this->sanitizeString((string) $value), $values), fn ($value) => $value !== ''));
    }

    protected function sanitizeString(string $value): string
    {
        $clean = preg_replace('/[^\p{L}\p{N}\s\-\._]/u', '', $value);

        if ($clean === null) {
            return '';
        }

        return trim($clean);
    }
}

