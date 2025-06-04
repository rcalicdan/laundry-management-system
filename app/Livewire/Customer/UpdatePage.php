<?php

namespace App\Livewire\Customer;

use App\Models\Customer;
use Livewire\Component;
use Illuminate\Validation\Rule;

class UpdatePage extends Component
{
    public Customer $customer;
    public $name = '';
    public $email = '';
    public $phone = '';
    public $address = '';

    public function mount(Customer $customer)
    {
        $this->authorize('update', $customer);
        $this->customer = $customer;
        $this->name = $customer->name;
        $this->email = $customer->email;
        $this->phone = $customer->phone;
        $this->address = $customer->address;
    }

    public function rules()
    {
        return [
            'name' => 'required|string|min:2|max:100',
            'email' => [
                'required',
                'email',
                'max:100',
                Rule::unique('customers')->ignore($this->customer->id)
            ],
            'phone' => ['required', 'regex:/^([0-9\s\-\+\(\)]*)$/', 'min:10', 'max:15'],
            'address' => 'nullable|string|max:255'
        ];
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function update()
    {
        $this->authorize('update', $this->customer);
        $validatedData = $this->validate();

        $this->customer->update($validatedData);
        session()->flash('success', 'Customer updated successfully!');
    }

    public function render()
    {
        return view('livewire.customer.update-page');
    }
}
