<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'laundry_service_id',
        'quantity_kg',
        'unit_price',
        'subtotal',
        'notes',
    ];

    protected $casts = [
        'quantity_kg' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function laundryService(): BelongsTo
    {
        return $this->belongsTo(LaundryService::class);
    }

    public function getQuantityAttribute()
    {
        return $this->quantity_kg;
    }

    public function getPricePerKgAttribute()
    {
        return $this->unit_price;
    }
}
