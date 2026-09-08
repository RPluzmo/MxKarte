<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Track;

class MapController extends Controller
{
    public function index()
    {
        $tracks = Track::select('id', 'name', 'lat', 'lng', 'description')->get();
    }
}
