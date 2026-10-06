<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateGoalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'          => ['required', 'string', 'max:150'],
            'target_amount' => ['required', 'numeric', 'min:0.01', 'max:9999999999999.99', 'decimal:0,2'],
            'target_date'   => ['nullable', 'date'],
            'description'   => ['nullable', 'string', 'max:2000'],
            'status'        => ['nullable', Rule::in(['active', 'completed', 'cancelled'])],
        ];
    }

    public function messages(): array
    {
        return [
            'target_amount.min' => 'Target amount must be greater than zero.',
        ];
    }
}