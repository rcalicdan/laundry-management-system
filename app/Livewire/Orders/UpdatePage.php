<?php

namespace App\Livewire\Orders;

use App\Models\Order;
use App\Models\Customer;
use App\Models\LaundryService;
use App\Enums\OrderStatus;
use App\Services\OrderCalculatorService;
use App\Services\OrderUpdateService;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Exception;

class UpdatePage extends Component
{
    public Order $order;
    public $customer_id;
    public $status;
    public $special_instructions;
    public $is_express = false;
    public $orderItems = [];
    public $customers;
    public $laundryServices;
    public $statusOptions;

    private OrderCalculatorService $calculator;
    private OrderUpdateService $orderUpdateService;

    public function boot(OrderCalculatorService $calculator, OrderUpdateService $orderUpdateService)
    {
        $this->calculator = $calculator;
        $this->orderUpdateService = $orderUpdateService;
    }

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
            'status' => ['required', 'in:' . implode(',', OrderStatus::values())],
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
        $this->orderItems = $this->calculator->calculateOrderItemSubtotals(
            $this->orderItems,
            $this->laundryServices,
            $this->is_express
        );

        Log::debug('Calculated order items:', $this->orderItems);
    }

    public function getTotalAmount(): float
    {
        return $this->calculator->calculateTotalAmount($this->orderItems);
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
            $orderData = [
                'customer_id' => $this->customer_id,
                'status' => OrderStatus::from($this->status),
                'special_instructions' => $this->special_instructions,
                'is_express' => $this->is_express,
                'total_amount' => $totalAmount,
            ];

            $this->orderUpdateService->updateOrder($this->order, $orderData, $this->orderItems);

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
        $this->status = $this->order->status->value;
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
        $this->statusOptions = OrderStatus::options();
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