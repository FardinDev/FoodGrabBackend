<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Location;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function index(Request $request){

        $locations = Location::where('status', 1)->orderBy('name', 'ASC')->get(['id', 'name']);


        return $this->apiSuccessResponse('Location data', $locations);
    }
}
