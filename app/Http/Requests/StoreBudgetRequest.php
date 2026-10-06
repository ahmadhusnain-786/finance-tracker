<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreBudgetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        $userId = $this->user()->id;

        return [
            'category_id' => [
                'required',
                Rule::exists('categories', 'id')->where(function ($q) use ($userId) {
                    $q->where('type', 'expense')
                      ->where(function ($sub) use ($userId) {
                          $sub->where('user_id', $userId)->orWhereNull('user_id');
                      });
                }),
                // No duplicate budget for same category+period
                Rule::unique('budgets')->where(function ($q) use ($userId) {
                    return $q->where('user_id', $userId)
                             ->where('period', $this->input('period', 'monthly'));
                }),
            ],

            'amount' => ['required', 'numeric', 'min:0.01', 'max:9999999999999.99', 'decimal:0,2'],

            'period' => ['required', Rule::in(['weekly', 'monthly', 'yearly'])],

            'notes' => ['nullable', 'string', 'max:1000'],

            'is_active' => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'category_id.exists' => 'The selected category is invalid or is not an expense category.',
            'category_id.unique' => 'You already have a budget for this category in the selected period.',
            'amount.min'         => 'Budget amount must be greater than zero.',
            'amount.decimal'     => 'Budget amount can have at most 2 decimal places.',
        ];
    }
}