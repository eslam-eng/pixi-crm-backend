<?php

namespace App\Livewire\Reasons;

use App\Models\Reason;
use Livewire\Component;
use App\DTO\Reason\ReasonDTO;
use App\Services\ReasonService;
use App\Http\Requests\Reason\ReasonUpdateRequest;

class EditReasonModal extends Component
{
    public ?int $reasonId = null;
    public string $name = '';

    protected ReasonService $reasonService;

    public function boot(ReasonService $reasonService)
    {
        $this->reasonService = $reasonService;
    }

    protected function rules()
    {
        return (new ReasonUpdateRequest())->rules(); // Centralized validation
    }

    public function loadReason($id)
    {
        $reason = Reason::findOrFail($id);
        $this->reasonId = $reason->id;
        $this->name = $reason->name;

        // Open modal using Livewire dispatch
        $this->dispatch('open-edit-modal');
    }

    public function update()
    {
        $this->validate();

        $reasonDTO = ReasonDTO::fromArray(['name' => $this->name]);
        $this->reasonService->update($reasonDTO, $this->reasonId);

        // Close the modal and refresh list
        $this->dispatch('close-modal', ['message' => 'Reason updated successfully!']);
        $this->dispatch('refreshReasonsList');

        // Reset fields
        $this->reset();
    }

    public function render()
    {
        return view('livewire.reasons.edit-reason-modal');
    }
}
