<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TenantRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'contact' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('tenants', 'email')
                    ->where(fn ($query) => $query->where('building_id', $this->building_id))
            ],
            'building_id' => 'required|exists:buildings,id',
            'flat_id' => [
                'nullable',
                Rule::exists('flats', 'id')
                    ->where(fn ($query) => $query->where('building_id', $this->building_id)),
            ]
        ];
    }
}
