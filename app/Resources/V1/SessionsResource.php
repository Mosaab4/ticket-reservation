<?php

namespace App\Resources\V1;

use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\TripSession */
class SessionsResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'    => $this->uuid,
            'seats' => $this->seats,
            'date'  => $this->date->toDateString(),
            'trip'  => new TripResource($this->whenLoaded('trip')),
        ];
    }
}
