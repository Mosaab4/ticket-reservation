<?php

namespace Database\Seeders;

use App\Models\Station;
use Illuminate\Database\Seeder;

class StationSeeder extends Seeder
{
    public function run()
    {
        Station::create(['name'  =>  'Alex']);
        Station::create(['name'  =>  'Cairo']);
        Station::create(['name'  =>  'Aswan']);
    }
}
