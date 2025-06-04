<?php

namespace App\Livewire\Customer;

use App\Models\Customer;
use Illuminate\Validation\Rule;
use Livewire\Component;

class CreatePage extends Component
{
    public $name;
    public $email;
    public $phone;
    public $address;

    public function rules(): array
    {
        return [
            'name' => ['required', 'min:2', 'max:100'],
            'email' => ['required', 'email', Rule::unique('customers', 'email'), 'max:100'],
            'phone' => ['required', 'regex:/^([0-9\s\-\+\(\)]*)$/', 'min:10', 'max:15'],
            'address' => ['nullable', 'max:255']
        ];
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function create()
    {
        $validatedData = $this->validate();

        Customer::create($validatedData);

        session()->flash('success', 'Customer created successfully.');

        return $this->redirectRoute('customers.table', navigate: true);
    }

    public function render()
    {
        return view('livewire.customer.create-page');
    }
}
