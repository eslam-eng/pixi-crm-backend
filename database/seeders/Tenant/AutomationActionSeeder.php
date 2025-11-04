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
                'key' => 'create_contact',
                'icon' => 'user-plus',
                'name' => [
                    'ar' => 'إنشاء جهة اتصال',
                    'en' => 'Create Contact',
                    'fr' => 'Créer contact',
                    'es' => 'Crear contacto'
                ],
                'description' => 'Create contact'
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
            ],
            [
                'key' => 'assign_to_sales',
                'icon' => 'alert-circle',
                'name' => [
                    'ar' => 'تعيين للمبيعات',
                    'en' => 'Assign to Sales',
                    'fr' => 'Assigner à la vente',
                    'es' => 'Asignar a la venta'
                ],
                'description' => 'Assign to sales rep/team based on criteria',
                'configs' => [
                    'assignment_type' => [
                        'type' => 'select',
                        'label' => [
                            'ar' => 'نوع التعيين',
                            'en' => 'Assignment Type',
                            'fr' => 'Type d\'assignation',
                            'es' => 'Tipo de asignación'
                        ],
                        'options' => [
                            [
                                'value' => 'specific_user',
                                'label' => [
                                    'ar' => 'حسب مستخدم محدد',
                                    'en' => 'According to specific user',
                                    'fr' => 'Selon un utilisateur spécifique',
                                    'es' => 'Según usuario específico'
                                ],
                                'config' => [
                                    'show_user_dropdown' => true
                                ]
                            ],
                            [
                                'value' => 'owner_user',
                                'label' => [
                                    'ar' => 'حسب المستخدم المالك',
                                    'en' => 'According to Owner user',
                                    'fr' => 'Selon l\'utilisateur propriétaire',
                                    'es' => 'Según usuario propietario'
                                ],
                                'config' => [
                                    'exclude_triggers' => ['contact_created']
                                ]
                            ],
                            [
                                'value' => 'round_robin_sequential',
                                'label' => [
                                    'ar' => 'Round Robile Sequention',
                                    'en' => 'Round Robin Sequential',
                                    'fr' => 'Round Robin Séquentiel',
                                    'es' => 'Round Robin Secuencial'
                                ],
                                'config' => [
                                    'requires_table' => 'employee_assignments'
                                ]
                            ],
                            [
                                'value' => 'round_robin_active_opportunities',
                                'label' => [
                                    'ar' => 'Round Robile Random According to Active Opportunity',
                                    'en' => 'Round Robin Random According to Active Opportunity',
                                    'fr' => 'Round Robin Aléatoire selon Opportunités Actives',
                                    'es' => 'Round Robin Aleatorio según Oportunidades Activas'
                                ]
                            ],
                            [
                                'value' => 'round_robin_active_tasks',
                                'label' => [
                                    'ar' => 'Round Robile Random According to Active Tasks',
                                    'en' => 'Round Robin Random According to Active Tasks',
                                    'fr' => 'Round Robin Aléatoire selon Tâches Actives',
                                    'es' => 'Round Robin Aleatorio según Tareas Activas'
                                ]
                            ],
                            [
                                'value' => 'round_robin_performance',
                                'label' => [
                                    'ar' => 'Round Robile Random According to Performance',
                                    'en' => 'Round Robin Random According to Performance',
                                    'fr' => 'Round Robin Aléatoire selon Performance',
                                    'es' => 'Round Robin Aleatorio según Rendimiento'
                                ]
                            ],
                            [
                                'value' => 'round_robin_best_seller',
                                'label' => [
                                    'ar' => 'Round Robile Random According to Best seller',
                                    'en' => 'Round Robin Random According to Best seller',
                                    'fr' => 'Round Robin Aléatoire selon Meilleur Vendeur',
                                    'es' => 'Round Robin Aleatorio según Mejor Vendedor'
                                ],
                                'config' => [
                                    'based_on' => 'deals_won_last_3_months',
                                    'description' => [
                                        'ar' => 'بناء على أكثر حد باع في آخر 3 شهور بناء على ال Deals Won',
                                        'en' => 'Based on highest sales in last 3 months from Deals Won',
                                        'fr' => 'Basé sur les ventes les plus élevées dans les 3 derniers mois des Deals Won',
                                        'es' => 'Basado en las ventas más altas en los últimos 3 meses de Deals Won'
                                    ]
                                ]
                            ]
                        ],
                        'default' => 'specific_user',
                        'required' => true
                    ]
                ]
            ],
            [
                'key' => 'create_opportunity',
                'icon' => 'user-plus',
                'name' => [
                    'ar' => 'إنشاء فرصة',
                    'en' => 'Create Opportunity',
                    'fr' => 'Créer opportunité',
                    'es' => 'Crear oportunidad'
                ],
                'description' => 'Create opportunity',
                'configs' => [
                    'stage_id' => [
                        'type' => 'select',
                        'rules' => 'required|exists:stages,id',
                    ]
                ]
            ],
        ];

        foreach ($actions as $action) {
            AutomationAction::firstOrCreate(
                ['key' => $action['key']],
                $action
            );
        }
    }
}
