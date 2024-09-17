<?php

namespace App\Http\PaymentGateways\Requests;

use Illuminate\Foundation\Http\FormRequest;

class Mpesa extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'safaricom_passkey' => ['required', 'string'],
            'mpesa_business_shortcode' => ['required', 'string'],
            'mpesa_environment' => ['required', 'string'],
            'mpesa_consumer_key' => ['required', 'string'],
            'mpesa_consumer_secret' => ['required', 'string'],
            'mpesa_status' => ['nullable', 'numeric'],
        ];
    }
}
