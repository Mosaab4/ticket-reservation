<?php

namespace Database\Factories;

use Str;
use App\Models\TripSession;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class TripSessionFactory extends Factory
{
    protected $model = TripSession::class;

    public function definition()
    {
        return [
            'seats' => Arr::random(config('seats.all'), random_int(1, 10)),
            'date'  => Carbon::today(),
            'uuid'  => Str::uuid(),
        ];
    }
}
