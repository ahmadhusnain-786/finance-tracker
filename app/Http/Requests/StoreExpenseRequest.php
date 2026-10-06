<?php

namespace App\Http\Requests;

use App\Enums\RecurringFrequency;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreExpenseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        $userId = $this->user()->id;

        return [
            'title'  => ['required', 'string', 'max:150'],

            'amount' => [
                'required',
                'numeric',
                'min:0.01',
                'max:9999999999999.99',
                'decimal:0,2',
            ],

            'category_id' => [
                'required',
                Rule::exists('categories', 'id')->where(function ($q) use ($userId) {
                    $q->where('type', 'expense')
                      ->where(function ($sub) use ($userId) {
                          $sub->where('user_id', $userId)->orWhereNull('user_id');
                      });
                }),
            ],

            'payment_method_id' => [
                'nullable',
                Rule::exists('payment_methods', 'id')->where(function ($q) use ($userId) {
                    $q->where('user_id', $userId)->orWhereNull('user_id');
                }),
            ],

            'date'        => ['required', 'date', 'before_or_equal:today'],
            'description' => ['nullable', 'string', 'max:2000'],

            'is_recurring' => ['sometimes', 'boolean'],

            'recurring_frequency' => [
                'nullable',
                'required_if:is_recurring,1',
                Rule::in(array_column(RecurringFrequency::cases(), 'value')),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'amount.min'                  => 'Amount must be greater than zero.',
            'amount.decimal'              => 'Amount can have at most 2 decimal places.',
            'category_id.exists'          => 'The selected category is invalid or not an expense category.',
            'payment_method_id.exists'    => 'The selected payment method is invalid.',
            'date.before_or_equal'        => 'Expense date cannot be in the future.',
            'recurring_frequency.required_if' => 'Please choose a recurring frequency.',
        ];
    }
}