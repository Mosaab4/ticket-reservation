<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Arr;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderItemFactory extends Factory
{
    protected $model = OrderItem::class;

    public function definition()
    {
        return [
            'seat_id'  => Arr::random(config('seats.all')),
            'order_id' => Order::factory(),
        ];
    }
}
