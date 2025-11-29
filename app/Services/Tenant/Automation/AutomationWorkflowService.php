<?php

namespace App\Services\Tenant\Automation;

use App\DTO\Automation\AutomationWorkflowDTO;
use App\Enums\AutomationActionsEnum;
use App\Models\Tenant\AutomationWorkflow;
use App\Models\Tenant\AutomationWorkflowStep;
use App\Models\Tenant\AutomationWorkflowStepCondition;
use App\Models\Tenant\AutomationWorkflowStepAction;
use App\Models\Tenant\AutomationWorkflowStepDelay;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Html\Options\Languages\Paginate;

class AutomationWorkflowService
{
    /**
     * Create a new automation workflow with steps
     */
    public function create(AutomationWorkflowDTO $dto): AutomationWorkflow
    {
        return DB::transaction(function () use ($dto) {
            // Create the workflow
            $workflow = AutomationWorkflow::create([
                'name' => $dto->name,
                'description' => $dto->description,
                'automation_trigger_id' => $dto->automation_trigger_id,
                'is_active' => true,
            ]);

            // Create steps
            foreach ($dto->steps as $stepData) {
                $this->createStep($workflow, $stepData);
            }

            return $workflow->load(['automationTrigger', 'steps.condition', 'steps.action', 'steps.delay']);
        });
    }

    /**
     * Create a workflow step
     */
    private function createStep(AutomationWorkflow $workflow, array $stepData): AutomationWorkflowStep
    {

        // Create the step record
        $step = AutomationWorkflowStep::create([
            'automation_workflow_id' => $workflow->id,
            'type' => $stepData['type'],
            'order' => $stepData['order'],
        ]);

        // Create the specific step type
        $this->createStepTarget($stepData, $step->id);

        return $step;
    }

    /**
     * Create the specific step target based on type
     */
    private function createStepTarget(array $stepData, int $stepId): void
    {
        switch ($stepData['type']) {
            case 'condition':
                AutomationWorkflowStepCondition::create([
                    'automation_workflow_step_id' => $stepId,
                    'field' => $stepData['field'],
                    'operation' => $stepData['operation'],
                    'value' => $stepData['value'],
                ]);
                break;

            case 'action':
                $configs = null;
                if ($stepData['automation_action_id'] == AutomationActionsEnum::ASSIGN_TO_SALES->value) {
                    $configs = [
                        'assign_strategy' => $stepData['assign_strategy'] ?? null,
                        'assign_user_id' => $stepData['assign_user_id'] ?? null,
                    ];
                }else if ($stepData['automation_action_id'] == AutomationActionsEnum::NOTIFY_OWNER->value) {
                    $configs = [
                        'message' => $stepData['message'] ?? null,
                    ];
                }else if ($stepData['automation_action_id'] == AutomationActionsEnum::NOTIFY_MANAGER->value) {
                    $configs = [
                        'message' => $stepData['message'] ?? null,
                    ];
                }else if ($stepData['automation_action_id'] == AutomationActionsEnum::NOTIFY_ADMIN->value) {
                    $configs = [
                        'message' => $stepData['message'] ?? null,
                    ];
                }

                AutomationWorkflowStepAction::create([
                    'automation_workflow_step_id' => $stepId,
                    'automation_action_id' => $stepData['automation_action_id'],
                    'configs' => $configs,
                ]);
                break;

            case 'delay':
                AutomationWorkflowStepDelay::create([
                    'automation_workflow_step_id' => $stepId,
                    'duration' => $stepData['duration'],
                    'unit' => $stepData['unit'],
                ]);
                break;

            default:
                throw new \InvalidArgumentException("Invalid step type: {$stepData['type']}");
        }
    }


    /**
     * Get all automation workflows
     */
    public function getAll()
    {
        return AutomationWorkflow::with(['automationTrigger', 'steps.condition', 'steps.action', 'steps.delay'])
            ->active()
            ->orderBy('created_at', 'desc')
            ->paginate(per_page());
    }

    /**
     * Get automation workflow by ID
     */
    public function getById(int $id): ?AutomationWorkflow
    {
        return AutomationWorkflow::with(['automationTrigger', 'steps.condition', 'steps.action', 'steps.delay'])
            ->find($id);
    }

    /**
     * Update automation workflow
     */
    public function update(int $id, AutomationWorkflowDTO $dto): ?AutomationWorkflow
    {
        $workflow = AutomationWorkflow::find($id);

        if (!$workflow) {
            return null;
        }

        return DB::transaction(function () use ($workflow, $dto) {
            // Update workflow
            $workflow->update([
                'name' => $dto->name,
                'description' => $dto->description,
                'automation_trigger_id' => $dto->automation_trigger_id,
            ]);

            // Delete existing steps
            $workflow->steps()->delete();

            // Create new steps
            foreach ($dto->steps as $stepData) {
                $this->createStep($workflow, $stepData);
            }

            return $workflow->load(['automationTrigger', 'steps.condition', 'steps.action', 'steps.delay']);
        });
    }

    /**
     * Delete automation workflow
     */
    public function delete(int $id): bool
    {
        $workflow = AutomationWorkflow::find($id);

        if (!$workflow) {
            return false;
        }

        return $workflow->delete();
    }

    /**
     * Toggle workflow active status
     */
    public function toggleActive(int $id): bool
    {
        $workflow = AutomationWorkflow::find($id);

        if (!$workflow) {
            return false;
        }

        return $workflow->update(['is_active' => !$workflow->is_active]);
    }
}
