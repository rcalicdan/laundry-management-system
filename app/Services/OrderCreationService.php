<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\LaundryService;
use App\Enums\OrderStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class OrderCreationService
{
    public function createOrder(array $orderData, array $orderItems): Order
    {
        return DB::transaction(function () use ($orderData, $orderItems) {
            $order = $this->createOrderRecord($orderData);
            $this->createOrderItems($order, $orderItems);
            $this->validateOrderTotal($order);
            $this->logOrderCreation($order);

            return $order;
        });
    }

    private function createOrderRecord(array $orderData): Order
    {
        $order = Order::create([
            'customer_id' => $orderData['customer_id'],
            'user_id' => auth()->id(),
            'status' => OrderStatus::PENDING,
            'total_amount' => $orderData['total_amount'],
            'special_instructions' => $orderData['special_instructions'],
            'is_express' => $orderData['is_express'],
        ]);

        if (!$order || !$order->id) {
            throw new Exception('Failed to create order record');
        }

        return $order;
    }

    private function createOrderItems(Order $order, array $orderItems): void
    {
        $orderItemsCreated = 0;

        foreach ($orderItems as $item) {
            if ($this->isValidOrderItem($item)) {
                $this->validateLaundryService($item['laundry_service_id']);
                $this->createOrderItem($order, $item);
                $orderItemsCreated++;
            }
        }

        if ($orderItemsCreated === 0) {
            throw new Exception('No valid order items were created');
        }
    }

    private function isValidOrderItem(array $item): bool
    {
        return !empty($item['laundry_service_id']) && $item['quantity'] > 0;
    }

    private function validateLaundryService(int $serviceId): void
    {
        $laundryService = LaundryService::find($serviceId);
        if (!$laundryService) {
            throw new Exception("Laundry service with ID {$serviceId} not found");
        }
    }

    private function createOrderItem(Order $order, array $item): void
    {
        $orderItem = OrderItem::create([
            'order_id' => $order->id,
            'laundry_service_id' => $item['laundry_service_id'],
            'quantity_kg' => $item['quantity'],
            'unit_price' => $item['price_per_kg'],
            'subtotal' => $item['subtotal'],
            'notes' => $item['notes'] ?? null,
        ]);

        if (!$orderItem) {
            throw new Exception('Failed to create order item');
        }
    }

    private function validateOrderTotal(Order $order): void
    {
        $actualTotal = $order->orderItems()->sum('subtotal');
        $expectedTotal = $order->total_amount;

        if (abs($actualTotal - $expectedTotal) > 0.01) {
            throw new Exception("Order total mismatch. Expected: {$expectedTotal}, Actual: {$actualTotal}");
        }

        $order->update(['total_amount' => $actualTotal]);
    }

    private function logOrderCreation(Order $order): void
    {
        Log::info('Order created successfully', [
            'order_id' => $order->id,
            'order_number' => $order->order_number,
            'customer_id' => $order->customer_id,
            'user_id' => $order->user_id,
            'total_amount' => $order->total_amount,
            'items_count' => $order->orderItems()->count(),
        ]);
    }
}
