<?php

namespace App\Http\Requests;

use App\Enums\Activity;
use App\Enums\OrderType;
use App\Rules\ValidJsonOrder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Smartisan\Settings\Facades\Settings;

class TableOrderRequest extends FormRequest
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
            'dining_table_id' => ['required', 'numeric'],
            'customer_id' => ['nullable', 'numeric'],
            'branch_id' => ['required', 'numeric'],
            'subtotal' => ['required', 'numeric'],
            'discount' => ['nullable', 'numeric'],
            'delivery_charge' => request('order_type') === OrderType::DELIVERY ? ['required', 'numeric'] : ['nullable'],
            'total' => ['required', 'numeric'],
            'order_type' => ['required', 'numeric'],
            'is_advance_order' => ['required', 'numeric'],
            'address_id' => request('order_type') === OrderType::DELIVERY ? ['required', 'numeric'] : ['nullable'],
            'delivery_time' => request('order_type') === OrderType::DELIVERY ? ['required', 'string'] : ['nullable'],
            'source' => ['required', 'numeric'],
            'items' => ['required', 'json', new ValidJsonOrder],
            'customer' => ['required_if:payment_method,digitalPayment', 'array'],
            'customer.phone' => Rule::when(
                fn () => $this->input('payment_method') === 'digitalPayment',
                ['required_if', 'integer', 'min_digits:9', 'max_digits:9']
            ),
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if (request('order_type') == OrderType::DELIVERY && Settings::group('order_setup')->get('order_setup_delivery') == Activity::DISABLE) {
                $validator->errors()->add('order_type', 'This order type is disabled now you can try another order type right now or call the management.');
            } elseif (request('order_type') == OrderType::TAKEAWAY && Settings::group('order_setup')->get('order_setup_takeaway') == Activity::DISABLE) {
                $validator->errors()->add('order_type', 'This order type is disabled now you can try another order type right now or call the management.');
            } elseif (blank(request('order_type'))) {
                $validator->errors()->add('order_type', 'This order type is disabled now you can try another order type right now or call the management.');
            }
        });
    }
}
