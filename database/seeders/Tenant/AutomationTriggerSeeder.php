<?php

namespace Database\Seeders\Tenant;

use App\Models\Tenant\AutomationTrigger;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AutomationTriggerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $triggers = [
            [
                'key' => 'contact_created',
                'icon' => 'user-plus',
                'name' => [
                    'ar' => 'تم إنشاء جهة اتصال',
                    'en' => 'Contact Created',
                    'fr' => 'Contact créé',
                    'es' => 'Contacto creado'
                ],
                'description' => 'New contact added manually or via import'
            ],
            [
                'key' => 'contact_updated',
                'icon' => 'user',
                'name' => [
                    'ar' => 'تم تحديث جهة الاتصال',
                    'en' => 'Contact Updated',
                    'fr' => 'Contact mis à jour',
                    'es' => 'Contacto actualizado'
                ],
                'description' => 'Key fields changed (country, phone, email, etc.)'
            ],
            [
                'key' => 'opportunity_created',
                'icon' => 'trending-up',
                'name' => [
                    'ar' => 'تم إنشاء فرصة',
                    'en' => 'Opportunity Created',
                    'fr' => 'Opportunité créée',
                    'es' => 'Oportunidad creada'
                ],
                'description' => 'New opportunity added (from form submission)'
            ],
            [
                'key' => 'opportunity_lead_qualified',
                'icon' => 'target',
                'name' => [
                    'ar' => 'تم تأهيل الفرصة',
                    'en' => 'Opportunity Qualified',
                    'fr' => 'Opportunité qualifiée',
                    'es' => 'Oportunidad calificada'
                ],
                'description' => 'Opportunity status changed to Qualified'
            ],
            [
                'key' => 'opportunity_stage_changed',
                'icon' => 'arrow-right',
                'name' => [
                    'ar' => 'تم تغيير المرحلة',
                    'en' => 'Stage Changed',
                    'fr' => 'Étape modifiée',
                    'es' => 'Etapa cambiada'
                ],
                'description' => 'Opportunity moved between stages'
            ],
            [
                'key' => 'opportunity_no_action_for_x_time',
                'icon' => 'clock',
                'name' => [
                    'ar' => 'لا يوجد نشاط لفترة زمنية',
                    'en' => 'No Action for X Time',
                    'fr' => 'Aucune action pendant X temps',
                    'es' => 'Sin acción por X tiempo'
                ],
                'description' => 'No task/comment within the time'
            ],
            [
                'key' => 'opportunity_high_value',
                'icon' => 'dollar-sign',
                'name' => [
                    'ar' => 'فرصة عالية القيمة',
                    'en' => 'High Value Opportunity',
                    'fr' => 'Opportunité de haute valeur',
                    'es' => 'Oportunidad de alto valor'
                ],
                'description' => 'Amount crosses threshold'
            ],
            [
                'key' => 'opportunity_won',
                'icon' => 'check-circle',
                'name' => [
                    'ar' => 'تم الفوز بالفرصة',
                    'en' => 'Opportunity Won',
                    'fr' => 'Opportunité gagnée',
                    'es' => 'Oportunidad ganada'
                ],
                'description' => 'Marked as Won'
            ],
            [
                'key' => 'opportunity_lost',
                'icon' => 'x-circle',
                'name' => [
                    'ar' => 'تم خسارة الفرصة',
                    'en' => 'Opportunity Lost',
                    'fr' => 'Opportunité perdue',
                    'es' => 'Oportunidad perdida'
                ],
                'description' => 'Marked as Lost'
            ],
            [
                'key' => 'deal_created',
                'icon' => 'file-text',
                'name' => [
                    'ar' => 'تم إنشاء صفقة',
                    'en' => 'Deal Created',
                    'fr' => 'Affaire créée',
                    'es' => 'Trato creado'
                ],
                'description' => 'New deal/contract created post-win'
            ],
            [
                'key' => 'deal_updated',
                'icon' => 'edit',
                'name' => [
                    'ar' => 'تم تحديث الصفقة',
                    'en' => 'Deal Updated',
                    'fr' => 'Affaire mise à jour',
                    'es' => 'Trato actualizado'
                ],
                'description' => 'Deal amount/terms changed'
            ],
            [
                'key' => 'deal_overdue_payment',
                'icon' => 'alert-triangle',
                'name' => [
                    'ar' => 'دفعة متأخرة للصفقة',
                    'en' => 'Deal Overdue Payment',
                    'fr' => 'Paiement en retard',
                    'es' => 'Pago vencido del trato'
                ],
                'description' => 'Payment not received by due date'
            ],
            [
                'key' => 'task_created',
                'icon' => 'check-square',
                'name' => [
                    'ar' => 'تم إنشاء مهمة',
                    'en' => 'Task Created',
                    'fr' => 'Tâche créée',
                    'es' => 'Tarea creada'
                ],
                'description' => 'New task created (call/email/follow-up)'
            ],
            [
                'key' => 'task_completed',
                'icon' => 'check',
                'name' => [
                    'ar' => 'تم إنجاز المهمة',
                    'en' => 'Task Completed',
                    'fr' => 'Tâche terminée',
                    'es' => 'Tarea completada'
                ],
                'description' => 'Task marked as done'
            ],
            [
                'key' => 'task_overdue',
                'icon' => 'clock',
                'name' => [
                    'ar' => 'مهمة متأخرة',
                    'en' => 'Task Overdue',
                    'fr' => 'Tâche en retard',
                    'es' => 'Tarea vencida'
                ],
                'description' => 'Due date passed without completion'
            ],
            [
                'key' => 'calendar_event_created',
                'icon' => 'calendar',
                'name' => [
                    'ar' => 'تم إنشاء حدث',
                    'en' => 'Event Created',
                    'fr' => 'Événement créé',
                    'es' => 'Evento creado'
                ],
                'description' => 'Client books a meeting/demo'
            ],
            [
                'key' => 'calendar_event_cancelled',
                'icon' => 'x',
                'name' => [
                    'ar' => 'تم إلغاء الحدث',
                    'en' => 'Event Cancelled',
                    'fr' => 'Événement annulé',
                    'es' => 'Evento cancelado'
                ],
                'description' => 'Client cancels meeting'
            ],
            [
                'key' => 'calendar_attendee_rsvp',
                'icon' => 'users',
                'name' => [
                    'ar' => 'رد الحضور',
                    'en' => 'Attendee RSVP',
                    'fr' => 'RSVP participant',
                    'es' => 'RSVP asistente'
                ],
                'description' => 'Client accepts/declines meeting'
            ],
            [
                'key' => 'form_submitted',
                'icon' => 'file-text',
                'name' => [
                    'ar' => 'تم إرسال النموذج',
                    'en' => 'Form Submitted',
                    'fr' => 'Formulaire soumis',
                    'es' => 'Formulario enviado'
                ],
                'description' => 'New lead from Meta/Website/Typeform'
            ],
            [
                'key' => 'form_field_mapping_error',
                'icon' => 'alert-circle',
                'name' => [
                    'ar' => 'خطأ في ربط الحقول',
                    'en' => 'Field Mapping Error',
                    'fr' => 'Erreur de mappage de champ',
                    'es' => 'Error de mapeo de campo'
                ],
                'description' => 'Form data incomplete or unmapped'
            ]
        ];

        foreach ($triggers as $trigger) {
            AutomationTrigger::firstOrCreate(
                ['key' => $trigger['key']],
                $trigger
            );
        }
    }
}
