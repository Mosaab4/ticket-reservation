<?php

namespace App\Http\Requests\API\V1;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class StoreSessionRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'seats'   => 'required|array',
            'from_id' => 'required|exists:stations,id',
            'to_id'   => 'required|exists:stations,id',
            'seats.*' => [
                'required',
                'string',
                Rule::in(config('seats.all')),
            ],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
