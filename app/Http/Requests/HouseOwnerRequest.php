<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class HouseOwnerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $ownerId = $this->route('house_owner') ? $this->route('house_owner')->id : null;

        return [
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($ownerId),
            ],
            'password' => $this->isMethod('POST')
                ? 'required|string|min:6|confirmed'
                : 'nullable|string|min:6|confirmed',
        ];
    }
}
