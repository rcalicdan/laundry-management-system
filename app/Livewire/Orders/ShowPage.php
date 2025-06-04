<?php

namespace App\Livewire\Orders;

use App\Models\Order;
use Livewire\Component;

class ShowPage extends Component
{
    public Order $order;

    public function mount(Order $order)
    {
        $this->order = $order->load(['customer', 'user', 'orderItems.laundryService', 'payment']);
    }

    public function render()
    {
        return view('livewire.orders.show-page');
    }
}