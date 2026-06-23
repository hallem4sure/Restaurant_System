<?php

namespace App\Http\Requests\Table;

use Illuminate\Foundation\Http\FormRequest;

class StoreTableRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('manage tables');
    }

    public function rules(): array
    {
        return [
            'table_number' => ['required', 'string', 'max:50', 'unique:restaurant_tables,table_number'],
            'capacity' => ['required', 'integer', 'min:1', 'max:20'],
            'status' => ['required', 'in:available,occupied,reserved,maintenance'],
            'is_private' => ['boolean'],
        ];
    }
}
