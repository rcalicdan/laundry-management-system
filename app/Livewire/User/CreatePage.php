<?php

namespace App\Livewire\User;

use App\Models\User;
use Illuminate\Validation\Rule;
use Livewire\Component;

class CreatePage extends Component
{
    public $name;
    public $email;
    public $password;
    public $password_confirmation;

    public function rules(): array
    {
        return [
            'name' => ['required', 'min:2', 'max:50'],
            'email' => ['required', 'email', Rule::unique('users', 'email'), 'max:50'],
            'password' => ['required', 'min:8', 'confirmed'],
            'password_confirmation' => ['required']
        ];
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function create()
    {
        $validatedData = $this->validate();

        User::create($validatedData);

        session()->flash('success', 'User created successfully.');

        return $this->redirectRoute('users.table', navigate: true);
    }

    public function render()
    {
        return view('livewire.user.create-page');
    }
}
