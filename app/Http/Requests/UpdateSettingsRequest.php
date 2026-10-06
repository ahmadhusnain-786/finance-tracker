<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'currency'    => ['required', Rule::in(['PKR', 'USD', 'EUR', 'GBP', 'AED', 'SAR', 'CAD', 'AUD'])],
            'timezone'    => ['required', 'string', 'max:64'],
            'date_format' => ['required', Rule::in(['d/m/Y', 'm/d/Y', 'Y-m-d', 'd-m-Y'])],
        ];
    }
}