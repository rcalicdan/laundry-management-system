<?php

namespace App\Livewire\Orders;

use App\Models\Order;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;

class Table extends Component
{
    use WithPagination;

    #[Url(as: 'id')]
    public $searchId = '';

    #[Url(as: 'customer')]
    public $searchCustomer = '';

    #[Url(as: 'status')]
    public $searchStatus = '';

    #[Url(as: 'date')]
    public $searchDate = '';
    public $isSearchModalOpen = false;

    public function performSearch()
    {
        $this->resetPage();
        $this->dispatch('search-completed');
    }

    public function clearSearch()
    {
        $this->searchId = '';
        $this->searchCustomer = '';
        $this->searchStatus = '';
        $this->searchDate = '';
        $this->resetPage();
    }

    public function delete(Order $order)
    {
        $this->authorize('delete', $order);
        $order->delete();
        session()->flash('success', 'Order deleted successfully.');
    }

    public function render()
    {
        $orders = Order::with(['customer', 'orderItems.laundryService'])
            ->when($this->searchId, function ($query) {
                return $query->where('id', $this->searchId);
            })
            ->when($this->searchCustomer, function ($query) {
                return $query->whereHas('customer', function ($q) {
                    $q->where('name', 'like', "%{$this->searchCustomer}%");
                });
            })
            ->when($this->searchStatus, function ($query) {
                return $query->where('status', $this->searchStatus);
            })
            ->when($this->searchDate, function ($query) {
                return $query->whereDate('created_at', $this->searchDate);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('livewire.orders.table', compact('orders'));
    }
}
