<?php

namespace App\DTO\Automation;

use App\DTO\BaseDTO;
use Illuminate\Http\Request;

class AutomationWorkflowDTO extends BaseDTO
{
    public function __construct(
        public string $name,
        public ?string $description,
        public int $automation_trigger_id,
        public array $steps = []
    ) {}

    public static function fromRequest(Request $request): self
    {
        return self::fromArray($request->validated());
    }

    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'],
            description: $data['description'] ?? null,
            automation_trigger_id: $data['automation_trigger_id'],
            steps: $data['steps'] ?? []
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'description' => $this->description,
            'automation_trigger_id' => $this->automation_trigger_id,
            'steps' => $this->steps,
        ];
    }

    public function validate(): array
    {
        $errors = [];

        if (empty($this->name)) {
            $errors['name'] = 'Name is required';
        }

        if (empty($this->automation_trigger_id)) {
            $errors['automation_trigger_id'] = 'Automation trigger ID is required';
        }

        // Validate steps
        foreach ($this->steps as $index => $step) {
            $stepErrors = $this->validateStep($step, $index);
            if (!empty($stepErrors)) {
                $errors["steps.{$index}"] = $stepErrors;
            }
        }

        return $errors;
    }

    private function validateStep(array $step, int $index): array
    {
        $errors = [];

        if (!isset($step['type']) || !in_array($step['type'], ['condition', 'action', 'delay'])) {
            $errors['type'] = 'Step type must be condition, action, or delay';
        }

        if (!isset($step['order']) || !is_numeric($step['order'])) {
            $errors['order'] = 'Step order must be a number';
        }

        // Validate based on step type
        switch ($step['type'] ?? '') {
            case 'condition':
                if (!isset($step['field']) || empty($step['field'])) {
                    $errors['field'] = 'Field is required for condition step';
                }
                if (!isset($step['operation']) || empty($step['operation'])) {
                    $errors['operation'] = 'Operation is required for condition step';
                }
                if (!isset($step['value'])) {
                    $errors['value'] = 'Value is required for condition step';
                }
                break;

            case 'action':
                if (!isset($step['automation_action_id']) || empty($step['automation_action_id'])) {
                    $errors['automation_action_id'] = 'Automation action ID is required for action step';
                }
                break;

            case 'delay':
                if (!isset($step['duration']) || !is_numeric($step['duration'])) {
                    $errors['duration'] = 'Duration must be a number for delay step';
                }
                if (!isset($step['unit']) || empty($step['unit'])) {
                    $errors['unit'] = 'Unit is required for delay step';
                }
                break;
        }

        return $errors;
    }
}
