<?php

namespace App\Modules\Map\Controllers;

use App\Http\Controllers\Controller;

class MapController extends Controller
{
    public function index()
    {
        return view('map::index');
    }
}

