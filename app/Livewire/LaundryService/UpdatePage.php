<?php

namespace App\Livewire\LaundryService;

use App\Models\LaundryService;
use Illuminate\Validation\Rule;
use Livewire\Component;

class UpdatePage extends Component
{
    public LaundryService $laundryService;
    public $name = '';
    public $price_per_kg = '';

    public function mount(LaundryService $laundryService)
    {
        $this->authorize('update', $laundryService);
        $this->laundryService = $laundryService;
        $this->name = $laundryService->name;
        $this->price_per_kg = $laundryService->price_per_kg;
    }

    public function rules()
    {
        return [
            'name' => ['required', 'string', 'min:2', 'max:100', Rule::unique('laundry_services','name')->ignore($this->laundryService->id)],
            'price_per_kg' => ['required', 'numeric', 'min:0', 'max:9999.99'],
        ];
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function update()
    {
        $this->authorize('update', $this->laundryService);
        
        $validatedData = $this->validate();

        $this->laundryService->update($validatedData);
        
        session()->flash('success', 'Laundry service updated successfully!');
    }

    public function render()
    {
        return view('livewire.laundry-service.update-page');
    }
}