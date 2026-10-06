<?php

namespace App\Http\Requests;

use App\Enums\CategoryType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Policy handles ownership check
    }

    public function rules(): array
    {
        $category = $this->route('category');

        return [
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('categories')->where(function ($q) use ($category) {
                    return $q->where('user_id', $this->user()->id)
                             ->where('type', $this->input('type'))
                             ->where('id', '!=', $category->id);
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