<?php

namespace App\Http\Controllers\API\V1;

use App\Models\Station;
use App\Http\Controllers\Controller;
use App\Resources\V1\StationResource;

class StationController extends Controller
{
    public function index()
    {
        $stations = Station::orderBy('id', 'desc')->get();

        return $this->respond(StationResource::collection($stations));
    }
}
