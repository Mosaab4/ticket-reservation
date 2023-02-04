<?php

namespace App\Http\Controllers\API\V1;

use Carbon\Carbon;
use App\Models\Trip;
use App\Models\OrderItem;
use Illuminate\Support\Str;
use App\Models\TripSession;
use App\Models\TripReservation;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Lang;
use App\Resources\V1\SessionsResource;
use App\Http\Requests\API\V1\StoreSessionRequest;

class SessionController extends Controller
{
    public function index()
    {
        $session = TripSession::query()
            ->getUserCurrentSession(auth()->id())
            ->with('trip.pickup', 'trip.destination')
            ->select(['*', 'trip_reservations.id as trip_reservation_id', 'trip_sessions.id as session_id'])
            ->get();

        return $this->respond(SessionsResource::collection($session));
    }

    public function store(StoreSessionRequest $request)
    {
        $trip = Trip::query()
            ->getReservation()
            ->select([
                'trips.id as trip_id', 'trip_reservations.id as trip_reservation_id',
                'remaining_seats', 'locked', 'lock_user_id', 'locked_at',
            ])
            ->where('from_id', $request['from_id'])
            ->where('to_id', $request['to_id'])
            ->first();

        if (!$trip) {
            return $this->respondNotFound(Lang::get('api.no_trip'));
        }

        if ($trip->remaining_seats === 0) {
            return $this->respondForbidden(Lang::get('api.no_remaining_seats'));
        }

        if ($this->tripIsLocked($trip)) {
            return $this->respondForbidden(Lang::get('api.can_not_create_session'));
        }

        if ($this->userSessionExpired($trip)) {
            // Delete the active session
            TripSession::activeSessionForTheTrip($trip->trip_id)->delete();

            // unlock the trip
            TripReservation::find($trip->trip_reservation_id)->unlock();

            return $this->respondForbidden(Lang::get('api.session_expired'));
        }

        $previous_reservations = OrderItem::query()
            ->getPreviousReservation($trip->trip_id, ['seat_id'])
            ->pluck('seat_id')
            ->toArray();

        foreach ($request['seats'] as $seat) {
            if (in_array($seat, $previous_reservations)) {
                return $this->respondForbidden(
                    Lang::get('api.seat_is_not_available', ['seat' => $seat])
                );
            }
        }

        $trip_reservation_id = $trip->trip_reservation_id;
        if ($trip->trip_reservation_id == null) {
            $trip_reservation = Trip::createDefaultReservation($trip->trip_id);
            $trip_reservation_id = $trip_reservation->id;
        }

        $session = TripSession::updateOrCreate([
            'uuid'    => Str::uuid(),
            'user_id' => auth()->id(),
            'trip_id' => $trip->trip_id,
        ], [
            'date'  => Carbon::today(),
            'seats' => $request['seats'],
        ]);

        TripReservation::find($trip_reservation_id)->lock();

        return $this->respond([
            'session_id' => $session->uuid,
        ], 'Session Created');
    }

    private function tripIsLocked($trip)
    {
        return $trip->remaining_seats != null &&
            $trip->lock_user_id != auth()->id() &&
            $trip->locked == 1 &&
            Carbon::parse($trip->locked_at)->diffInMinutes() < 2;
    }

    private function userSessionExpired($trip)
    {
        return $trip->lock_user_id == auth()->id() &&
            Carbon::parse($trip->locked_at)->diffInMinutes(Carbon::now()) > 2;
    }
}
