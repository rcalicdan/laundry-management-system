<?php

namespace App\Livewire\User;

use App\Models\User;
use App\Enums\UserRoles;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Livewire\Component;

class CreatePage extends Component
{
    public $name;
    public $email;
    public $password;
    public $password_confirmation;
    public $role;

    public function mount()
    {
        $this->authorize('create', User::class);
        $this->role = Auth::user()->isAdmin() ? UserRoles::EMPLOYEE->value : UserRoles::EMPLOYEE->value;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'min:2', 'max:50'],
            'email' => ['required', 'email', Rule::unique('users', 'email'), 'max:50'],
            'password' => ['required', 'min:8', Password::defaults()],
            'password_confirmation' => ['required', 'same:password'],
            'role' => ['required', Rule::in($this->getAvailableRoles())]
        ];
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function create()
    {
        $this->authorize('create', User::class);
        $validatedData = $this->validate();

        User::create($validatedData);

        session()->flash('success', 'User created successfully.');

        return $this->redirectRoute('users.table', navigate: true);
    }

    public function getAvailableRoles(): array
    {
        $currentUser = Auth::user();
        
        if ($currentUser->isAdmin()) {
            return [UserRoles::EMPLOYEE->value, UserRoles::MANAGER->value];
        } elseif ($currentUser->isManager()) {
            return [UserRoles::EMPLOYEE->value];
        }
        
        return [];
    }

    public function render()
    {
        return view('livewire.user.create-page', [
            'availableRoles' => $this->getAvailableRoles()
        ]);
    }
}