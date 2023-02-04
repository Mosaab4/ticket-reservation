<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Trip;
use App\Models\Order;

class FrequentTripsTest extends TestCase
{
    public function test_api_returns_correct_results()
    {
        $trip = create(Trip::class);
        $trip2 = create(Trip::class);

        create(Order::class, [
            'user_id'      => $this->user->id,
            'trip_id'      => $trip,
            'trip_details' => [
                'from' => $trip->pickup->name,
                'to'   => $trip->destination->name,
            ],
        ], [], 5);

        create(Order::class, [
            'user_id'      => $this->user->id,
            'trip_id'      => $trip2,
            'trip_details' => [
                'from' => $trip2->pickup->name,
                'to'   => $trip2->destination->name,
            ],
        ], [], 3);

        $request = $this->get('api/v1/frequent-trips');

        $request->assertOk();

        $request->assertJson([
            'data' => [
                [
                    'user_id'      => $this->user->id,
                    'frequentBook' => strtolower($trip->pickup->name) . '-' . strtolower($trip->destination->name),
                    'email'        => $this->user->email,
                    'count'        => 5,
                ],
            ],
        ]);

        $request->assertJsonMissing([
            'data' => [
                [
                    'user_id'      => $this->user->id,
                    'frequentBook' => strtolower($trip2->pickup->name) . '-' . strtolower($trip2->destination->name),
                    'email'        => $this->user->email,
                    'count'        => 3,
                ],
            ],
        ]);
    }
}
