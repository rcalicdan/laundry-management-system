<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LaundryService extends Model
{
    protected $table = 'laundry_services';
    
    protected $fillable = [
        'name',
        'price_per_kg',
        'estimated_time',
    ];
}