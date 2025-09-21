<?php

namespace App\Livewire\Sources;

use Livewire\Component;

class SearchSourceModal extends Component
{
    public $filters = [
        'start_date' => null,
        'end_date' => null,
        'name' => null,
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
        return view('livewire.sources.search-source-modal');
    }
}
