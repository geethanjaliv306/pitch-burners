<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Venue;

class VenuesController extends Controller
{
    public function index(){
        $venues = Venue::all();
        return response()->json($venues);
    }
}
