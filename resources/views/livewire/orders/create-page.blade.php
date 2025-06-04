<section class="w-full">
    <x-contents.heading title="Create New Order" />

    <x-contents.layout>
        <div class="p-4 sm:p-6 lg:p-8">
            <form wire:submit.prevent='create' class="space-y-6">
                
                <!-- General Error Display -->
                @error('general')
                    <div class="bg-red-50 border border-red-200 rounded-md p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
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

                <!-- Express Service -->
                <div class="flex items-center">
                    <input id="is_express" type="checkbox" wire:model.live='is_express'
                        class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                    <label for="is_express" class="ml-2 block text-sm text-gray-900">
                        Express Service (15% charges apply to all service price)
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

                                    <!-- Subtotal (Read-only) -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Subtotal</label>
                                        <input type="text" value="₱{{ number_format($item['subtotal'] ?? 0, 2) }}" readonly
                                            class="mt-1 block w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-md shadow-sm sm:text-sm">
                                    </div>

                                    <!-- Remove Button -->
                                    <div class="flex items-end">
                                        @if (count($orderItems) > 1)
                                            <button type="button" wire:click="removeOrderItem({{ $index }})"
                                                class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                                Remove
                                            </button>
                                        @endif
                                    </div>
                                </div>

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
                            </div>
                        @endforeach
                    </div>

                    <!-- Total Amount -->
                    <div class="mt-4 bg-gray-100 p-4 rounded-lg">
                        <div class="flex justify-between items-center">
                            <span class="text-lg font-medium text-gray-900">Total Amount:</span>
                            <span class="text-xl font-bold text-indigo-600">₱{{ number_format($this->getTotalAmount(), 2) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Special Instructions -->
                <div>
                    <label for="special_instructions" class="block text-sm font-medium text-gray-700">Special Instructions</label>
                    <textarea id="special_instructions" wire:model.live='special_instructions' rows="3"
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                        placeholder="Any special instructions for this order..."></textarea>
                    @error('special_instructions')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Submit Buttons -->
                <div class="flex justify-end space-x-3">
                    <x-utils.link-button :href="route('orders.table')" button-text="Cancel" />
                    <x-utils.submit-button wire-target="create" button-text="Create Order" />
                </div>
            </form>
        </div>
    </x-contents.layout>
</section>