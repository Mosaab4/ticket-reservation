<?php

namespace App\Models;

use App\Enums\BusTypeEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Bus extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'type' => BusTypeEnum::class,
    ];
}
