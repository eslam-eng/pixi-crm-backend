<?php

namespace App\Livewire\Industries;

use App\Models\Industry;
use Livewire\Component;

class CreateIndustrySearchModal extends Component
{
    public $filters = [
        'receiver_id' => '',
        'code' => '',
        'datepicker' => '',
        'start_time' => '',
        'end_time' => '',
        'selected_company' => '',
        'selected_branch' => '',
        'selected_city' => '',
        'selected_area' => '',
        'selected_status' => '',
        'geid' => '',
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

        return view('livewire.industries.create-industry-search-modal');
    }
}
