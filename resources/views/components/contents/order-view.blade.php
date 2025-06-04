@props(['order'])

<button 
    x-data=""
    x-on:click="$dispatch('open-modal', 'order-items-{{ $order->id }}')"
    class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-md text-blue-600 bg-blue-100 hover:bg-blue-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-150"
>
    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
    </svg>
    View
</button>

<!-- Modal for Order Items -->
<x-modal name="order-items-{{ $order->id }}" max-width="6xl">
    <div class="p-6">
        <div class="mb-6">
            <h3 class="text-lg font-medium text-gray-900 mb-2">
                Order #{{ $order->id }} - Order Items
            </h3>
            <div class="bg-gray-50 rounded-lg p-4">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 text-sm">
                    <div>
                        <span class="font-medium text-gray-700">Customer:</span>
                        <span class="text-gray-900">{{ $order->customer->name }}</span>
                    </div>
                    <div>
                        <span class="font-medium text-gray-700">Status:</span>
                        <span class="px-2 py-1 text-xs font-semibold rounded-full 
                            @if($order->status === 'pending') bg-yellow-100 text-yellow-800
                            @elseif($order->status === 'in_progress') bg-blue-100 text-blue-800
                            @elseif($order->status === 'completed') bg-green-100 text-green-800
                            @elseif($order->status === 'delivered') bg-purple-100 text-purple-800
                            @else bg-gray-100 text-gray-800
                            @endif">
                            {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                        </span>
                    </div>
                    <div>
                        <span class="font-medium text-gray-700">Type:</span>
                        <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $order->is_express ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800' }}">
                            {{ $order->is_express ? 'Express' : 'Regular' }}
                        </span>
                    </div>
                    <div>
                        <span class="font-medium text-gray-700">Total:</span>
                        <span class="text-gray-900 font-semibold">₱{{ number_format($order->total_amount, 2) }}</span>
                    </div>
                </div>
                @if($order->special_instructions)
                    <div class="mt-3 pt-3 border-t border-gray-200">
                        <span class="font-medium text-gray-700">Special Instructions:</span>
                        <p class="text-gray-900 mt-1">{{ $order->special_instructions }}</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Order Items Table -->
        <div class="overflow-x-auto bg-white border border-gray-200 rounded-lg">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Service
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Quantity (kg)
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Price per kg
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Subtotal
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Notes
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($order->orderItems as $item)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $item->laundryService->name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ number_format($item->quantity, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                ₱{{ number_format($item->price_per_kg, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                ₱{{ number_format($item->subtotal, 2) }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                {{ $item->notes ?: '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                No items found
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot class="bg-gray-50">
                    <tr>
                        <td colspan="3" class="px-6 py-3 text-right text-sm font-medium text-gray-900">
                            Total Amount:
                        </td>
                        <td class="px-6 py-3 text-sm font-bold text-gray-900">
                            ₱{{ number_format($order->total_amount, 2) }}
                        </td>
                        <td class="px-6 py-3"></td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="mt-6 flex justify-end">
            <button 
                x-on:click="$dispatch('close')"
                class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500"
            >
                Close
            </button>
        </div>
    </div>
</x-modal>