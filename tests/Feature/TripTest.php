<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Trip;
use App\Models\User;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Arr;

class TripTest extends TestCase
{
    public function test_show_trip()
    {
        $trip = Trip::factory()->create();

        $order = Order::factory()->create([
            'trip_id' => $trip->id,
        ]);

        $seats = Arr::random(config('seats.all'), 3);

        foreach ($seats as $seat) {
            OrderItem::factory()->create([
                'order_id' => $order->id,
                'seat_id'  => $seat,
            ]);
        }

        $this->actingAs($this->user);

        $request = $this->get("api/v1/trips/{$trip->uuid}");

        $request->assertOk();

        $request->assertJsonStructure([
            'data' => [
                'id',
                'from',
                'to',
                'distance',
                'seats',
            ],
        ]);

        foreach (json_decode($request->getContent())->data->seats as $seat) {
            in_array($seat->id, $seats)
                ? $this->assertFalse($seat->available)
                : $this->assertTrue($seat->available);
        }
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
    }
}
