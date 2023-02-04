<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrderItem extends Model
{
    use SoftDeletes;
    use HasFactory;

    protected $guarded = [];

    public function reservation(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function scopeGetPreviousReservation(Builder $query, $trip_id, $select = ['*'])
    {
        $query->select($select)
            ->leftJoin('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.trip_id', $trip_id)
            ->where('orders.date', Carbon::today());
    }
}
