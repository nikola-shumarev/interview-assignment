<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePaymentRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Ensure proper authentication checks are in place
    }

    public function rules()
    {
        return [
            'bank_credit' => 'required',
            'amount' => 'required|numeric|min:1'
        ];
    }
}
