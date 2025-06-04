<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    protected $fillable = [
        'name',
        'phone',
        'email',
        'address'
    ];

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}
