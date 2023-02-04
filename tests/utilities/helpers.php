<?php

use Illuminate\Support\Arr;

function get_seats($count = 1)
{
    return Arr::random(config('seats.all'), $count);
}

function create($model, $attributes = [], $states = [], $times = 1)
{
    $factory = $model::factory();

    if ($times > 1) {
        $factory = $factory->count($times);
    }

    if (!empty($states)) {
        foreach ($states as $state) {
            $factory = $factory->{$state}();
        }
    }

    return $factory->create($attributes);
}
