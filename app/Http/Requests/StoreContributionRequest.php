<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreContributionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'amount'            => ['required', 'numeric', 'min:0.01', 'max:9999999999999.99', 'decimal:0,2'],
            'contribution_date' => ['required', 'date', 'before_or_equal:today'],
            'note'              => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'amount.min'                    => 'Contribution must be greater than zero.',
            'contribution_date.before_or_equal' => 'Contribution date cannot be in the future.',
        ];
    }
}