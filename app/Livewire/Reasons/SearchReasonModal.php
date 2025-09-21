<?php

namespace App\Livewire\Reasons;

use Livewire\Component;
use App\QueryFilters\ReasonFilters;
use App\Models\Reason;

class SearchReasonModal extends Component
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
        return view('livewire.reasons.search-reason-modal');
    }
}
