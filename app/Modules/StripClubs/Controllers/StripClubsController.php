<?php

namespace App\Modules\StripClubs\Controllers;

use App\Http\Controllers\Controller;
use App\Models\StripClub;
use Illuminate\Http\Request;

class StripClubsController extends Controller
{
    public function index(Request $request)
    {
        $selectedCity = $request->input('city', $request->cookie('selectedCity', 'moscow'));
        
        if ($request->has('city')) {
            cookie()->queue('selectedCity', $selectedCity, 525600);
        }
        
        $cityName = $selectedCity === 'spb' ? 'Санкт-Петербург' : 'Москва';
        
        $clubs = StripClub::where('city', $cityName)
            ->orderBy('created_at', 'desc')
            ->paginate(12);
        
        return view('stripclubs::index', compact('clubs', 'cityName'));
    }
    
    public function show($id)
    {
        $club = StripClub::where('club_id', $id)->firstOrFail();
        
        $images = $club->images ?? [];
        $phones = $club->phones ?? [];
        $tariffs = $club->tariffs ?? [];
        
        $clubData = [
            'id' => $club->club_id,
            'name' => $club->name,
            'title' => $club->title,
            'phones' => $phones,
            'schedule' => $club->schedule ?? 'Круглосуточно',
            'city' => $club->city ?? 'Москва',
            'metro' => $club->metro,
            'district' => $club->district,
            'coordinates' => $club->coordinates,
            'map_link' => $club->map_link,
            'description' => $club->description,
            'images' => array_map(function($img) {
                return is_array($img) ? ($img['full'] ?? $img['preview'] ?? '') : $img;
            }, $images),
            'tariffs' => $tariffs,
            'reviews' => $club->reviews ?? [],
        ];
        
        return view('stripclubs::show', compact('clubData'));
    }
}
