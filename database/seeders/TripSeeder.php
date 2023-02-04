<?php

namespace Database\Seeders;

use App\Models\Bus;
use App\Models\Trip;
use App\Models\Station;
use App\Enums\BusTypeEnum;
use Illuminate\Database\Seeder;

class TripSeeder extends Seeder
{
    public function run()
    {
        $short_bus = Bus::where('type', BusTypeEnum::SHORT)->first();
        $long_bus = Bus::where('type', BusTypeEnum::LONG)->first();

        $cairo = Station::where('name', 'Cairo')->first();
        $alex = Station::where('name', 'Alex')->first();
        $aswan = Station::where('name', 'Aswan')->first();

        Trip::create([
            'uuid'     => 1,
            'from_id'  => $cairo->id,
            'to_id'    => $alex->id,
            'distance' => 90,
            'bus_id'   => $short_bus->id,
            'price'    => 200
        ]);

        Trip::create([
            'uuid'     => 2,
            'from_id'  => $cairo->id,
            'to_id'    => $aswan->id,
            'distance' => 150,
            'bus_id'   => $long_bus->id,
            'price'    => 700
        ]);
    }
}
