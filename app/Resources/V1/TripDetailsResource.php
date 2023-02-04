<?php

namespace App\Resources\V1;

use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\Trip */
class TripDetailsResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'       => $this->uuid,
            'from'     => $this->pickup?->name,
            'to'       => $this->destination?->name,
            'distance' => $this->distance,
            'seats'    => $this->seats,
        ];
    }
}
