<?php

namespace App\Http\Requests\Bill;

use Illuminate\Foundation\Http\FormRequest;

class ProcessPaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('process payments');
    }

    public function rules(): array
    {
        return [
            'payment_method' => 'required|in:cash,card,digital_wallet',
            'amount_paid' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:500',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $bill = $this->route('bill');
            if ($bill && $this->input('amount_paid') < $bill->total_amount) {
                $validator->errors()->add('amount_paid', 'The amount paid must be at least the total amount of ' . number_format($bill->total_amount, 2) . '.');
            }
        });
    }
}
