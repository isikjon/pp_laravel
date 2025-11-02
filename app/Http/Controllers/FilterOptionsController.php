<?php

namespace App\Http\Controllers;

use App\Models\Girl;
use Illuminate\Http\Request;

class FilterOptionsController extends Controller
{
    public function getFilterOptions(Request $request)
    {
        $selectedCity = $request->input('city', $request->cookie('selectedCity', 'moscow'));
        $cityName = $selectedCity === 'spb' ? 'Санкт-Петербург' : 'Москва';
        
        $hairColors = Girl::select('hair_color')
            ->distinct()
            ->where('city', $cityName)
            ->whereNotNull('hair_color')
            ->where('hair_color', '!=', '')
            ->pluck('hair_color')
            ->filter()
            ->sort()
            ->values();

        $intimateTrims = Girl::select('intimate_trim')
            ->distinct()
            ->where('city', $cityName)
            ->whereNotNull('intimate_trim')
            ->where('intimate_trim', '!=', '')
            ->pluck('intimate_trim')
            ->filter()
            ->sort()
            ->values();

        $nationalities = Girl::select('nationality')
            ->distinct()
            ->where('city', $cityName)
            ->whereNotNull('nationality')
            ->where('nationality', '!=', '')
            ->pluck('nationality')
            ->filter()
            ->sort()
            ->values();

        $districts = Girl::select('district')
            ->distinct()
            ->where('city', $cityName)
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

