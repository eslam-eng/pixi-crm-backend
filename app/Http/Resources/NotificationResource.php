<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class NotificationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = $this->data ?? [];
        
        return [
            'id' => $this->id,
            'type' => $this->getTypeDisplayName(),
            'title' => $this->getTitle(),
            'message' => $this->getMessage(),
            'is_read' => $this->read_at !== null,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'created_at_human' => $this->created_at->diffForHumans(),
            'action_url' => $this->getActionUrl(),
        ];
    }

    /**
     * Get the display name for the notification type
     */
    private function getTypeDisplayName(): string
    {
        return match ($this->type) {
            'App\\Notifications\\Tenant\\TaskEscalationNotification' => 'Task Escalation',
            'App\\Notifications\\Tenant\\TaskReminderNotification' => 'Task Reminder',
            'App\\Notifications\\Tenant\\TaskAssignmentNotification' => 'Task Assignment',
            'App\\Notifications\\Tenant\\TaskStatusChangeNotification' => 'Task Status Change',
            'App\\Notifications\\Tenant\\DealNotification' => 'Deal Notification',
            'App\\Notifications\\Tenant\\OpportunityNotification' => 'Opportunity Notification',
            'App\\Notifications\\Tenant\\ClientNotification' => 'Client Notification',
            default => 'System Notification'
        };
    }

    /**
     * Get the notification title
     */
    private function getTitle(): string
    {
        $data = $this->data ?? [];
        
        // For task escalation notifications
        if ($this->type === 'App\\Notifications\\Tenant\\TaskEscalationNotification') {
            return trans('app.task_escalation_subject');
        }elseif ($this->type === 'App\\Notifications\\Tenant\\TaskReminderNotification') {
            return trans('app.task_reminder_subject');
        }
        
        // For other notification types, try to get title from data
        return $data['title'] ?? $data['subject'] ?? $this->getTypeDisplayName();
    }

    /**
     * Get the notification message
     */
    private function getMessage(): string
    {
        $data = $this->data ?? [];
        
        // For task escalation notifications
        if ($this->type === 'App\\Notifications\\Tenant\\TaskEscalationNotification' || $this->type === 'App\\Notifications\\Tenant\\TaskReminderNotification') {
            $locale = app()->getLocale();
            if (isset($data['messages']) && is_array($data['messages'])) {
                if (!empty($data['messages'][$locale])) {
                    return (string) $data['messages'][$locale];
                }
                if (!empty($data['messages']['en'])) {
                    return (string) $data['messages']['en'];
                }
                $firstMessage = reset($data['messages']);
                if (is_string($firstMessage) && $firstMessage !== '') {
                    return $firstMessage;
                }
            }
            return $data['message'] ?? 'A task requires your immediate attention.';
        }
        
        // For other notification types
        return $data['message'] ?? $data['body'] ?? $data['text'] ?? 'You have a new notification.';
    }

    /**
     * Get the action URL for the notification
     */
    private function getActionUrl(): ?string
    {
        $data = $this->data ?? [];
        
        // For task escalation notifications
        if ($this->type === 'App\\Notifications\\Tenant\\TaskEscalationNotification') {
            return $data['action_url'] ?? '/tasks/' . ($data['task_id'] ?? '');
        }
        
        // For other notification types
        return $data['action_url'] ?? $data['url'] ?? null;
    }

    /**
     * Get the icon for the notification
     */
    private function getIcon(): string
    {
        return match ($this->type) {
            'App\\Notifications\\Tenant\\TaskEscalationNotification' => 'exclamation-triangle',
            'App\\Notifications\\Tenant\\TaskReminderNotification' => 'clock',
            'App\\Notifications\\Tenant\\TaskAssignmentNotification' => 'user-plus',
            'App\\Notifications\\Tenant\\TaskStatusChangeNotification' => 'check-circle',
            'App\\Notifications\\Tenant\\DealNotification' => 'handshake',
            'App\\Notifications\\Tenant\\OpportunityNotification' => 'trending-up',
            'App\\Notifications\\Tenant\\ClientNotification' => 'users',
            default => 'bell'
        };
    }

    /**
     * Get the priority level for the notification
     */
    private function getPriority(): string
    {
        return match ($this->type) {
            'App\\Notifications\\Tenant\\TaskEscalationNotification' => 'high',
            'App\\Notifications\\Tenant\\TaskReminderNotification' => 'medium',
            'App\\Notifications\\Tenant\\TaskAssignmentNotification' => 'medium',
            'App\\Notifications\\Tenant\\TaskStatusChangeNotification' => 'low',
            'App\\Notifications\\Tenant\\DealNotification' => 'high',
            'App\\Notifications\\Tenant\\OpportunityNotification' => 'high',
            'App\\Notifications\\Tenant\\ClientNotification' => 'medium',
            default => 'medium'
        };
    }

    /**
     * Get the category for the notification
     */
    private function getCategory(): string
    {
        return match ($this->type) {
            'App\\Notifications\\Tenant\\TaskEscalationNotification',
            'App\\Notifications\\Tenant\\TaskReminderNotification',
            'App\\Notifications\\Tenant\\TaskAssignmentNotification',
            'App\\Notifications\\Tenant\\TaskStatusChangeNotification' => 'tasks',
            'App\\Notifications\\Tenant\\DealNotification' => 'deals',
            'App\\Notifications\\Tenant\\OpportunityNotification' => 'opportunities',
            'App\\Notifications\\Tenant\\ClientNotification' => 'clients',
            default => 'system'
        };
    }


}
