<?php

namespace Tests\Feature;

use Carbon\Carbon;
use Tests\TestCase;
use App\Models\Trip;
use App\Models\User;
use App\Models\Order;
use App\Models\TripSession;
use Illuminate\Support\Str;
use App\Models\TripReservation;

class OrderTest extends TestCase
{
    public function test_user_can_reserve_a_trip()
    {
        $trip = create(Trip::class);

        $seats = get_seats(3);

        $trip_reservation = create(
            TripReservation::class,
            [
                'lock_user_id' => $this->user->id,
                'trip_id'      => $trip->id,
            ],
            ['locked', 'available'],
        );

        $session = create(TripSession::class, [
            'user_id' => $this->user->id,
            'trip_id' => $trip->id,
            'seats'   => $seats,
        ]);

        $this->actingAs($this->user);

        $request = $this->post('api/v1/orders', [
            'session_id' => $session->uuid,
        ]);

        $request->assertOk();

        $this->assertDatabaseHas('orders', [
            'date'         => Carbon::today(),
            'user_id'      => $this->user->id,
            'trip_id'      => $trip->id,
            'email'        => $this->user->email,
            'seats_count'  => count($seats),
            'seat_price'   => $trip->price,
            'total'        => count($seats) * $trip->price,
            'discount'     => 0,
            'trip_details' => json_encode([
                'from'  => $session->trip?->pickup?->name,
                'to'    => $session->trip?->destination?->name,
                'bus'   => $session->trip?->bus?->name,
                'seats' => $seats,
            ]),
        ]);

        $order = Order::latest()->first();

        foreach ($seats as $seat) {
            $this->assertDatabaseHas('order_items', [
                'seat_id'  => $seat,
                'order_id' => $order->id,
            ]);
        }

        $this->assertDatabaseHas('trip_reservations', [
            'id'              => $trip_reservation->id,
            'locked'          => 0,
            'locked_at'       => null,
            'lock_user_id'    => null,
            'remaining_seats' => 17,
        ]);

        $this->assertSoftDeleted('trip_sessions', [
            'id'      => $session->id,
            'user_id' => $this->user->id,
            'trip_id' => $trip->id,
        ]);
    }

    public function test_user_get_discount_after_booking_more_than_five_seats()
    {
        $trip = create(Trip::class);

        $seats = get_seats(6);

        create(
            TripReservation::class,
            [
                'lock_user_id' => $this->user->id,
                'trip_id'      => $trip->id,
            ],
            ['locked', 'available'],
        );

        $session = create(TripSession::class, [
            'user_id' => $this->user->id,
            'trip_id' => $trip->id,
            'seats'   => $seats,
        ]);

        $this->actingAs($this->user);

        $request = $this->post('api/v1/orders', [
            'session_id' => $session->uuid,
        ]);

        $request->assertOk();

        $discount = (count($seats) * $trip->price) * config('seats.discount_percent') / 100;

        $this->assertDatabaseHas('orders', [
            'seats_count' => count($seats),
            'seat_price'  => $trip->price,
            'total'       => count($seats) * $trip->price,
            'discount'    => $discount,
        ]);
    }

    public function test_user_can_not_reserve_if_there_is_no_session()
    {
        $this->actingAs($this->user);

        $request = $this->post('api/v1/orders', [
            'session_id' => Str::uuid(),
        ]);

        $request->assertUnprocessable();

        $request->assertJson([
            'status' => false,
            'data'   => [
                'error' => [
                    'message' => 'The selected session id is invalid.',
                ],
            ],
        ]);
    }

    public function test_user_can_view_order()
    {
        $order = create(Order::class, [
            'user_id' => $this->user->id,
            'email'   => $this->user->email,
        ]);

        $this->actingAs($this->user);

        $request = $this->get("api/v1/orders/{$order->uuid}");

        $request->assertOk();

        $request->assertJsonStructure([
            'data' => [
                'id',
                'date',
                'seats_count',
                'seat_price',
                'total',
                'discount',
                'email',
                'details',
            ],
        ]);

        $request->assertJson([
            'data' => [
                'id'    => $order->uuid,
                'email' => $this->user->email,
            ],
        ]);
    }

    public function test_user_can_not_view_another_user_order()
    {
        $order = create(Order::class, ['user_id' => $this->user->id]);

        $this->actingAs(create(User::class));

        $request = $this->get("api/v1/orders/{$order->uuid}");

        $request->assertNotFound();
    }

    public function test_user_can_delete_order()
    {
        $order = create(Order::class, ['user_id' => $this->user->id]);

        $this->actingAs($this->user);

        $request = $this->delete("api/v1/orders/{$order->uuid}");

        $request->assertOk();

        $this->assertSoftDeleted('orders', [
            'id'    => $order->id,
            'uuid'  => $order->uuid,
            'email' => $order->email,
        ]);
    }

    public function test_user_can_not_delete_another_user_order()
    {
        $order = create(Order::class, ['user_id' => $this->user->id]);

        $this->actingAs(create(User::class));

        $request = $this->delete("api/v1/orders/{$order->uuid}");

        $request->assertNotFound();
    }
}
