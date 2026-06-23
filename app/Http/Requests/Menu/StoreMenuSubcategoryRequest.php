<?php

namespace App\Http\Requests\Menu;

use Illuminate\Foundation\Http\FormRequest;

class StoreMenuSubcategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('manage menu');
    }

    public function rules(): array
    {
        return [
            'category_id' => ['required', 'exists:menu_categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'image' => ['nullable', 'string'],
            'sort_order' => ['integer', 'min:0'],
            'is_active' => ['boolean'],
        ];
    }
}
