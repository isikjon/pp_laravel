<?php

namespace App\Modules\Salons\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Salon;
use Illuminate\Http\Request;

class SalonsController extends Controller
{
    public function index(Request $request)
    {
        $selectedCity = $request->input('city', $request->cookie('selectedCity', 'moscow'));
        
        if ($request->has('city')) {
            cookie()->queue('selectedCity', $selectedCity, 525600);
        }
        
        $cityName = $selectedCity === 'spb' ? 'Санкт-Петербург' : 'Москва';
        
        $salons = Salon::where('city', $cityName)
            ->orderBy('created_at', 'desc')
            ->paginate(12);
        
        return view('salons::index', compact('salons', 'cityName'));
    }
    
    public function show($id)
    {
        $salon = Salon::where('salon_id', $id)->firstOrFail();
        
        $images = $salon->images ?? [];
        $phones = $salon->phones ?? [];
        $tariffs = $salon->tariffs ?? [];
        
        $salonData = [
            'id' => $salon->salon_id,
            'name' => $salon->name,
            'title' => $salon->title,
            'phones' => $phones,
            'schedule' => $salon->schedule ?? 'Круглосуточно',
            'city' => $salon->city ?? 'Москва',
            'metro' => $salon->metro,
            'district' => $salon->district,
            'coordinates' => $salon->coordinates,
            'map_link' => $salon->map_link,
            'description' => $salon->description,
            'images' => array_map(function($img) {
                return is_array($img) ? ($img['full'] ?? $img['preview'] ?? '') : $img;
            }, $images),
            'tariffs' => $tariffs,
            'reviews' => $salon->reviews ?? [],
        ];
        
        return view('salons::show', compact('salonData'));
    }
}
