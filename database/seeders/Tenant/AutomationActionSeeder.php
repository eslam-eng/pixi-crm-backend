<?php

namespace Database\Seeders\Tenant;

use App\Models\Tenant\AutomationAction;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AutomationActionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $actions = [
            [
                'key' => 'assign_contact',
                'icon' => 'user',
                'name' => [
                    'ar' => 'تعيين جهة اتصال',
                    'en' => 'Assign Contact',
                    'fr' => 'Assigner un contact',
                    'es' => 'Asignar contacto'
                ],
                'description' => 'Assign to sales rep/team based on criteria'
            ],
            [
                'key' => 'notify_owner',
                'icon' => 'bell',
                'name' => [
                    'ar' => 'إشعار المالك',
                    'en' => 'Notify Owner',
                    'fr' => 'Notifier le propriétaire',
                    'es' => 'Notificar al propietario'
                ],
                'description' => 'Send internal notification to owner'
            ],
            [
                'key' => 'send_welcome_email',
                'icon' => 'mail',
                'name' => [
                    'ar' => 'إرسال بريد ترحيبي',
                    'en' => 'Send Welcome Email',
                    'fr' => 'Envoyer un email de bienvenue',
                    'es' => 'Enviar correo de bienvenida'
                ],
                'description' => 'Send onboarding/intro email automatically'
            ],
            [
                'key' => 'assign_to_team',
                'icon' => 'users',
                'name' => [
                    'ar' => 'تعيين للفريق',
                    'en' => 'Assign to Team',
                    'fr' => 'Assigner à l\'équipe',
                    'es' => 'Asignar al equipo'
                ],
                'description' => 'Route contact to Key Accounts/Partners team'
            ],
            [
                'key' => 'assign_opportunity',
                'icon' => 'target',
                'name' => [
                    'ar' => 'تعيين فرصة',
                    'en' => 'Assign Opportunity',
                    'fr' => 'Assigner une opportunité',
                    'es' => 'Asignar oportunidad'
                ],
                'description' => 'Distribute to sales automatically'
            ],
            [
                'key' => 'send_email',
                'icon' => 'mail',
                'name' => [
                    'ar' => 'إرسال بريد إلكتروني',
                    'en' => 'Send Email',
                    'fr' => 'Envoyer un email',
                    'es' => 'Enviar correo electrónico'
                ],
                'description' => 'Follow-up/proposal email'
            ],
            [
                'key' => 'escalate',
                'icon' => 'arrow-up',
                'name' => [
                    'ar' => 'تصعيد',
                    'en' => 'Escalate',
                    'fr' => 'Escalader',
                    'es' => 'Escalar'
                ],
                'description' => 'Notify manager or reassign'
            ],
            [
                'key' => 'notify_manager',
                'icon' => 'alert-triangle',
                'name' => [
                    'ar' => 'إشعار المدير',
                    'en' => 'Notify Manager',
                    'fr' => 'Notifier le manager',
                    'es' => 'Notificar al gerente'
                ],
                'description' => 'High-priority internal alert'
            ],
            [
                'key' => 'create_onboarding_task',
                'icon' => 'check-square',
                'name' => [
                    'ar' => 'إنشاء مهمة إعداد',
                    'en' => 'Create Onboarding Task',
                    'fr' => 'Créer une tâche d\'intégration',
                    'es' => 'Crear tarea de incorporación'
                ],
                'description' => 'Kick off handover to Success/Implementation team'
            ],
            [
                'key' => 'tag_and_reopen_later',
                'icon' => 'tag',
                'name' => [
                    'ar' => 'وضع علامة وإعادة فتح لاحقاً',
                    'en' => 'Tag & Reopen Later',
                    'fr' => 'Étiqueter et rouvrir plus tard',
                    'es' => 'Etiquetar y reabrir más tarde'
                ],
                'description' => 'Tag Dormant and schedule reopen'
            ],
            [
                'key' => 'send_invoice_email',
                'icon' => 'file-text',
                'name' => [
                    'ar' => 'إرسال بريد الفاتورة',
                    'en' => 'Send Invoice Email',
                    'fr' => 'Envoyer un email de facture',
                    'es' => 'Enviar correo de factura'
                ],
                'description' => 'Email invoice or payment link to client'
            ],
            [
                'key' => 'notify_finance',
                'icon' => 'dollar-sign',
                'name' => [
                    'ar' => 'إشعار المالية',
                    'en' => 'Notify Finance',
                    'fr' => 'Notifier les finances',
                    'es' => 'Notificar a finanzas'
                ],
                'description' => 'Alert finance to review changes'
            ],
            [
                'key' => 'send_reminder_and_task',
                'icon' => 'clock',
                'name' => [
                    'ar' => 'إرسال تذكير ومهمة',
                    'en' => 'Send Reminder + Task',
                    'fr' => 'Envoyer un rappel + tâche',
                    'es' => 'Enviar recordatorio + tarea'
                ],
                'description' => 'Send reminder and create collection task'
            ],
            [
                'key' => 'send_reminder',
                'icon' => 'bell',
                'name' => [
                    'ar' => 'إرسال تذكير',
                    'en' => 'Send Reminder',
                    'fr' => 'Envoyer un rappel',
                    'es' => 'Enviar recordatorio'
                ],
                'description' => 'Remind assignee before due date'
            ],
            [
                'key' => 'trigger_next_step',
                'icon' => 'arrow-right',
                'name' => [
                    'ar' => 'تشغيل الخطوة التالية',
                    'en' => 'Trigger Next Step',
                    'fr' => 'Déclencher l\'étape suivante',
                    'es' => 'Activar siguiente paso'
                ],
                'description' => 'Create next task or move stage'
            ],
            [
                'key' => 'escalate_task',
                'icon' => 'arrow-up',
                'name' => [
                    'ar' => 'تصعيد المهمة',
                    'en' => 'Escalate Task',
                    'fr' => 'Escalader la tâche',
                    'es' => 'Escalar tarea'
                ],
                'description' => 'Notify team lead or reassign'
            ],
            [
                'key' => 'move_stage',
                'icon' => 'arrow-right',
                'name' => [
                    'ar' => 'نقل المرحلة',
                    'en' => 'Move Stage',
                    'fr' => 'Déplacer l\'étape',
                    'es' => 'Mover etapa'
                ],
                'description' => 'Move opportunity to Qualification stage'
            ],
            [
                'key' => 'notify_owner_and_reschedule',
                'icon' => 'calendar',
                'name' => [
                    'ar' => 'إشعار المالك وإعادة الجدولة',
                    'en' => 'Notify Owner + Reschedule',
                    'fr' => 'Notifier le propriétaire + reprogrammer',
                    'es' => 'Notificar propietario + reprogramar'
                ],
                'description' => 'Alert rep and send reschedule link'
            ],
            [
                'key' => 'send_reminder_reschedule',
                'icon' => 'calendar',
                'name' => [
                    'ar' => 'إرسال تذكير/إعادة جدولة',
                    'en' => 'Send Reminder/Reschedule',
                    'fr' => 'Envoyer rappel/reprogrammer',
                    'es' => 'Enviar recordatorio/reprogramar'
                ],
                'description' => 'Reminder if accepted; reschedule if declined'
            ],
            [
                'key' => 'create_contact_and_opportunity',
                'icon' => 'user-plus',
                'name' => [
                    'ar' => 'إنشاء جهة اتصال وفرصة',
                    'en' => 'Create Contact & Opportunity',
                    'fr' => 'Créer contact et opportunité',
                    'es' => 'Crear contacto y oportunidad'
                ],
                'description' => 'Create records and trigger assignment'
            ],
            [
                'key' => 'notify_admin',
                'icon' => 'alert-circle',
                'name' => [
                    'ar' => 'إشعار الإدارة',
                    'en' => 'Notify Admin',
                    'fr' => 'Notifier l\'admin',
                    'es' => 'Notificar administrador'
                ],
                'description' => 'Alert ops to fix mapping issues'
            ]
        ];

        foreach ($actions as $action) {
            AutomationAction::firstOrCreate(
                ['key' => $action['key']],
                $action
            );
        }
    }
}
