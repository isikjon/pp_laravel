<?php

namespace App\Http\Controllers;

use App\Models\Girl;
use Illuminate\Http\Request;

class FilterOptionsController extends Controller
{
    public function getFilterOptions()
    {
        // Получаем уникальные значения из БД
        $hairColors = Girl::select('hair_color')
            ->distinct()
            ->whereNotNull('hair_color')
            ->where('hair_color', '!=', '')
            ->pluck('hair_color')
            ->filter()
            ->sort()
            ->values();

        $intimateTrims = Girl::select('intimate_trim')
            ->distinct()
            ->whereNotNull('intimate_trim')
            ->where('intimate_trim', '!=', '')
            ->pluck('intimate_trim')
            ->filter()
            ->sort()
            ->values();

        $nationalities = Girl::select('nationality')
            ->distinct()
            ->whereNotNull('nationality')
            ->where('nationality', '!=', '')
            ->pluck('nationality')
            ->filter()
            ->sort()
            ->values();

        $districts = Girl::select('district')
            ->distinct()
            ->whereNotNull('district')
            ->where('district', '!=', '')
            ->pluck('district')
            ->filter()
            ->sort()
            ->values();

        return response()->json([
            'hair_colors' => $hairColors,
            'intimate_trims' => $intimateTrims,
            'nationalities' => $nationalities,
            'districts' => $districts,
        ]);
    }
}

