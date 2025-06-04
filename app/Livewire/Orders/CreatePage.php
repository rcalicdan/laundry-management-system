<?php

namespace App\Livewire\Orders;

use App\Models\Order;
use App\Models\Customer;
use App\Models\LaundryService;
use App\Models\OrderItem;
use App\Enums\OrderStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Exception;

class CreatePage extends Component
{
    public $customer_id;
    public $pickup_date;
    public $delivery_date;
    public $special_instructions;
    public $is_express = false;
    public $orderItems = [];
    public $customers;
    public $laundryServices;
    public $createdOrderId = null;

    public function mount()
    {
        $this->customers = Customer::orderBy('name')->get();
        $this->laundryServices = LaundryService::orderBy('name')->get();
        $this->addOrderItem();
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

    public function updated($propertyName)
    {
        if (!str_starts_with($propertyName, 'orderItems.') || str_ends_with($propertyName, '.subtotal')) {
            $this->validateOnly($propertyName);
        }
        
        if (str_contains($propertyName, 'laundry_service_id') || str_contains($propertyName, 'quantity')) {
            $this->calculateSubtotals();
        }
    }

    public function addOrderItem()
    {
        $this->orderItems[] = [
            'laundry_service_id' => '',
            'quantity' => '',
            'price_per_kg' => 0,
            'subtotal' => 0,
            'notes' => '', 
        ];
    }

    public function removeOrderItem($index)
    {
        if (count($this->orderItems) > 1) {
            unset($this->orderItems[$index]);
            $this->orderItems = array_values($this->orderItems); 
            $this->calculateSubtotals();
        }
    }

    public function calculateSubtotals()
    {
        foreach ($this->orderItems as $index => &$item) {
            if ($item['laundry_service_id'] && $item['quantity']) {
                $service = $this->laundryServices->find($item['laundry_service_id']);
                if ($service) {
                    $item['price_per_kg'] = $service->price_per_kg;
                    $item['subtotal'] = $service->price_per_kg * $item['quantity'];
                }
            } else {
                $item['subtotal'] = 0;
            }
        }
    }

    public function getTotalAmount()
    {
        return collect($this->orderItems)->sum('subtotal');
    }

    public function create()
    {
        $this->authorize('create', Order::class);
        
        $this->validateOnly([
            'customer_id',
            'pickup_date', 
            'delivery_date',
            'special_instructions',
            'is_express',
            'orderItems'
        ]);

        // Calculate final subtotals
        $this->calculateSubtotals();
        
        $totalAmount = $this->getTotalAmount();
        
        if ($totalAmount <= 0) {
            $this->addError('orderItems', 'Order must have at least one item with quantity greater than 0.');
            return;
        }

        // Use database transaction for ACID compliance
        try {
            DB::transaction(function () use ($totalAmount) {
                // Create the order
                $order = Order::create([
                    'customer_id' => $this->customer_id,
                    'user_id' => auth()->id(),
                    'status' => OrderStatus::PENDING,
                    'total_amount' => $totalAmount,
                    'pickup_date' => $this->pickup_date,
                    'delivery_date' => $this->delivery_date,
                    'special_instructions' => $this->special_instructions,
                    'is_express' => $this->is_express,
                ]);

                // Validate that order was created successfully
                if (!$order || !$order->id) {
                    throw new Exception('Failed to create order record');
                }

                // Create order items
                $orderItemsCreated = 0;
                foreach ($this->orderItems as $item) {
                    if ($item['laundry_service_id'] && $item['quantity'] > 0) {
                        // Verify laundry service still exists (additional safety check)
                        $laundryService = LaundryService::find($item['laundry_service_id']);
                        if (!$laundryService) {
                            throw new Exception("Laundry service with ID {$item['laundry_service_id']} not found");
                        }

                        $orderItem = OrderItem::create([
                            'order_id' => $order->id,
                            'laundry_service_id' => $item['laundry_service_id'],
                            'quantity' => $item['quantity'],
                            'price_per_kg' => $item['price_per_kg'],
                            'subtotal' => $item['subtotal'],
                            'notes' => $item['notes'] ?? null, // Added notes field
                        ]);

                        if (!$orderItem) {
                            throw new Exception('Failed to create order item');
                        }

                        $orderItemsCreated++;
                    }
                }

                // Ensure at least one order item was created
                if ($orderItemsCreated === 0) {
                    throw new Exception('No valid order items were created');
                }

                // Verify the total amount matches what we calculated
                $actualTotal = $order->orderItems()->sum('subtotal');
                if (abs($actualTotal - $totalAmount) > 0.01) { // Allow for small floating point differences
                    throw new Exception('Order total mismatch. Expected: ' . $totalAmount . ', Actual: ' . $actualTotal);
                }

                // Update order with recalculated total for consistency
                $order->update(['total_amount' => $actualTotal]);

                // Log successful order creation for audit trail
                Log::info('Order created successfully', [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                    'customer_id' => $order->customer_id,
                    'user_id' => $order->user_id,
                    'total_amount' => $order->total_amount,
                    'items_count' => $orderItemsCreated,
                ]);

                // Store order ID for redirect
                $this->createdOrderId = $order->id;
            });

            // Transaction completed successfully
            session()->flash('success', 'Order created successfully.');
            return $this->redirectRoute('orders.table', navigate: true);

        } catch (Exception $e) {
            // Log the error for debugging
            Log::error('Failed to create order', [
                'error' => $e->getMessage(),
                'customer_id' => $this->customer_id,
                'user_id' => auth()->id(),
                'order_items_count' => count($this->orderItems),
                'total_amount' => $totalAmount,
            ]);

            // Show user-friendly error message
            $this->addError('general', 'Failed to create order. Please try again. If the problem persists, contact support.');
            
            if (config('app.debug')) {
                $this->addError('debug', 'Debug: ' . $e->getMessage());
            }
        }
    }

    public function render()
    {
        return view('livewire.orders.create-page');
    }
}