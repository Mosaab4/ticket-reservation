<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TripReservation extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'date'      => 'date',
        'locked_at' => 'date',
    ];

    public function lock()
    {
        $this->update([
            'locked'       => 1,
            'lock_user_id' => auth()->id(),
            'locked_at'    => Carbon::now(),
        ]);
    }

    public function unlock($seats = null)
    {
        $this->update([
            'locked'          => 0,
            'lock_user_id'    => null,
            'locked_at'       => null,
            'remaining_seats' => !empty($seats) ? $seats : $this->remaining_seats,
        ]);
    }
}
