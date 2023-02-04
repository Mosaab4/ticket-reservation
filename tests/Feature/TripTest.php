<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Trip;
use App\Models\Order;
use App\Models\OrderItem;

class TripTest extends TestCase
{
    public function test_show_trip()
    {
        $trip = create(Trip::class);

        $order = create(Order::class, ['trip_id' => $trip->id]);

        $seats = get_seats(3);

        foreach ($seats as $seat) {
            create(OrderItem::class, [
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
}
