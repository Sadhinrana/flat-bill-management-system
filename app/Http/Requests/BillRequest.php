<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BillRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // Same rules for create and update, with status optional on create
        $rules = [
            'month' => 'required|string|max:7',
            'amount' => 'required|numeric|min:0',
            'due_amount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
            'flat_id' => 'required|exists:flats,id',
            'bill_category_id' => 'required|exists:bill_categories,id',
        ];

        // For update requests (PUT/PATCH), allow status field and validate it
        if (in_array($this->method(), ['PUT', 'PATCH'])) {
            $rules['status'] = 'required|in:paid,unpaid';
        } else {
            $rules['status'] = 'nullable|in:paid,unpaid';
        }

        return $rules;
    }
}
