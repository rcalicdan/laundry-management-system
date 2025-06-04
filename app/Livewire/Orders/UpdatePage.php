<?php

namespace App\Livewire\Orders;

use App\Models\Order;
use App\Models\Customer;
use App\Models\LaundryService;
use App\Services\OrderCalculatorService;
use App\Services\OrderCreationService;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Exception;
use Illuminate\Support\Facades\DB;

class UpdatePage extends Component
{
    public Order $order;
    public $customer_id;
    public $special_instructions;
    public $is_express = false;
    public $orderItems = [];
    public $customers;
    public $laundryServices;

    public function mount(Order $order)
    {
        $this->authorize('update', $order);
        $this->order = $order;
        $this->loadOrderData();
        $this->loadInitialData();
    }

    public function rules(): array
    {
        return [
            'customer_id' => ['required', 'exists:customers,id'],
            'special_instructions' => ['nullable', 'string', 'max:500'],
            'is_express' => ['boolean'],
            'orderItems' => ['required', 'array', 'min:1'],
            'orderItems.*.laundry_service_id' => ['required', 'exists:laundry_services,id'],
            'orderItems.*.quantity' => ['required', 'numeric', 'min:0.1', 'max:999.99'],
            'orderItems.*.notes' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function updated($propertyName)
    {
        if ($this->shouldSkipValidation($propertyName)) {
            return;
        }

        $this->validateOnly($propertyName);

        if ($this->shouldRecalculateSubtotals($propertyName)) {
            $this->calculateSubtotals();
        }
    }

    public function addOrderItem(): void
    {
        $this->orderItems[] = [
            'id' => null, 
            'laundry_service_id' => '',
            'quantity' => '',
            'price_per_kg' => 0,
            'subtotal' => 0,
            'notes' => '',
        ];
    }

    public function removeOrderItem(int $index): void
    {
        if (count($this->orderItems) > 1) {
            unset($this->orderItems[$index]);
            $this->orderItems = array_values($this->orderItems);
            $this->calculateSubtotals();
        }
    }

    public function calculateSubtotals(): void
    {
        $calculator = new OrderCalculatorService();
        $this->orderItems = $calculator->calculateOrderItemSubtotals(
            $this->orderItems,
            $this->laundryServices,
            $this->is_express
        );

        Log::debug('Calculated order items:', $this->orderItems);
    }

    public function getTotalAmount(): float
    {
        $calculator = new OrderCalculatorService();
        return $calculator->calculateTotalAmount($this->orderItems);
    }

    public function update()
    {
        $this->authorize('update', $this->order);
        $this->validate();
        $this->calculateSubtotals();
        
        Log::debug('Order items before update:', $this->orderItems);
        $totalAmount = $this->getTotalAmount();

        if ($totalAmount <= 0) {
            $this->addError('orderItems', 'Order must have at least one item with quantity greater than 0.');
            return;
        }

        try {
            $this->updateOrder($totalAmount);
            session()->flash('success', 'Order updated successfully.');
            return $this->redirectRoute('orders.table', navigate: true);
        } catch (Exception $e) {
            $this->handleOrderUpdateError($e, $totalAmount);
        }
    }

    public function render()
    {
        return view('livewire.orders.update-page');
    }

    private function loadOrderData(): void
    {
        $this->customer_id = $this->order->customer_id;
        $this->special_instructions = $this->order->special_instructions;
        $this->is_express = $this->order->is_express;

        $this->orderItems = $this->order->orderItems->map(function ($item) {
            return [
                'id' => $item->id,
                'laundry_service_id' => $item->laundry_service_id,
                'quantity' => $item->quantity_kg,
                'price_per_kg' => $item->unit_price,
                'subtotal' => $item->subtotal,
                'notes' => $item->notes,
            ];
        })->toArray();

        if (empty($this->orderItems)) {
            $this->addOrderItem();
        }
    }

    private function loadInitialData(): void
    {
        $this->customers = Customer::orderBy('name')->get();
        $this->laundryServices = LaundryService::orderBy('name')->get();
    }

    private function shouldSkipValidation(string $propertyName): bool
    {
        return !str_starts_with($propertyName, 'orderItems.')
            || str_ends_with($propertyName, '.subtotal')
            || str_ends_with($propertyName, '.price_per_kg');
    }

    private function shouldRecalculateSubtotals(string $propertyName): bool
    {
        return str_contains($propertyName, 'laundry_service_id')
            || str_contains($propertyName, 'quantity')
            || $propertyName === 'is_express';
    }

    private function updateOrder(float $totalAmount): void
    {
        DB::transaction(function () use ($totalAmount) {
            // Update order record
            $this->order->update([
                'customer_id' => $this->customer_id,
                'special_instructions' => $this->special_instructions,
                'is_express' => $this->is_express,
                'total_amount' => $totalAmount,
            ]);

            // Handle order items
            $this->updateOrderItems();

            // Recalculate and validate total
            $this->validateOrderTotal();
        });

        Log::info('Order updated successfully', [
            'order_id' => $this->order->id,
            'order_number' => $this->order->order_number,
            'customer_id' => $this->order->customer_id,
            'user_id' => auth()->id(),
            'total_amount' => $this->order->total_amount,
            'items_count' => count($this->orderItems),
        ]);
    }

    private function updateOrderItems(): void
    {
        $existingItemIds = collect($this->orderItems)
            ->pluck('id')
            ->filter()
            ->toArray();

        $this->order->orderItems()
            ->whereNotIn('id', $existingItemIds)
            ->delete();

        foreach ($this->orderItems as $item) {
            if ($this->isValidOrderItem($item)) {
                $this->updateOrCreateOrderItem($item);
            }
        }
    }

    private function isValidOrderItem(array $item): bool
    {
        return !empty($item['laundry_service_id']) && $item['quantity'] > 0;
    }

    private function updateOrCreateOrderItem(array $item): void
    {
        $orderItemData = [
            'order_id' => $this->order->id,
            'laundry_service_id' => $item['laundry_service_id'],
            'quantity_kg' => $item['quantity'],
            'unit_price' => $item['price_per_kg'],
            'subtotal' => $item['subtotal'],
            'notes' => $item['notes'] ?? null,
        ];

        if ($item['id']) {
            // Update existing item
            $this->order->orderItems()
                ->where('id', $item['id'])
                ->update($orderItemData);
        } else {
            // Create new item
            $this->order->orderItems()->create($orderItemData);
        }
    }

    private function validateOrderTotal(): void
    {
        $actualTotal = $this->order->orderItems()->sum('subtotal');
        $expectedTotal = $this->order->total_amount;

        if (abs($actualTotal - $expectedTotal) > 0.01) {
            throw new Exception("Order total mismatch. Expected: {$expectedTotal}, Actual: {$actualTotal}");
        }

        $this->order->update(['total_amount' => $actualTotal]);
    }

    private function handleOrderUpdateError(Exception $e, float $totalAmount): void
    {
        Log::error('Failed to update order', [
            'error' => $e->getMessage(),
            'order_id' => $this->order->id,
            'customer_id' => $this->customer_id,
            'user_id' => auth()->id(),
            'order_items_count' => count($this->orderItems),
            'total_amount' => $totalAmount,
        ]);

        $this->addError('general', 'Failed to update order. Please try again. If the problem persists, contact support.');

        if (config('app.debug')) {
            $this->addError('debug', 'Debug: ' . $e->getMessage());
        }
    }
}