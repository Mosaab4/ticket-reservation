<?php

namespace Database\Factories;

use App\Models\Bus;
use App\Models\Trip;
use App\Models\Station;
use Illuminate\Database\Eloquent\Factories\Factory;

class TripFactory extends Factory
{
    protected $model = Trip::class;

    public function definition()
    {
        return [
            'uuid'     => $this->faker->uuid(),
            'from_id'  => Station::factory(),
            'to_id'    => Station::factory(),
            'distance' => random_int(50, 200),
            'bus_id'   => Bus::factory(),
            'price'    => random_int(200, 700),
        ];
    }

    public function long()
    {
        return $this->state(fn (array $attributes) => [
            'bus_id'   => Bus::factory()->long(),
            'distance' => random_int(100, 200),
            'price'    => random_int(300, 700),
        ]);
    }

    public function short()
    {
        return $this->state(fn (array $attributes) => [
            'bus_id'   => Bus::factory()->short(),
            'distance' => random_int(50, 99),
            'price'    => random_int(200, 300),
        ]);
    }
}
