<?php

namespace App\Http\Controllers\API\V1;

use App\Models\Trip;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Resources\V1\TripDetailsResource;

class GetTripByStationController extends Controller
{
    public function __invoke(Request $request)
    {
        $request->validate([
            'from_id' => 'required|exists:stations,id',
            'to_id'   => 'required|exists:stations,id',
        ]);

        $trip = Trip::query()
            ->where('from_id', $request['from_id'])
            ->where('to_id', $request['to_id'])
            ->with(['pickup', 'destination'])
            ->firstOrFail();

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
