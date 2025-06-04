<?php

namespace App\Services;

use App\Models\LaundryService;
use Illuminate\Support\Collection;

class OrderCalculatorService
{
    private const EXPRESS_SURCHARGE_PERCENTAGE = 15;

    public function calculateOrderItemSubtotals(array $orderItems, Collection $laundryServices, bool $isExpress = false): array
    {
        foreach ($orderItems as $index => &$item) {
            $subtotal = $this->calculateItemSubtotal($item, $laundryServices, $isExpress);
            $item['subtotal'] = $subtotal;
        }

        return $orderItems;
    }

    public function calculateTotalAmount(array $orderItems): float
    {
        return collect($orderItems)->sum('subtotal');
    }

    private function calculateItemSubtotal(array &$item, Collection $laundryServices, bool $isExpress = false): float
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

        $basePrice = $service->price_per_kg;

        if ($isExpress) {
            $basePrice = $this->applyExpressSurcharge($basePrice);
        }

        $item['price_per_kg'] = $basePrice;

        return $basePrice * $item['quantity'];
    }

    private function applyExpressSurcharge(float $basePrice): float
    {
        return $basePrice * (1 + self::EXPRESS_SURCHARGE_PERCENTAGE / 100);
    }
}
