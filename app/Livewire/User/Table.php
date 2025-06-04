<?php

namespace App\Livewire\User;

use App\Models\User;
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

    #[Url(as: 'hire_date')]
    public $searchHireDate = '';

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
        $this->searchHireDate = '';
        $this->resetPage();
    }

    public function delete(User $user)
    {
        $this->authorize('delete', $user);
        $user->delete();
        session()->flash('success', 'User deleted successfully.');
    }

    public function render()
    {
        $this->authorize('viewAny', User::class);

        $users = User::when($this->searchId, function ($query) {
            return $query->where('id', $this->searchId);
        })
            ->when($this->searchName, function ($query) {
                return $query->where('name', 'like', "%$this->searchName%");
            })
            ->when($this->searchEmail, function ($query) {
                return $query->where('email', 'like', "%$this->searchEmail%");
            })
            ->when($this->searchHireDate, function ($query) {
                return $query->whereDate('created_at', $this->searchHireDate);
            })
            ->paginate(20);

        return view('livewire.user.table', compact('users'));
    }
}