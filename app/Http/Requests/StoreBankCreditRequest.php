<?php

namespace App\Http\Requests;

use App\Models\Consumer;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;

class StoreBankCreditRequest extends FormRequest
{
    protected ?Consumer $consumer = null;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->consumer = Consumer::where('email', $this->input('email'))->first();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:4', 'max:255', 'regex:/^[A-Za-z\s]+$/'],
            'email' => 'required|email|max:255',
            'amount' => ['required', 'numeric', 'min:1', function ($attribute, $value, $fail) {
                $totalCreditAmount = $this->consumer ? $this->consumer->total_credit_amount + $value : $value;

                // Check if the consumer's total credit plus the new amount exceeds the limit
                if ($totalCreditAmount > 80000) {
                    $fail('The total credit amount for this consumer exceeds the limit of 80000.');
                }
            }],
            'months' => 'required|numeric|min:3|max:120',
        ];
    }

    /**
     * Get the consumer associated with the request.
     *
     * @return Consumer|null
     */
    public function getConsumer(): ?Consumer
    {
        return $this->consumer;
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.regex' => 'The name may only include letters and spaces.',
        ];
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param Validator $validator
     * @throws ValidationException
     */
    protected function failedValidation(Validator $validator): void
    {
        throw new ValidationException($validator);
    }
}
