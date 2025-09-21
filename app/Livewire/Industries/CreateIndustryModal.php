<?php

namespace App\Livewire\Industries;

use App\Models\Industry;
use Livewire\Component;

class CreateIndustryModal extends Component
{

    public $name;

    protected $rule = [
        'name' => 'required'
    ];

    protected function rules()
    {
        return [
            'name' => 'required|min:3',
        ];
    }

    public function save()
    {
        $this->validate();

        // Save the record to the database
        Industry::create([
            'name' => $this->name,
        ]);

        // Reset form fields
        $this->reset();


        // Close the modal
        $this->dispatch('close-modal', ['message' => 'New Industy added!']);
    }
    public function render()
    {
        return view('livewire.industries.create-industry-modal');
    }
}
