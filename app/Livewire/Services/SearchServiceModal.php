<?php

namespace App\Livewire\Services;

use Livewire\Component;

class SearchServiceModal extends Component
{
    public $filters = [
        'start_date' => null,
        'end_date' => null,
        'name' => null,
        'min_price' => null,
        'max_price' => null,
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
        return view('livewire.services.search-service-modal');
    }
}
