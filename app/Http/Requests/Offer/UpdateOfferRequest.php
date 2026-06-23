<?php

namespace App\Http\Requests\Offer;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOfferRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('manage offers');
    }

    public function rules(): array
    {
        return [
            'name'                  => ['required', 'string', 'max:255'],
            'description'           => ['nullable', 'string'],
            'type'                  => ['required', 'in:percentage,fixed'],
            'value'                 => ['required', 'numeric', 'min:0.01'],
            'min_order_amount'      => ['nullable', 'numeric', 'min:0'],
            'max_discount_amount'   => ['nullable', 'numeric', 'min:0.01'],
            'is_active'             => ['boolean'],
            'starts_at'             => ['required', 'date'],
            'ends_at'               => ['required', 'date', 'after:starts_at'],
            'applicable_days'       => ['nullable', 'array'],
            'applicable_days.*'     => ['in:monday,tuesday,wednesday,thursday,friday,saturday,sunday'],
            'applicable_from_time'  => ['nullable', 'date_format:H:i', 'required_with:applicable_to_time'],
            'applicable_to_time'    => ['nullable', 'date_format:H:i', 'required_with:applicable_from_time', 'after:applicable_from_time'],
            'menu_item_ids'         => ['nullable', 'array'],
            'menu_item_ids.*'       => ['integer', 'exists:menu_items,id'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($v) {
            if ($this->input('type') === 'percentage' && (float) $this->input('value') > 100) {
                $v->errors()->add('value', 'Percentage discount cannot exceed 100%.');
            }
        });
    }
}
