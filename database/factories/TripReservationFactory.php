<?php

namespace Database\Factories;

use Illuminate\Support\Carbon;
use App\Models\TripReservation;
use Illuminate\Database\Eloquent\Factories\Factory;

class TripReservationFactory extends Factory
{
    protected $model = TripReservation::class;

    public function definition()
    {
        return [
            'date'            => Carbon::now(),
            'remaining_seats' => $this->faker->randomNumber(),
            'trip_id'         => $this->faker->randomNumber(),
        ];
    }

    public function locked()
    {
        return $this->state(fn(array $attributes) => [
            'locked'    => 1,
            'locked_at' => Carbon::now(),
        ]);
    }

    public function expired()
    {
        return $this->state(fn(array $attributes) => [
            'locked_at' => Carbon::now()->subMinutes(10),
        ]);
    }

    public function available()
    {
        return $this->state(fn(array $attributes) => [
            'remaining_seats' => 20,
        ]);
    }

    public function comleted()
    {
        return $this->state(fn(array $attributes) => [
            'remaining_seats' => 0,
        ]);
    }
}
