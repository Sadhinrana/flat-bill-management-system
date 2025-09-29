<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BillCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'building_id' => 'required|exists:buildings,id',
        ];
    }
}
