<?php

namespace App\Http\Requests;

use App\Enums\CategoryType;
use App\Enums\RecurringFrequency;
use App\Enums\TransactionType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRecurringRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        $userId = $this->user()->id;
        $type   = $this->input('type'); // 'income' | 'expense'

        // Category must match type AND be available to this user
        $catType = $type === 'income'
            ? CategoryType::Income->value
            : CategoryType::Expense->value;

        return [
            'title'  => ['required', 'string', 'max:150'],

            'amount' => ['required', 'numeric', 'min:0.01', 'max:9999999999999.99', 'decimal:0,2'],

            'type' => ['required', Rule::in(array_column(TransactionType::cases(), 'value'))],

            'category_id' => [
                'required',
                Rule::exists('categories', 'id')->where(function ($q) use ($userId, $catType) {
                    $q->where('type', $catType)
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

            'description' => ['nullable', 'string', 'max:2000'],

            'frequency' => ['required', Rule::in(array_column(RecurringFrequency::cases(), 'value'))],

            'start_date' => ['required', 'date'],

            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],

            'is_active' => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'amount.min'                 => 'Amount must be greater than zero.',
            'category_id.exists'         => 'The selected category does not match the transaction type or is not available.',
            'payment_method_id.exists'   => 'The selected payment method is not available.',
            'end_date.after_or_equal'    => 'End date must be on or after the start date.',
        ];
    }
}