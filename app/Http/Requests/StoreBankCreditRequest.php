<?php

namespace App\Http\Requests;

use App\Models\Consumer;
use Illuminate\Foundation\Http\FormRequest;

class StoreBankCreditRequest extends FormRequest
{

    protected $consumer;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function prepareForValidation()
    {
        $this->consumer = Consumer::where('email', $this->email)->first();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:4', 'max:255', 'regex:/^[A-Za-z\s]+$/'],
            'email' => 'required|email|max:255',
            'amount' => ['required', 'numeric', 'min:1', function($attribute, $value, $fail) {

                // Calculate the new total credit amount to check if it exceeds the limit
                $totalCreditAmount = $this->consumer ? $this->consumer->total_credit_amount + $value : $value;

                // Check if the consumer's total credit plus the new amount exceeds the limit
                if ($totalCreditAmount > 80000) {
                    $fail('The total credit amount exceeds the limit of 80000.');
                }
            }],
            'months' => 'required|numeric|min:3|max:120',
        ];
    }

    public function getConsumer()
    {
        return $this->consumer;
    }

    public function messages()
    {
        return [
            'name.regex' => 'The name may only include letters and spaces.',
        ];
    }
}
