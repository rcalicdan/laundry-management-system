<?php

namespace App\Livewire\Customer;

use App\Models\Customer;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;

class Table extends Component
{
    use WithPagination;

    #[Url(as: 'id')]
    public $searchId = '';

    #[Url(as: 'name')]
    public $searchName = '';

    #[Url(as: 'email')]
    public $searchEmail = '';

    #[Url(as: 'phone')]
    public $searchPhone = '';

    #[Url(as: 'created_date')]
    public $searchCreatedDate = '';

    public $isSearchModalOpen = false;

    public function performSearch()
    {
        $this->resetPage();
        $this->dispatch('search-completed');
    }

    public function clearSearch()
    {
        $this->searchId = '';
        $this->searchName = '';
        $this->searchEmail = '';
        $this->searchPhone = '';
        $this->searchCreatedDate = '';
        $this->resetPage();
    }

    public function delete(Customer $customer)
    {
        $this->authorize('delete', $customer);
        $customer->delete();
        session()->flash('success', 'Customer deleted successfully.');
    }

    public function render()
    {
        $customers = Customer::when($this->searchId, function ($query) {
            return $query->where('id', $this->searchId);
        })
            ->when($this->searchName, function ($query) {
                return $query->where('name', 'like', "%$this->searchName%");
            })
            ->when($this->searchEmail, function ($query) {
                return $query->where('email', 'like', "%$this->searchEmail%");
            })
            ->when($this->searchPhone, function ($query) {
                return $query->where('phone', 'like', "%$this->searchPhone%");
            })
            ->when($this->searchCreatedDate, function ($query) {
                return $query->whereDate('created_at', $this->searchCreatedDate);
            })
            ->paginate(20);

        return view('livewire.customer.table', compact('customers'));
    }
}
