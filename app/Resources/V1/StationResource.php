<?php

namespace App\Resources\V1;

use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\Station */
class StationResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'   => $this->id,
            'name' => $this->name,
        ];
    }
}
