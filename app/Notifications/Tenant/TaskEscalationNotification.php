<?php

namespace App\Notifications\Tenant;

use App\Models\Tenant\Task;
use App\Settings\NotificationSettings;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TaskEscalationNotification extends Notification
{
    use Queueable;

    public $task;


    /**
     * Create a new notification instance.
     */
    public function __construct($task)
    {
        $this->task = $task;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        $settings = new NotificationSettings();
        $channels = [];
        if ($settings->mail_notification) {
            $channels[] = 'mail';
        }
        if ($settings->system_notification) {
            $channels[] = 'database';
        }
        return $channels;
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        // Build due datetime safely: due_date may be cast to Carbon (midnight time)
        // and due_time is a time string; avoid concatenation that can double-specify time
        $date = $this->task->due_date instanceof Carbon
            ? $this->task->due_date->copy()
            : Carbon::parse($this->task->due_date);
        $dueDateTime = (clone $date)->setTimeFromTimeString((string) $this->task->due_time);
        $hoursOverdue = Carbon::now()->diffInHours($dueDateTime);
        
        $assignedTo = $this->task->assignedTo ? 
            $this->task->assignedTo->first_name . ' ' . $this->task->assignedTo->last_name : 
            __('app.task_escalation_not_assigned');
        
        // Store multilingual email content for future use
        $emailContent = [
            'en' => [
                'subject' => 'Task Escalation: ' . $this->task->title,
                'greeting' => 'Hello ' . $notifiable->first_name . ',',
                'intro' => 'This is an escalation notification for a task that is overdue.',
                'details' => '**Task Details:**',
                'title' => '• **Title:** ' . $this->task->title,
                'description' => '• **Description:** ' . $this->task->description,
                'due_date' => '• **Due Date:** ' . $dueDateTime->format('M d, Y \a\t g:i A'),
                'hours_overdue' => '• **Hours Overdue:** ' . $hoursOverdue . ' hours',
                'priority' => '• **Priority:** ' . ($this->task->priority->name ?? 'Not specified'),
                'assigned_to' => '• **Assigned To:** ' . $assignedTo,
                'action_required' => 'Please take immediate action to complete this task.',
                'view_task' => 'View Task',
                'thanks' => 'Thank you for your attention to this matter.'
            ],
            'es' => [
                'subject' => 'Escalación de Tarea: ' . $this->task->title,
                'greeting' => 'Hola ' . $notifiable->first_name . ',',
                'intro' => 'Esta es una notificación de escalación para una tarea vencida.',
                'details' => '**Detalles de la Tarea:**',
                'title' => '• **Título:** ' . $this->task->title,
                'description' => '• **Descripción:** ' . $this->task->description,
                'due_date' => '• **Fecha de Vencimiento:** ' . $dueDateTime->format('M d, Y \a\t g:i A'),
                'hours_overdue' => '• **Horas de Retraso:** ' . $hoursOverdue . ' horas',
                'priority' => '• **Prioridad:** ' . ($this->task->priority->name ?? 'No especificado'),
                'assigned_to' => '• **Asignado a:** ' . $assignedTo,
                'action_required' => 'Por favor tome acción inmediata para completar esta tarea.',
                'view_task' => 'Ver Tarea',
                'thanks' => 'Gracias por su atención a este asunto.'
            ],
            'fr' => [
                'subject' => 'Escalade de Tâche: ' . $this->task->title,
                'greeting' => 'Bonjour ' . $notifiable->first_name . ',',
                'intro' => 'Ceci est une notification d\'escalade pour une tâche en retard.',
                'details' => '**Détails de la Tâche:**',
                'title' => '• **Titre:** ' . $this->task->title,
                'description' => '• **Description:** ' . $this->task->description,
                'due_date' => '• **Date d\'échéance:** ' . $dueDateTime->format('M d, Y \a\t g:i A'),
                'hours_overdue' => '• **Heures de retard:** ' . $hoursOverdue . ' heures',
                'priority' => '• **Priorité:** ' . ($this->task->priority->name ?? 'Non spécifié'),
                'assigned_to' => '• **Assigné à:** ' . $assignedTo,
                'action_required' => 'Veuillez prendre des mesures immédiates pour terminer cette tâche.',
                'view_task' => 'Voir la Tâche',
                'thanks' => 'Merci de votre attention à ce sujet.'
            ],
            'ar' => [
                'subject' => 'تصعيد المهمة: ' . $this->task->title,
                'greeting' => 'مرحباً ' . $notifiable->first_name . '،',
                'intro' => 'هذا إشعار تصعيد لمهمة متأخرة.',
                'details' => '**تفاصيل المهمة:**',
                'title' => '• **العنوان:** ' . $this->task->title,
                'description' => '• **الوصف:** ' . $this->task->description,
                'due_date' => '• **تاريخ الاستحقاق:** ' . $dueDateTime->format('M d, Y \a\t g:i A'),
                'hours_overdue' => '• **ساعات التأخير:** ' . $hoursOverdue . ' ساعة',
                'priority' => '• **الأولوية:** ' . ($this->task->priority->name ?? 'غير محدد'),
                'assigned_to' => '• **المكلف:** ' . $assignedTo,
                'action_required' => 'يرجى اتخاذ إجراء فوري لإكمال هذه المهمة.',
                'view_task' => 'عرض المهمة',
                'thanks' => 'شكراً لاهتمامك بهذا الأمر.'
            ]
        ];
        
        return (new MailMessage)
                    ->subject(__('app.task_escalation_subject', ['title' => $this->task->title]))
                    ->greeting(__('app.task_escalation_greeting', ['name' => $notifiable->first_name]))
                    ->line(__('app.task_escalation_intro'))
                    ->line(__('app.task_escalation_details'))
                    ->line(__('app.task_escalation_title', ['title' => $this->task->title]))
                    ->line(__('app.task_escalation_description', ['description' => $this->task->description]))
                    ->line(__('app.task_escalation_due_date', ['due_date' => $dueDateTime->format('M d, Y \a\t g:i A')]))
                    ->line(__('app.task_escalation_hours_overdue', ['hours' => $hoursOverdue]))
                    ->line(__('app.task_escalation_priority', ['priority' => $this->task->priority->name ?? __('app.task_escalation_not_specified')]))
                    ->line(__('app.task_escalation_assigned_to', ['assigned_to' => $assignedTo]))
                    ->line(__('app.task_escalation_action_required'))
                    ->action(__('app.task_escalation_view_task'), url('/tasks/' . $this->task->id))
                    ->line(__('app.task_escalation_thanks'))
                    ->with('multilingual_content', $emailContent); // Store all 4 languages for future use
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $date = $this->task->due_date instanceof Carbon
            ? $this->task->due_date->copy()
            : Carbon::parse($this->task->due_date);
        $dueDateTime = (clone $date)->setTimeFromTimeString((string) $this->task->due_time);
        $hoursOverdue = intval(Carbon::now()->floatDiffInHours($dueDateTime));
        
        // Store messages in all 4 languages for future use
        $messages = [
            'en' => 'Task (:title) is :hours hours overdue and requires immediate attention.',
            'es' => 'La tarea (:title) tiene :hours horas de retraso y requiere atención inmediata.',
            'fr' => 'La tâche (:title) est en retard de :hours heures et nécessite une attention immédiate.',
            'ar' => 'المهمة (:title) متأخرة :hours ساعة وتتطلب اهتماماً فورياً.'
        ];
        
        // Replace placeholders in each language
        foreach ($messages as $lang => $message) {
            $messages[$lang] = str_replace([':title', ':hours'], [$this->task->title, $hoursOverdue], $message);
        }
        
        return [
            'task_title' => $this->task->title,
            'type' => 'task_escalation',
            'messages' => $messages, // Store all 4 languages
            'target_model' => Task::class,
            'target_id' => $this->task->id,
            'action_url' => '/tasks/' . $this->task->id,
        ];
    }
}
