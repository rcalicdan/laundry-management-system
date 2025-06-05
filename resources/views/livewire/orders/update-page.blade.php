<section class="w-full">
    <x-contents.heading title="Update Order #{{ $order->order_number }}" />

    <x-contents.layout>
        <div class="p-4 sm:p-6 lg:p-8">
            <form wire:submit.prevent='update' class="space-y-6">

                <!-- General Error Display -->
                @error('general')
                <div class="bg-red-50 border border-red-200 rounded-md p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-red-800">{{ $message }}</p>
                        </div>
                    </div>
                </div>
                @enderror

                @error('debug')
                <div class="bg-yellow-50 border border-yellow-200 rounded-md p-4">
                    <p class="text-sm text-yellow-800">{{ $message }}</p>
                </div>
                @enderror

                <!-- Order Information -->
                <div class="bg-blue-50 border border-blue-200 rounded-md p-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-blue-800">
                                    Order created on {{ $order->created_at->format('M d, Y \a\t g:i A') }}
                                </p>
                            </div>
                        </div>

                        <!-- Payment Status Badge -->
                        <div class="flex items-center space-x-2">
                            @if($order->payment)
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    {{ $order->payment->status->value === 'paid' ? 'bg-green-100 text-green-800' : 
                                       ($order->payment->status->value === 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                                        ($order->payment->status->value === 'failed' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800')) }}">
                                Payment: {{ $order->payment->status->label() }}
                            </span>
                            @else
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                No Payment Record
                            </span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Customer Selection and Status in a Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Customer Selection -->
                    <div>
                        <label for="customer_id" class="block text-sm font-medium text-gray-700">Customer</label>
                        <select id="customer_id" wire:model.live='customer_id'
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                            required>
                            <option value="">Select a customer</option>
                            @foreach ($customers as $customer)
                            <option value="{{ $customer->id }}">{{ $customer->name }} - {{ $customer->phone }}</option>
                            @endforeach
                        </select>
                        @error('customer_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Order Status -->
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700">Order Status</label>
                        <select id="status" wire:model.live='status'
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                            required>
                            <option value="">Select status</option>
                            @foreach ($statusOptions as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('status')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Express Service -->
                <div class="flex items-center">
                    <input id="is_express" type="checkbox" wire:model.live='is_express'
                        class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                    <label for="is_express" class="ml-2 block text-sm text-gray-900">
                        Express Service (+15% surcharge)
                    </label>
                </div>

                <!-- Order Items Section -->
                <div class="border-t pt-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Order Items</h3>
                        <button type="button" wire:click="addOrderItem"
                            class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Add Item
                        </button>
                    </div>

                    @error('orderItems')
                    <p class="mb-4 text-sm text-red-600">{{ $message }}</p>
                    @enderror

                    <div class="space-y-4">
                        @foreach ($orderItems as $index => $item)
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
                                <!-- Service Selection -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Service</label>
                                    <select wire:model.live="orderItems.{{ $index }}.laundry_service_id"
                                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                        <option value="">Select service</option>
                                        @foreach ($laundryServices as $service)
                                        <option value="{{ $service->id }}">
                                            {{ $service->name }} (₱{{ number_format($service->price_per_kg, 2) }}/kg)
                                        </option>
                                        @endforeach
                                    </select>
                                    @error("orderItems.{$index}.laundry_service_id")
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Quantity -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Quantity (kg)</label>
                                    <input type="number" step="0.1" wire:model.live="orderItems.{{ $index }}.quantity"
                                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                        placeholder="0.0">
                                    @error("orderItems.{$index}.quantity")
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Price per kg (Display) -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Price/kg</label>
                                    <input type="text" value="₱{{ number_format($item['price_per_kg'] ?? 0, 2) }}"
                                        readonly
                                        class="mt-1 block w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-md shadow-sm sm:text-sm">
                                </div>

                                <!-- Subtotal (Read-only) -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Subtotal</label>
                                    <input type="text" value="₱{{ number_format($item['subtotal'] ?? 0, 2) }}" readonly
                                        class="mt-1 block w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-md shadow-sm sm:text-sm">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Notes for this item -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Item Notes (Optional)</label>
                                    <textarea wire:model.live="orderItems.{{ $index }}.notes" rows="2"
                                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                        placeholder="Special instructions for this item..."></textarea>
                                    @error("orderItems.{$index}.notes")
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Remove Button -->
                                <div class="flex items-end">
                                    @if (count($orderItems) > 1)
                                    <button type="button" wire:click="removeOrderItem({{ $index }})"
                                        class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                        Remove Item
                                    </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <!-- Total Amount -->
                    <div class="mt-4 bg-gray-100 p-4 rounded-lg">
                        <div class="flex justify-between items-center">
                            <span class="text-lg font-medium text-gray-900">Total Amount:</span>
                            <span class="text-xl font-bold text-indigo-600">₱{{ number_format($this->getTotalAmount(),
                                2) }}</span>
                        </div>
                        @if($is_express)
                        <p class="text-sm text-gray-600 mt-2">* Express service surcharge (15%) applied</p>
                        @endif
                    </div>
                </div>

                <!-- Special Instructions -->
                <div>
                    <label for="special_instructions" class="block text-sm font-medium text-gray-700">Special
                        Instructions</label>
                    <textarea id="special_instructions" wire:model.live='special_instructions' rows="3"
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                        placeholder="Any special instructions for this order..."></textarea>
                    @error('special_instructions')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Submit Buttons -->
                <div class="flex justify-between">
                    <div class="flex space-x-3">
                        <button type="button" wire:click="openPaymentModal"
                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z">
                                </path>
                            </svg>
                            Process Payment
                        </button>
                    </div>

                    <div class="flex space-x-3">
                        <x-utils.link-button :href="route('orders.table')" button-text="Cancel" />
                        <x-utils.submit-button wire-target="update" button-text="Update Order" />
                    </div>
                </div>
            </form>
        </div>
    </x-contents.layout>

    <!-- Payment Modal -->
    @if($showPaymentModal)
    <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" id="payment-modal">
        <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-1/2 lg:w-1/3 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <!-- Modal Header -->
                <div class="flex items-center justify-between pb-4 border-b">
                    <h3 class="text-lg font-medium text-gray-900">Process Payment</h3>
                    <button type="button" wire:click="closePaymentModal" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <!-- Payment Form -->
                <form wire:submit.prevent="processPayment" class="mt-4 space-y-4">
                    @error('payment')
                    <div class="bg-red-50 border border-red-200 rounded-md p-3">
                        <p class="text-sm text-red-800">{{ $message }}</p>
                    </div>
                    @enderror

                    @error('payment_debug')
                    <div class="bg-yellow-50 border border-yellow-200 rounded-md p-3">
                        <p class="text-sm text-yellow-800">{{ $message }}</p>
                    </div>
                    @enderror

                    <!-- Payment Amount -->
                    <div>
                        <label for="payment_amount" class="block text-sm font-medium text-gray-700">Amount</label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 sm:text-sm">₱</span>
                            </div>
                            <input type="number" step="0.01" id="payment_amount" wire:model.live="payment_amount"
                                class="block w-full pl-7 pr-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                placeholder="0.00" required>
                        </div>
                        @error('payment_amount')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Payment Method -->
                    <div>
                        <label for="payment_method" class="block text-sm font-medium text-gray-700">Payment
                            Method</label>
                        <select id="payment_method" wire:model.live="payment_method"
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                            required>
                            <option value="">Select payment method</option>
                            @foreach ($paymentMethodOptions as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('payment_method')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Payment Status -->
                    <div>
                        <label for="payment_status" class="block text-sm font-medium text-gray-700">Payment
                            Status</label>
                        <select id="payment_status" wire:model.live="payment_status"
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                            required>
                            <option value="">Select status</option>
                            @foreach ($paymentStatusOptions as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('payment_status')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Transaction ID -->
                    <div>
                        <label for="transaction_id" class="block text-sm font-medium text-gray-700">Transaction ID
                            (Optional)</label>
                        <input type="text" id="transaction_id" wire:model.live="transaction_id"
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                            placeholder="Enter transaction reference...">
                        @error('transaction_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Payment Notes -->
                    <div>
                        <label for="payment_notes" class="block text-sm font-medium text-gray-700">Notes
                            (Optional)</label>
                        <textarea id="payment_notes" wire:model.live="payment_notes" rows="3"
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                            placeholder="Additional payment notes..."></textarea>
                        @error('payment_notes')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Modal Footer -->
                    <div class="flex justify-end space-x-3 pt-4 border-t">
                        <button type="button" wire:click="closePaymentModal"
                            class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Cancel
                        </button>
                        <button type="submit"
                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                            wire:loading.attr="disabled" wire:target="processPayment">
                            <span wire:loading.remove wire:target="processPayment">Process Payment</span>
                            <span wire:loading wire:target="processPayment" class="flex items-center">
                                <svg class="animate-spin -ml-1 mr-3 h-4 w-4 text-white"
                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                        stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                    </path>
                                </svg>
                                Processing...
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
</section>