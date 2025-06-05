<?php

namespace App\Livewire\Orders;

use App\Models\Order;
use App\Models\Customer;
use App\Models\LaundryService;
use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Services\OrderCalculatorService;
use App\Services\OrderUpdateService;
use App\Services\PaymentService;
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
    public $showPaymentModal = false;
    public $payment_amount;
    public $payment_method;
    public $payment_status;
    public $transaction_id;
    public $payment_notes;
    public $paymentMethodOptions;
    public $paymentStatusOptions;

    private OrderCalculatorService $calculator;
    private OrderUpdateService $orderUpdateService;
    private PaymentService $paymentService;

    public function boot(
        OrderCalculatorService $calculator,
        OrderUpdateService $orderUpdateService,
        PaymentService $paymentService
    ) {
        $this->calculator = $calculator;
        $this->orderUpdateService = $orderUpdateService;
        $this->paymentService = $paymentService;
    }

    public function mount(Order $order)
    {
        $this->authorize('update', $order);
        $this->order = $order;
        $this->loadOrderData();
        $this->loadInitialData();
        $this->loadPaymentData();
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

    protected function getPaymentRules(): array
    {
        return [
            'payment_amount' => ['required', 'numeric', 'min:0.01'],
            'payment_method' => ['required', 'in:' . implode(',', PaymentMethod::values())],
            'payment_status' => ['required', 'in:' . implode(',', PaymentStatus::values())],
            'transaction_id' => ['nullable', 'string', 'max:255'],
            'payment_notes' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function updated($propertyName)
    {
        if ($this->shouldSkipValidation($propertyName)) {
            return;
        }

        if (str_starts_with($propertyName, 'payment_')) {
            $this->validateOnly($propertyName, $this->getPaymentRules());
        } else {
            $this->validateOnly($propertyName);
        }

        if ($this->shouldRecalculateSubtotals($propertyName)) {
            $this->calculateSubtotals();
            $this->updatePaymentAmount();
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
            $this->updatePaymentAmount();
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

    public function openPaymentModal(): void
    {
        if (!$this->paymentService->canProcessPayment($this->order)) {
            $this->addError('payment', 'Cannot process payment for orders with zero total amount.');
            return;
        }

        $this->updatePaymentAmount();
        $this->resetPaymentValidation();
        $this->dispatch('open-payment-modal');
    }


    public function closePaymentModal(): void
    {
        $this->resetPaymentValidation();
        $this->dispatch('close-payment-modal');
    }

    public function processPayment(): void
    {
        $this->validate($this->getPaymentRules());

        try {
            $paymentData = [
                'amount' => $this->payment_amount,
                'payment_method' => $this->payment_method,
                'status' => $this->payment_status,
                'transaction_id' => $this->transaction_id,
                'notes' => $this->payment_notes,
            ];

            $this->paymentService->processPayment($this->order, $paymentData);
            $this->order->refresh();
            session()->flash('success', 'Payment processed successfully.');
            $this->dispatch('close-payment-modal');
        } catch (Exception $e) {
            Log::error('Failed to process payment', [
                'error' => $e->getMessage(),
                'order_id' => $this->order->id,
                'payment_data' => $paymentData ?? [],
            ]);

            $this->addError('payment', 'Failed to process payment. Please try again.');

            if (config('app.debug')) {
                $this->addError('payment_debug', 'Debug: ' . $e->getMessage());
            }
        }
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
            return $this->redirect(route('orders.show', ['order' => $this->order->id]));
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
        $this->paymentMethodOptions = PaymentMethod::options();
        $this->paymentStatusOptions = PaymentStatus::options();
    }

    private function loadPaymentData(): void
    {
        $paymentData = $this->paymentService->getPaymentData($this->order);

        $this->payment_amount = $paymentData['amount'];
        $this->payment_method = $paymentData['payment_method'];
        $this->payment_status = $paymentData['status'];
        $this->transaction_id = $paymentData['transaction_id'];
        $this->payment_notes = $paymentData['notes'];
    }

    private function updatePaymentAmount(): void
    {
        $this->payment_amount = $this->getTotalAmount();
    }

    private function resetPaymentValidation(): void
    {
        $this->resetErrorBag([
            'payment_amount',
            'payment_method',
            'payment_status',
            'transaction_id',
            'payment_notes',
            'payment',
            'payment_debug'
        ]);
    }

    private function shouldSkipValidation(string $propertyName): bool
    {
        return !str_starts_with($propertyName, 'orderItems.')
            || str_ends_with($propertyName, '.subtotal')
            || str_ends_with($propertyName, '.price_per_kg')
            || $propertyName === 'showPaymentModal';
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
