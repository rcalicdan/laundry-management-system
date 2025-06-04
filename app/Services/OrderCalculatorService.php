<?php

namespace App\Services;

use App\Models\LaundryService;
use Illuminate\Support\Collection;

class OrderCalculatorService
{
    public function calculateOrderItemSubtotals(array $orderItems, Collection $laundryServices): array
    {
        foreach ($orderItems as $index => &$item) {
            $subtotal = $this->calculateItemSubtotal($item, $laundryServices);
            $item['subtotal'] = $subtotal;
        }
        
        return $orderItems;
    }

    public function calculateTotalAmount(array $orderItems): float
    {
        return collect($orderItems)->sum('subtotal');
    }

    private function calculateItemSubtotal(array &$item, Collection $laundryServices): float
    {
        if (empty($item['laundry_service_id']) || empty($item['quantity'])) {
            $item['price_per_kg'] = 0;
            return 0;
        }

        $service = $laundryServices->find($item['laundry_service_id']);
        if (!$service) {
            $item['price_per_kg'] = 0;
            return 0;
        }

        $item['price_per_kg'] = $service->price_per_kg;
        
        return $service->price_per_kg * $item['quantity'];
    }
}