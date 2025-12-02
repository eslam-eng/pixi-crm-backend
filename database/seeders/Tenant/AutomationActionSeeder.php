<?php

namespace Database\Seeders\Tenant;

use App\Models\Tenant\AutomationAction;
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
                'id' => 2,
                'key' => 'notify_owner',
                'icon' => 'bell',
                'name' => [
                    'ar' => 'إشعار المالك',
                    'en' => 'Notify Owner',
                    'fr' => 'Notifier le propriétaire',
                    'es' => 'Notificar al propietario'
                ],
                'description' => 'Send internal notification to owner',
                'configs' => [
                    'message' => [
                        'type' => 'text',
                        'label' => [
                            'ar' => 'محتوى الاشعار',
                            'en' => 'Notification Message',
                            'fr' => 'Message de notification',
                            'es' => 'Mensaje de notificación'
                        ],
                        'required' => true,
                    ]
                ]
            ],
            [
                'id' => 6,
                'key' => 'send_email',
                'icon' => 'mail',
                'name' => [
                    'ar' => 'إرسال بريد إلكتروني',
                    'en' => 'Send Email',
                    'fr' => 'Envoyer un email',
                    'es' => 'Enviar correo electrónico'
                ],
                'description' => 'Follow-up/proposal email',
                'configs' => [
                    'email_subject' => [
                        'type' => 'text',
                        'label' => [
                            'ar' => 'عنوان الايميل',
                            'en' => 'Email Subject',
                            'fr' => 'Objet du mail',
                            'es' => 'Asunto del correo'
                        ],
                        'required' => true,
                    ],
                    'email_message' => [
                        'type' => 'textarea',
                        'label' => [
                            'ar' => 'محتوى الايميل',
                            'en' => 'Email Body',
                            'fr' => 'Corps du mail',
                            'es' => 'Cuerpo del correo'
                        ],
                        'required' => true,
                    ]
                    ,
                    'email_template_id' => [
                        'type' => 'select',
                        'label' => [
                            'ar' => 'قالب الايميل',
                            'en' => 'Email Template',
                            'fr' => 'Modèle de mail',
                            'es' => 'Plantilla de correo'
                        ],
                        'required' => true,
                        'options' => 'tempates api'
                    ]
                ]
            ],
            [
                'id' => 7,
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
                'id' => 8,
                'key' => 'notify_manager',
                'icon' => 'alert-triangle',
                'name' => [
                    'ar' => 'إشعار المدير',
                    'en' => 'Notify Manager',
                    'fr' => 'Notifier le manager',
                    'es' => 'Notificar al gerente'
                ],
                'description' => 'High-priority internal alert',
                'configs' => [
                    'message' => [
                        'type' => 'text',
                        'label' => [
                            'ar' => 'محتوى الاشعار',
                            'en' => 'Notification Message',
                            'fr' => 'Message de notification',
                            'es' => 'Mensaje de notificación'
                        ],
                        'required' => true,
                    ]
                ]
            ],
            [
                'id' => 9,
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
                'id' => 14,
                'key' => 'send_reminder',
                'icon' => 'bell',
                'name' => [
                    'ar' => 'إرسال تذكير',
                    'en' => 'Send Reminder',
                    'fr' => 'Envoyer un rappel',
                    'es' => 'Enviar recordatorio'
                ],
                'description' => 'Remind assignee before due date',
                'module_name' => 'task'
            ],
            [
                'id' => 16,
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
                'id' => 17,
                'key' => 'move_stage',
                'icon' => 'arrow-right',
                'name' => [
                    'ar' => 'نقل المرحلة',
                    'en' => 'Move Stage',
                    'fr' => 'Déplacer l\'étape',
                    'es' => 'Mover etapa'
                ],
                'description' => 'Move opportunity to Qualification stage',
                'module_name' => 'opportunity'
            ],
            [
                'id' => 20,
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
                'id' => 21,
                'key' => 'notify_admin',
                'icon' => 'alert-circle',
                'name' => [
                    'ar' => 'إشعار الإدارة',
                    'en' => 'Notify Admin',
                    'fr' => 'Notifier l\'admin',
                    'es' => 'Notificar administrador'
                ],
                'description' => 'Alert ops to fix mapping issues',
                'configs' => [
                    'message' => [
                        'type' => 'text',
                        'label' => [
                            'ar' => 'محتوى الاشعار',
                            'en' => 'Notification Message',
                            'fr' => 'Message de notification',
                            'es' => 'Mensaje de notificación'
                        ],
                        'required' => true,
                    ]
                ]
            ],
            [
                'id' => 22,
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
                'id' => 23,
                'key' => 'create_opportunity',
                'icon' => 'user-plus',
                'name' => [
                    'ar' => 'إنشاء فرصة',
                    'en' => 'Create Opportunity',
                    'fr' => 'Créer opportunité',
                    'es' => 'Crear oportunidad'
                ],
                'description' => 'Create opportunity'
            ],
        ];

        foreach ($actions as $action) {
            AutomationAction::firstOrCreate(
                ['id' => $action['id']],
                $action
            );
        }
    }
}
