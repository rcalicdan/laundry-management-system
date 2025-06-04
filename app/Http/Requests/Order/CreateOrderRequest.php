<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'customer_id' => ['required', 'exists:customers,id'],
            'pickup_date' => ['required', 'date', 'after_or_equal:today'],
            'delivery_date' => ['nullable', 'date', 'after:pickup_date'],
            'special_instructions' => ['nullable', 'string', 'max:500'],
            'is_express' => ['boolean'],
            'orderItems' => ['required', 'array', 'min:1'],
            'orderItems.*.laundry_service_id' => ['required', 'exists:laundry_services,id'],
            'orderItems.*.quantity' => ['required', 'numeric', 'min:0.1', 'max:999.99'],
            'orderItems.*.notes' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'orderItems.required' => 'At least one order item is required.',
            'orderItems.*.laundry_service_id.required' => 'Please select a laundry service.',
            'orderItems.*.quantity.required' => 'Quantity is required.',
            'orderItems.*.quantity.min' => 'Quantity must be at least 0.1.',
        ];
    }
}
