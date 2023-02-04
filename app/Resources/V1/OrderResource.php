<?php

namespace App\Resources\V1;

use App\Models\Order;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Order */
class OrderResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'                   => $this->uuid,
            'date'                 => $this->date->toDateString(),
            'seats_count'          => $this->seats_count,
            'seat_price'           => $this->seat_price,
            'total'                => $this->total,
            'discount'             => $this->discount ?? 0,
            'total_after_discount' => $this->total_after_discount,
            'email'                => $this->email,
            'details'              => $this->trip_details,
        ];
    }
}
