<?php

namespace App\Resources\V1;

use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\User */
class UserResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'    => $this->id,
            'email' => $this->email,
            'token' => $this->token,
        ];
    }
}
