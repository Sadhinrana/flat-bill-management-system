<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FlatRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'flat_number' => 'required|string|max:255',
            'owner_name' => 'required|string|max:255',
            'owner_contact' => 'required|string|max:255',
            'owner_email' => 'nullable|email|max:255',
            'building_id' => 'required|exists:buildings,id',
        ];
    }
}
