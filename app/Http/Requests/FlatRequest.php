<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FlatRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'flat_number' => [
                'required',
                'string',
                'max:255',
                Rule::unique('flats', 'flat_number')
                    ->where(fn ($query) => $query->where('building_id', $this->building_id))
            ],
            'owner_name' => 'required|string|max:255',
            'owner_contact' => 'required|string|max:255',
            'owner_email' => 'required|email|max:255',
            'building_id' => 'required|exists:buildings,id',
        ];
    }
}
