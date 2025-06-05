<?php

namespace App\Livewire\Customer;

use App\Models\Customer;
use App\Models\Order;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;

class OrdersTable extends Component
{
    use WithPagination;

    public Customer $customer;

    #[Url(as: 'id')]
    public $searchId = '';

    #[Url(as: 'status')]
    public $searchStatus = '';

    #[Url(as: 'date')]
    public $searchDate = '';

    public $isSearchModalOpen = false;

    public function mount(Customer $customer)
    {
        $this->customer = $customer;
    }

    public function performSearch()
    {
        $this->resetPage();
        $this->dispatch('search-completed');
    }

    public function clearSearch()
    {
        $this->searchId = '';
        $this->searchStatus = '';
        $this->searchDate = '';
        $this->resetPage();
    }

    public function render()
    {
        $orders = $this->customer->orders()
            ->with(['orderItems.laundryService', 'payment'])
            ->when($this->searchId, function ($query) {
                return $query->where('id', $this->searchId);
            })
            ->when($this->searchStatus, function ($query) {
                return $query->where('status', $this->searchStatus);
            })
            ->when($this->searchDate, function ($query) {
                return $query->whereDate('created_at', $this->searchDate);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('livewire.customer.orders-table', compact('orders'));
    }
}
