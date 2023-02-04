<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Trip;
use App\Models\Order;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition()
    {
        return [
            'uuid'         => Str::uuid(),
            'date'         => Carbon::today(),
            'email'        => $this->faker->email,
            'user_id'      => User::factory(),
            'trip_id'      => Trip::factory(),
            'trip_details' => json_encode([]),
            'seats_count'  => $this->faker->randomNumber(),
            'seat_price'   => $this->faker->randomNumber(),
            'total'        => $this->faker->randomNumber(),
        ];
    }
}
