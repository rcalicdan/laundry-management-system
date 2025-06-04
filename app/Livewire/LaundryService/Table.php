<?php

namespace App\Livewire\LaundryService;

use App\Models\LaundryService;
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

    #[Url(as: 'price')]
    public $searchPrice = '';

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
        $this->searchPrice = '';
        $this->resetPage();
    }

    public function delete(LaundryService $laundryService)
    {
        $this->authorize('delete', $laundryService);
        $laundryService->delete();
        session()->flash('success', 'Laundry service deleted successfully.');
    }

    public function render()
    {
        $laundryServices = LaundryService::when($this->searchId, function ($query) {
            return $query->where('id', $this->searchId);
        })
            ->when($this->searchName, function ($query) {
                return $query->where('name', 'like', "%$this->searchName%");
            })
            ->when($this->searchPrice, function ($query) {
                return $query->where('price_per_kg', $this->searchPrice);
            })
            ->paginate(20);

        return view('livewire.laundry-service.table', compact('laundryServices'));
    }
}