<?php

namespace App\Livewire\Sources;

use Livewire\Component;
use App\DTO\Resource\ResourceDTO;
use App\Services\ResourceService;
use App\Http\Requests\Resource\ResourceStoreRequest;

class CreateSourceModal extends Component
{
    public $name;
    protected ResourceService $ResourceService;

    public function boot(ResourceService $ResourceService)
    {
        $this->ResourceService = $ResourceService;
    }

    protected function rules()
    {
        return (new ResourceStoreRequest())->rules(); // Use centralized validation
    }

    public function save()
    {
        $this->validate();
        // Convert array to ResourceDTO using fromArray()
        $ResourceDTO = ResourceDTO::fromArray(['name' => $this->name]);

        // Pass DTO to service
        $this->ResourceService->store($ResourceDTO);

        // Reset form fields
        $this->reset();

        // Close the modal and send success message
        $this->dispatch('close-modal', ['message' => 'New Resource added!']);
    }
    public function render()
    {
        return view('livewire.sources.create-source-modal');
    }
}
