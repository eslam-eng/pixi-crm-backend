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
                'module_name' => 'contact',
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
                'module_name' => 'contact',
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
                'module_name' => 'opportunity',
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
                'module_name' => 'opportunity',
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
                'module_name' => 'opportunity',
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
                'module_name' => 'opportunity',
                'icon' => 'clock',
                'name' => ['لا يوجد نشاط لفترة زمنية'
                    'ar' => ,
                    'en' => 'No Action for X Time',
                    'fr' => 'Aucune action pendant X temps',
                    'es' => 'Sin acción por X tiempo'
                ],
                'description' => 'No task/comment within the time'
            ],
            [
                'key' => 'opportunity_high_value',
                'module_name' => 'opportunity',
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
                'module_name' => 'opportunity',
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
                'module_name' => 'opportunity',
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
                'module_name' => 'deal',
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
                'module_name' => 'deal',
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
                'module_name' => 'deal',
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
                'module_name' => 'task',
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
                'module_name' => 'task',
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
                'module_name' => 'task',
                'icon' => 'clock',
                'name' => [
                    'ar' => 'مهمة متأخرة',
                    'en' => 'Task Overdue',
                    'fr' => 'Tâche en retard',
                    'es' => 'Tarea vencida'
                ],
                'description' => 'Due date passed without completion'
            ],
        ];

        foreach ($triggers as $trigger) {
            AutomationTrigger::firstOrCreate(
                ['key' => $trigger['key']],
                $trigger
            );
        }
    }
}
