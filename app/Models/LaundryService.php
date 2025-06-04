<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LaundryService extends Model
{
    protected $table = 'laundry_services';

    protected $fillable = [
        'name',
        'price_per_kg',
        'estimated_time',
    ];

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}
