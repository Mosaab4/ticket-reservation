<?php

namespace App\Http\Controllers\API\V1;

use App\Models\Trip;
use App\Models\OrderItem;
use App\Resources\V1\TripResource;
use App\Http\Controllers\Controller;
use App\Resources\V1\TripDetailsResource;

class TripController extends Controller
{
    public function index()
    {
        $trips = Trip::query()
            ->getReservation()
            ->with(['pickup', 'destination'])
            ->orderBy('trips.id')
            ->get();

        return $this->respond(TripResource::collection($trips));
    }

    public function show(Trip $trip)
    {
        $reserved_seats = OrderItem::query()
            ->getPreviousReservation($trip->id, ['seat_id'])
            ->pluck('seat_id')
            ->toArray();

        $available_seats = collect(config('seats.all'))
            ->map(function ($seat) use ($reserved_seats) {
                return [
                    'id'        => $seat,
                    'available' => !in_array($seat, $reserved_seats),
                ];
            });

        $trip->seats = $available_seats;

        return $this->respond(new TripDetailsResource($trip));
    }
}
