<?php

namespace App\Services;

use App\Models\Order;
use App\Models\LaundryService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class OrderUpdateService
{
    public function updateOrder(Order $order, array $orderData, array $orderItems): void
    {
        DB::transaction(function () use ($order, $orderData, $orderItems) {
            $this->updateOrderRecord($order, $orderData);
            $this->updateOrderItems($order, $orderItems);
            $this->validateAndUpdateOrderTotal($order);
        });

        $this->logOrderUpdate($order, $orderData, $orderItems);
    }

    private function updateOrderRecord(Order $order, array $orderData): void
    {
        $order->update([
            'customer_id' => $orderData['customer_id'],
            'special_instructions' => $orderData['special_instructions'],
            'is_express' => $orderData['is_express'],
            'total_amount' => $orderData['total_amount'],
        ]);
    }

    private function updateOrderItems(Order $order, array $orderItems): void
    {
        $existingItemIds = collect($orderItems)
            ->pluck('id')
            ->filter()
            ->toArray();

        $order->orderItems()
            ->whereNotIn('id', $existingItemIds)
            ->delete();

        foreach ($orderItems as $item) {
            if ($this->isValidOrderItem($item)) {
                $this->updateOrCreateOrderItem($order, $item);
            }
        }
    }

    private function isValidOrderItem(array $item): bool
    {
        return !empty($item['laundry_service_id']) && $item['quantity'] > 0;
    }

    private function updateOrCreateOrderItem(Order $order, array $item): void
    {
        $orderItemData = [
            'order_id' => $order->id,
            'laundry_service_id' => $item['laundry_service_id'],
            'quantity_kg' => $item['quantity'],
            'unit_price' => $item['price_per_kg'],
            'subtotal' => $item['subtotal'],
            'notes' => $item['notes'] ?? null,
        ];

        if ($item['id']) {
            $order->orderItems()
                ->where('id', $item['id'])
                ->update($orderItemData);
        } else {
            $order->orderItems()->create($orderItemData);
        }
    }

    private function validateAndUpdateOrderTotal(Order $order): void
    {
        $actualTotal = $order->orderItems()->sum('subtotal');
        $expectedTotal = $order->total_amount;

        if (abs($actualTotal - $expectedTotal) > 0.01) {
            throw new Exception("Order total mismatch. Expected: {$expectedTotal}, Actual: {$actualTotal}");
        }

        $order->update(['total_amount' => $actualTotal]);
    }

    private function logOrderUpdate(Order $order, array $orderData, array $orderItems): void
    {
        Log::info('Order updated successfully', [
            'order_id' => $order->id,
            'order_number' => $order->order_number,
            'customer_id' => $orderData['customer_id'],
            'user_id' => auth()->id(),
            'total_amount' => $order->total_amount,
            'items_count' => count($orderItems),
        ]);
    }
}