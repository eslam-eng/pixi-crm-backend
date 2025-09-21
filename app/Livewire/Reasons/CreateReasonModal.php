<?php

namespace App\Livewire\Reasons;

use App\Models\Reason;
use Livewire\Component;
use App\DTO\Reason\ReasonDTO;
use App\Services\ReasonService;
use App\Http\Requests\Reason\ReasonStoreRequest;

class CreateReasonModal extends Component
{
    public $name;
    protected ReasonService $reasonService;

    public function boot(ReasonService $reasonService)
    {
        $this->reasonService = $reasonService;
    }

    protected function rules()
    {
        return (new ReasonStoreRequest())->rules(); // Use centralized validation
    }

    public function save()
    {
        $this->validate();
        // Convert array to ReasonDTO using fromArray()
        $reasonDTO = ReasonDTO::fromArray(['name' => $this->name]);

        // Pass DTO to service
        $this->reasonService->store($reasonDTO);

        // Reset form fields
        $this->reset();

        // Close the modal and send success message
        $this->dispatch('close-modal', ['message' => 'New Reason added!']);
    }


    public function render()
    {
        return view('livewire.reasons.create-reason-modal');
    }
}
