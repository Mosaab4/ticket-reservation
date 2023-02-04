<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TripSession extends Model
{
    use SoftDeletes;
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'date'  => 'date',
        'seats' => 'json',
    ];

    public function trip(): BelongsTo
    {
        return $this->belongsTo(Trip::class, 'trip_id');
    }

    public function scopeActiveSessionForTheTrip(Builder $query, $trip_id, $user_id = null)
    {
        $query->where('trip_id', $trip_id)
            ->where('user_id', $user_id ?? auth()->id())
            ->whereDate('date', Carbon::today());
    }

    public function scopeGetUserCurrentSession(Builder $query, $user_id = null)
    {
        $user_id = !empty($user_id) ? $user_id : auth()->id();

        $query->where('user_id', $user_id)
            ->where('lock_user_id', $user_id)
            ->leftJoin(
                'trip_reservations',
                'trip_sessions.trip_id',
                '=',
                'trip_reservations.trip_id'
            )
            ->whereDate('trip_reservations.date', Carbon::today())
            ->whereTime('trip_reservations.locked_at', '>', Carbon::now()->subMinutes(3));
    }
}
