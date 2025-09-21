<?php

namespace App\Livewire\CustomFields;

use Livewire\Component;

class SearchCustomFieldModal extends Component
{
    public $filters = [
        'start_date' => null,
        'end_date' => null,
        'name' => null,
        'type' => null,
    ];



    public function search()
    {
        $this->dispatch('refreshDatatable', $this->filters);
    }
    public function resetfields()
    {
        $this->reset();
    }
    public function render()
    {
        return view('livewire.custom-fields.search-custom-field-modal');
    }
}
