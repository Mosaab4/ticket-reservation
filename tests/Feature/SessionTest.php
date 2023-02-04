<?php

namespace Tests\Feature;

use Carbon\Carbon;
use Tests\TestCase;
use App\Models\Trip;
use App\Models\User;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\TripSession;
use App\Models\TripReservation;
use Illuminate\Support\Facades\Lang;

class SessionTest extends TestCase
{
    public function test_user_can_not_reserve_when_the_session_expired()
    {
        $trip = create(Trip::class);

        $trip_reservation = create(
            TripReservation::class,
            [
                'lock_user_id' => $this->user->id,
                'trip_id'      => $trip->id,
            ],
            ['locked', 'available', 'expired'],
        );

        $session = create(TripSession::class, [
            'user_id' => $this->user->id,
            'trip_id' => $trip->id,
        ]);

        $this->travel(3)->minute();

        $this->actingAs($this->user);

        $request = $this->post('api/v1/sessions', [
            'from_id' => $trip->from_id,
            'to_id'   => $trip->to_id,
            'seats'   => get_seats(3),
        ]);

        $request->assertForbidden();

        $request->assertJson([
            'status' => false,
            'data'   => [
                'error' => [
                    'message' => Lang::get('api.session_expired'),
                ],
            ],
        ]);

        $this->assertSoftDeleted('trip_sessions', [
            'id'      => $session->id,
            'user_id' => $this->user->id,
            'trip_id' => $trip->id,
        ]);

        $this->assertDatabaseHas('trip_reservations', [
            'id'           => $trip_reservation->id,
            'locked'       => 0,
            'lock_user_id' => null,
            'locked_at'    => null,
        ]);
    }

    public function test_user_can_not_create_new_session_if_there_is_ongoing_session()
    {
        $trip = create(Trip::class);

        create(
            TripReservation::class,
            [
                'trip_id'      => $trip->id,
                'lock_user_id' => $this->user->id,
            ],
            ['available', 'locked']
        );

        $this->actingAs(create(User::class));

        $request = $this->post('api/v1/sessions', [
            'from_id' => $trip->from_id,
            'to_id'   => $trip->to_id,
            'seats'   => get_seats(3),
        ]);

        $request->assertForbidden();
        $request->assertJson([
            'status' => false,
            'data'   => [
                'error' => [
                    'message' => Lang::get('api.can_not_create_session'),
                ],
            ],
        ]);
    }

    public function test_user_can_create_new_session_when_old_session_expire()
    {
        $trip = create(Trip::class);

        $seats = get_seats(3);

        create(
            TripReservation::class,
            [
                'trip_id'      => $trip->id,
                'lock_user_id' => $this->user->id,
            ],
            ['available', 'locked', 'expired'],
        );

        $second_user = create(User::class);

        $this->actingAs($second_user);

        $this->post('api/v1/sessions', [
            'from_id' => $trip->from_id,
            'to_id'   => $trip->to_id,
            'seats'   => $seats,
        ])->assertOk();

        $this->assertDatabaseHas('trip_reservations', [
            'locked'       => 1,
            'lock_user_id' => $second_user->id,
        ]);
    }

    public function test_user_can_not_reserve_completed_trip()
    {
        $trip = create(Trip::class);

        create(TripReservation::class, ['trip_id' => $trip->id], ['completed']);

        $this->actingAs($this->user);

        $request = $this->post('api/v1/sessions', [
            'from_id' => $trip->from_id,
            'to_id'   => $trip->to_id,
            'seats'   => get_seats(3),
        ]);

        $request->assertForbidden();

        $request->assertJson([
            'status' => false,
            'data'   => [
                'error' => [
                    'message' => Lang::get('api.no_remaining_seats'),
                ],
            ],
        ]);
    }

    public function test_seats_updated_when_the_user_change_it()
    {
        $trip = create(Trip::class);

        create(TripSession::class, [
            'user_id' => $this->user->id,
            'trip_id' => $trip->id,
            'seats'   => [
                'A1', 'A2',
            ],
        ]);

        $this->actingAs($this->user);

        $new_seats = get_seats(4);

        $request = $this->post('api/v1/sessions', [
            'from_id' => $trip->from_id,
            'to_id'   => $trip->to_id,
            'seats'   => $new_seats,
        ]);

        $request->assertOk();

        $this->assertDatabaseHas('trip_sessions', [
            'user_id' => $this->user->id,
            'trip_id' => $trip->id,
            'seats'   => json_encode($new_seats),
        ]);
    }

    public function test_user_can_not_reserve_already_reserved_seats()
    {
        $seats = get_seats(3);

        $trip = create(Trip::class);

        create(TripSession::class, [
            'user_id' => $this->user->id,
            'trip_id' => $trip->id,
            'seats'   => $seats,
        ]);

        $reservation = create(Order::class, [
            'trip_id' => $trip->id,
        ]);

        foreach ($seats as $seat) {
            create(OrderItem::class, [
                'seat_id'  => $seat,
                'order_id' => $reservation->id,
            ]);
        }

        $this->actingAs($this->user);

        for ($i = 0; $i < 3; $i++) {
            $request = $this->post('api/v1/sessions', [
                'from_id' => $trip->from_id,
                'to_id'   => $trip->to_id,
                'seats'   => $seats,
            ]);

            $request->assertForbidden();

            $request->assertJson([
                'status' => false,
                'data'   => [
                    'error' => [
                        'message' => Lang::get(
                            'api.seat_is_not_available',
                            ['seat' => $seats[0]]
                        ),
                    ],
                ],
            ]);

            array_shift($seats);
        }
    }

    public function test_user_can_creat_a_new_session()
    {
        $trip = create(Trip::class);

        $this->actingAs($this->user);

        $seats = get_seats(3);

        $request = $this->post('api/v1/sessions', [
            'from_id' => $trip->from_id,
            'to_id'   => $trip->to_id,
            'seats'   => $seats,
        ]);

        $request->assertOk();

        $request->assertJsonStructure([
            'status_code',
            'status',
            'data' => [
                'session_id',
            ],
        ]);

        $this->assertDatabaseHas('trip_reservations', [
            'trip_id'         => $trip->id,
            'remaining_seats' => config('seats.seats_count'),
            'locked'          => 1,
            'lock_user_id'    => $this->user->id,
        ]);

        $this->assertDatabaseHas('trip_sessions', [
            'user_id' => $this->user->id,
            'trip_id' => $trip->id,
            'date'    => Carbon::today(),
            'seats'   => json_encode($seats),
        ]);
    }
}
