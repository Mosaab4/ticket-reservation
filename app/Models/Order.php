<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use SoftDeletes;
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'seats'        => 'json',
        'trip_details' => 'json',
        'date'         => 'date:Y-m-d',
        'discount'     => 'float',
    ];

    public function getRouteKeyName()
    {
        return 'uuid';
    }

    public function totalAfterDiscount(): Attribute
    {
        return Attribute::make(
            get: fn($value) => $this->total - $this->discount
        );
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class,'user_id');
    }

    public function trip(): BelongsTo
    {
        return $this->belongsTo(Trip::class,'trip_id');
    }
}
