<?php

namespace App\Http\Requests;

use App\Enums\CategoryType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'name'  => [
                'required',
                'string',
                'max:100',
                // Ensure this user doesn't already have a category with same name+type
                Rule::unique('categories')->where(function ($q) {
                    return $q->where('user_id', $this->user()->id)
                             ->where('type', $this->input('type'));
                }),
            ],
            'type'  => ['required', Rule::in(array_column(CategoryType::cases(), 'value'))],
            'icon'  => ['nullable', 'string', 'max:50'],
            'color' => ['nullable', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.unique' => 'You already have a category with this name and type.',
            'color.regex' => 'Color must be a valid hex code like #2563eb.',
        ];
    }
}