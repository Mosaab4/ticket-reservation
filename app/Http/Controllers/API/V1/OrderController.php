<?php

namespace App\Http\Controllers\API\V1;

use Carbon\Carbon;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\TripSession;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\TripReservation;
use App\Resources\V1\OrderResource;
use App\Http\Controllers\Controller;

class OrderController extends Controller
{
    public function index()
    {
        $reservations = Order::query()
            ->where('user_id', auth()->id())
            ->orderBy('id', 'DESC')
            ->get();

        return $this->respond(OrderResource::collection($reservations));
    }

    public function show(Order $order)
    {
        if ($order->user_id != auth()->id()) {
            return $this->respondNotFound();
        }

        return $this->respond(new OrderResource($order));
    }

    public function store(Request $request)
    {
        $request->validate([
            'session_id' => 'required|exists:trip_sessions,uuid',
        ]);

        $session = TripSession::query()
            ->getUserCurrentSession(auth()->id())
            ->where('uuid', $request['session_id'])
            ->with('trip.pickup', 'trip.destination', 'trip.bus')
            ->select(['*', 'trip_reservations.id as trip_reservation_id', 'trip_sessions.id as session_id'])
            ->first();

        if (!$session) {
            return $this->respondForbidden('Your session has expired, Please Create a new session');
        }

        // Apply discount
        $discount = 0;
        if (count($session->seats) > 5) {
            $discount = $this->applyDiscount($session);
        }

        // Create Order
        $order = $this->createOrder($session, $discount);

        $remaining_seats = $session->remaining_seats - $order->seats_count;

        // unlock the trip
        TripReservation::find($session->trip_reservation_id)->unlock($remaining_seats);

        // delete user's session
        TripSession::find($session->session_id)->delete();

        return $this->respond(new OrderResource($order), 'Order Created');
    }

    private function applyDiscount(TripSession $session)
    {
        $seat_price = $session->trip?->price;
        $total = $seat_price * count($session->seats);
        $discount_percentage = config('seats.discount_percent');

        return $total * $discount_percentage / 100;
    }

    private function createOrder($session, $discount)
    {
        $user = auth()->user();

        $count = count($session->seats);

        $order = Order::create([
            'uuid'         => Str::uuid(),
            'date'         => Carbon::today(),
            'seats_count'  => $count,
            'seat_price'   => $session->trip?->price,
            'total'        => $session->trip?->price * $count,
            'discount'     => $discount,
            'user_id'      => $user->id,
            'email'        => $user->email,
            'trip_id'      => $session->trip_id,
            'trip_details' => [
                'from'  => $session->trip?->pickup?->name,
                'to'    => $session->trip?->destination?->name,
                'bus'   => $session->trip?->bus?->name,
                'seats' => $session->seats,
            ],
        ]);

        $seats = [];

        foreach ($session->seats as $seat) {
            $seats[] = [
                'seat_id'  => $seat,
                'order_id' => $order->id,
            ];
        }

        OrderItem::insert($seats);

        return $order;
    }

    public function destroy(Order $order)
    {
        if ($order->user_id != auth()->id()) {
            return $this->respondNotFound();
        }

        $order->delete();
        return $this->respond([], 'Deleted Successfully');
    }
}
