<?php

namespace App\Livewire\LaundryService;

use App\Models\LaundryService;
use Illuminate\Validation\Rule;
use Livewire\Component;

class CreatePage extends Component
{
    public $name;
    public $price_per_kg;
    public $estimated_time;

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:2', 'max:100', Rule::unique('laundry_services','name')],
            'price_per_kg' => ['required', 'numeric', 'min:0', 'max:9999.99'],
            'estimated_time' => ['required', 'integer','min:0','max:9999'],
        ];
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function create()
    {
        $this->authorize('create', LaundryService::class);
        
        $validatedData = $this->validate();

        LaundryService::create($validatedData);

        session()->flash('success', 'Laundry service created successfully.');

        return $this->redirectRoute('laundry-services.table', navigate: true);
    }

    public function render()
    {
        return view('livewire.laundry-service.create-page');
    }
}