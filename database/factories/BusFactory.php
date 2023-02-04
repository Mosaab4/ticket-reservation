<?php

namespace Database\Factories;

use App\Models\Bus;
use App\Enums\BusTypeEnum;
use Illuminate\Support\Arr;
use Illuminate\Database\Eloquent\Factories\Factory;

class BusFactory extends Factory
{
    protected $model = Bus::class;

    public function definition()
    {
        return [
            'name' => $this->faker->name(),
            'type' => Arr::random([BusTypeEnum::LONG->value, BusTypeEnum::SHORT->value]),
        ];
    }

    public function long()
    {
        return $this->state(fn(array $attributes) => [
            'type' => BusTypeEnum::LONG,
        ]);
    }

    public function short()
    {
        return $this->state(fn(array $attributes) => [
            'type' => BusTypeEnum::SHORT
        ]);
    }
}
