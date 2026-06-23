<?php

namespace App\Http\Requests\Menu;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTagRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('manage menu');
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', 'unique:tags,name,' . $this->route('tag')->id],
            'color' => ['nullable', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/i'],
        ];
    }
}
