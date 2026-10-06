<?php

namespace App\Http\Requests;

use App\Enums\PaymentMethodType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePaymentMethodRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $method = $this->route('payment_method');

        return [
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('payment_methods')->where(function ($q) use ($method) {
                    return $q->where('user_id', $this->user()->id)
                             ->where('id', '!=', $method->id);
                }),
            ],
            'type'        => ['required', Rule::in(array_column(PaymentMethodType::cases(), 'value'))],
            'description' => ['nullable', 'string', 'max:500'],
            'is_active'   => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.unique' => 'You already have a payment method with this name.',
        ];
    }
}