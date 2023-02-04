<?php

namespace Database\Seeders;

use App\Models\Bus;
use App\Enums\BusTypeEnum;
use Illuminate\Database\Seeder;

class BusSeeder extends Seeder
{
    public function run()
    {
        Bus::create([
            'name' => 'Bus 1',
            'type' => BusTypeEnum::SHORT
        ]);

        Bus::create([
            'name' => 'Bus 2',
            'type' => BusTypeEnum::LONG
        ]);
    }
}
