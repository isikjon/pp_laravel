<?php

namespace App\Modules\IntimMap\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Girl;
use App\Models\Salon;
use App\Models\StripClub;
use Illuminate\Http\Request;

class IntimMapController extends Controller
{
    public function index(Request $request)
    {
        $selectedCity = $request->input('city', $request->cookie('selectedCity', 'moscow'));
        
        if ($request->has('city')) {
            cookie()->queue('selectedCity', $selectedCity, 525600);
        }
        
        $cityName = $selectedCity === 'spb' ? 'Санкт-Петербург' : 'Москва';
        
        return view('intimmap::index', compact('cityName', 'selectedCity'));
    }
    
    public function getMapData(Request $request)
    {
        $selectedCity = $request->input('city', $request->cookie('selectedCity', 'moscow'));
        $typesString = $request->input('types', '');
        $types = !empty($typesString) ? explode(',', $typesString) : ['1', '2', '3'];
        
        $cityName = $selectedCity === 'spb' ? 'Санкт-Петербург' : 'Москва';
        
        $data = [
            'girls' => [],
            'salons' => [],
            'clubs' => []
        ];
        
        if (in_array('1', $types)) {
            $girls = Girl::where('city', $cityName)
                ->whereNotNull('coordinates')
                ->where('coordinates', '!=', '')
                ->where('coordinates', '!=', 'null')
                ->select('id', 'anketa_id', 'name', 'coordinates')
                ->get();
            
            $data['girls'] = $girls->map(function($girl) {
                return [
                    'id' => $girl->anketa_id,
                    'name' => $girl->name,
                    'coordinates' => $this->parseCoordinates($girl->coordinates),
                    'type' => 'girl'
                ];
            })->filter(function($item) {
                return !empty($item['coordinates']);
            })->values();
        }
        
        if (in_array('2', $types)) {
            $salons = Salon::where('city', $cityName)
                ->whereNotNull('coordinates')
                ->where('coordinates', '!=', '')
                ->where('coordinates', '!=', 'null')
                ->select('id', 'salon_id', 'name', 'coordinates')
                ->get();
            
            $data['salons'] = $salons->map(function($salon) {
                return [
                    'id' => $salon->salon_id,
                    'name' => $salon->name,
                    'coordinates' => $this->parseCoordinates($salon->coordinates),
                    'type' => 'salon'
                ];
            })->filter(function($item) {
                return !empty($item['coordinates']);
            })->values();
        }
        
        if (in_array('3', $types)) {
            $clubs = StripClub::where('city', $cityName)
                ->whereNotNull('coordinates')
                ->where('coordinates', '!=', '')
                ->where('coordinates', '!=', 'null')
                ->select('id', 'club_id', 'name', 'coordinates')
                ->get();
            
            $data['clubs'] = $clubs->map(function($club) {
                return [
                    'id' => $club->club_id,
                    'name' => $club->name,
                    'coordinates' => $this->parseCoordinates($club->coordinates),
                    'type' => 'club'
                ];
            })->filter(function($item) {
                return !empty($item['coordinates']);
            })->values();
        }
        
        return response()->json($data);
    }
    
    private function parseCoordinates($coords)
    {
        if (is_array($coords) && count($coords) === 2) {
            return [(float)$coords[0], (float)$coords[1]];
        }
        
        if (is_string($coords)) {
            try {
                $parsed = json_decode($coords, true);
                if (is_array($parsed) && count($parsed) === 2) {
                    return [(float)$parsed[0], (float)$parsed[1]];
                }
            } catch (\Exception $e) {
            }
            
            if (strpos($coords, ',') !== false) {
                $parts = explode(',', $coords);
                if (count($parts) === 2) {
                    return [(float)trim($parts[0]), (float)trim($parts[1])];
                }
            }
        }
        
        return null;
    }
}

