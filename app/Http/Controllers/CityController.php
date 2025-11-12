<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CityController extends Controller
{
    public function setCity(Request $request)
    {
        $city = $request->input('city', 'moscow');
        
        // Validate city
        if (!in_array($city, ['moscow', 'spb'])) {
            $city = 'moscow';
        }
        
        // Set cookie for 1 year
        cookie()->queue('selectedCity', $city, 525600);
        
        return response()->json([
            'success' => true,
            'city' => $city
        ]);
    }
}

