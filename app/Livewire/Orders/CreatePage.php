<?php

namespace App\Livewire\Orders;

use App\Models\Order;
use App\Models\Customer;
use App\Models\LaundryService;
use App\Services\OrderCreationService;
use App\Services\OrderCalculatorService;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Exception;

class CreatePage extends Component
{
    public $customer_id;
    public $special_instructions;
    public $is_express = false;
    public $orderItems = [];
    public $customers;
    public $laundryServices;

    public function mount()
    {
        $this->loadInitialData();
        $this->addOrderItem();
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

    public function create()
    {
        $this->authorize('create', Order::class);
        $this->validate();
        $this->calculateSubtotals();
        Log::debug('Order items before service call:', $this->orderItems);
        $totalAmount = $this->getTotalAmount();

        if ($totalAmount <= 0) {
            $this->addError('orderItems', 'Order must have at least one item with quantity greater than 0.');
            return;
        }

        try {
            $orderCreationService = new OrderCreationService();

            $order = $orderCreationService->createOrder(
                $this->getOrderData($totalAmount),
                $this->orderItems
            );

            session()->flash('success', 'Order created successfully.');
            return $this->redirectRoute('orders.table', navigate: true);
        } catch (Exception $e) {
            $this->handleOrderCreationError($e, $totalAmount);
        }
    }

    public function render()
    {
        return view('livewire.orders.create-page');
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

    private function getOrderData(float $totalAmount): array
    {
        return [
            'customer_id' => $this->customer_id,
            'special_instructions' => $this->special_instructions,
            'is_express' => $this->is_express,
            'total_amount' => $totalAmount,
        ];
    }

    private function handleOrderCreationError(Exception $e, float $totalAmount): void
    {
        Log::error('Failed to create order', [
            'error' => $e->getMessage(),
            'customer_id' => $this->customer_id,
            'user_id' => auth()->id(),
            'order_items_count' => count($this->orderItems),
            'total_amount' => $totalAmount,
        ]);

        $this->addError('general', 'Failed to create order. Please try again. If the problem persists, contact support.');

        if (config('app.debug')) {
            $this->addError('debug', 'Debug: ' . $e->getMessage());
        }
    }
}