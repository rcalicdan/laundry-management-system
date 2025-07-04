<?php

namespace App\Livewire\User;

use App\Models\User;
use App\Enums\UserRoles;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UpdatePage extends Component
{
    public User $user;
    public $name = '';
    public $email = '';
    public $password = '';
    public $password_confirmation = '';
    public $role = '';

    public function mount(User $user)
    {
        $this->authorize('update', $user);
        $this->user = $user;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->role = $user->role;
    }

    public function rules()
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users')->ignore($this->user->id)
            ]
        ];

        if ($this->canChangeRole()) {
            $rules['role'] = ['required', Rule::in($this->getAvailableRoles())];
        }

        if (!empty($this->password)) {
            $rules['password'] = ['required', 'min:8', Password::defaults()];
            $rules['password_confirmation'] = ['required','same:password'];
        }

        return $rules;
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function update()
    {
        $this->authorize('update', $this->user);
        $this->validate();
        
        $updateData = [
            'name' => $this->name,
            'email' => $this->email,
        ];

        if ($this->canChangeRole()) {
            $updateData['role'] = $this->role;
        }

        if (!empty($this->password)) {
            $updateData['password'] = $this->password;
        }

        $this->user->update($updateData);
        session()->flash('success', 'User updated successfully!');
    }

    public function canChangeRole(): bool
    {
        $currentUser = Auth::user();
        
        if ($currentUser->id === $this->user->id) {
            return false;
        }
        
        if ($currentUser->isAdmin()) {
            return true;
        }
        
        if ($currentUser->isManager() && !$this->user->isAdmin()) {
            return true;
        }
        
        return false;
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
        return view('livewire.user.update-page', [
            'availableRoles' => $this->getAvailableRoles(),
            'canChangeRole' => $this->canChangeRole()
        ]);
    }
}