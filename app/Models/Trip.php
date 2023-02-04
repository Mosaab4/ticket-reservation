<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Trip extends Model
{
    use HasFactory;

    protected $guarded = [];

    public static function createDefaultReservation($trip_id, $user_id = null)
    {
        return TripReservation::create([
            'trip_id'         => $trip_id,
            'date'            => Carbon::now(),
            'remaining_seats' => config('seats.seats_count'),
            'locked'          => 1,
            'lock_user_id'    => $user_id ?? auth()->id(),
            'locked_at'       => Carbon::now(),
        ]);
    }

    public function getRouteKeyName()
    {
        return 'uuid';
    }

    public function bus(): BelongsTo
    {
        return $this->belongsTo(Bus::class, 'bus_id');
    }

    public function pickup(): BelongsTo
    {
        return $this->belongsTo(Station::class, 'from_id');
    }

    public function destination(): BelongsTo
    {
        return $this->belongsTo(Station::class, 'to_id');
    }

    public function scopeGetReservation(Builder $query)
    {
        $query->leftJoin(
            'trip_reservations',
            'trips.id',
            '=',
            'trip_reservations.trip_id'
        )
            ->where(function ($query) {
                $query->whereDate('trip_reservations.date', Carbon::today()->toDateString())
                    ->orWhere('trip_reservations.date', '=', null);
            });
    }
}
