<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Module;

class ModuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $modules = [
            // Voice Calls Group
            // voice_calls
            [
                'key' => 'voice_calls',
                'name' => ['en' => 'Voice Calls', 'ar' => 'المكالمات الصوتية'],
                'group' => 'voice_calls',
                'group_label' => ['en' => 'Voice Calls', 'ar' => 'المكالمات الصوتية'],
                'has_number_field' => 1,
                'number_field_label' => ['en' => 'Monthly Call Minutes', 'ar' => 'دقائق المكالمات الشهرية'],
            ],

            // AI Features Group
            // ai_assistant
            [
                'key' => 'ai_assistant',
                'name' => ['en' => 'AI Assistant', 'ar' => 'مساعد الذكاء الاصطناعي'],
                'group' => 'AI Features',
                'group_label' => ['en' => 'AI Features', 'ar' => 'ميزات الذكاء الاصطناعي'],
                'has_number_field' => 1,
                'number_field_label' => ['en' => 'Monthly AI Assistant Queries', 'ar' => 'استعلامات مساعد الذكاء الاصطناعي الشهرية'],
            ],
            // ai_insights
            [
                'key' => 'ai_insights',
                'name' => ['en' => 'AI Insights', 'ar' => 'رؤى الذكاء الاصطناعي'],
                'group' => 'AI Features',
                'group_label' => ['en' => 'AI Features', 'ar' => 'ميزات الذكاء الاصطناعي'],
                'has_number_field' => 0,
                'number_field_label' => null,
            ],
            // ai_automation
            [
                'key' => 'ai_automation',
                'name' => ['en' => 'AI Automation', 'ar' => 'أتمتة الذكاء الاصطناعي'],
                'group' => 'AI Features',
                'group_label' => ['en' => 'AI Features', 'ar' => 'ميزات الذكاء الاصطناعي'],
                'has_number_field' => 1,
                'number_field_label' => ['en' => 'Monthly AI Automations', 'ar' => 'أتمتة الذكاء الاصطناعي الشهرية'],
            ],
            // lead_scoring
            [
                'key' => 'lead_scoring',
                'name' => ['en' => 'Lead Scoring', 'ar' => 'تسجيل النقاط'],
                'group' => 'AI Features',
                'group_label' => ['en' => 'AI Features', 'ar' => 'ميزات الذكاء الاصطناعي'],
                'has_number_field' => 0,
                'number_field_label' => null,
            ],
            // smart_recommendations
            [
                'key' => 'smart_recommendations',
                'name' => ['en' => 'Smart Recommendations', 'ar' => 'التوصيات الذكية'],
                'group' => 'AI Features',
                'group_label' => ['en' => 'AI Features', 'ar' => 'ميزات الذكاء الاصطناعي'],
                'has_number_field' => 1,
                'number_field_label' => ['en' => 'Monthly Smart Recommendations', 'ar' => 'التوصيات الذكية الشهرية'],
            ],



            // Third-party Integrations Group
            //WhatsApp Integration
            [
                'key' => 'whatsapp_integration',
                'name' => ['en' => 'WhatsApp', 'ar' => 'واتساب'],
                'group' => 'third_party_integrations',
                'group_label' => ['en' => 'third_party integrations', 'ar' => 'تكاملات الطرف الثالث'],
                'has_number_field' => 1,
                'number_field_label' => ['en' => 'Monthly WhatsApp Messages', 'ar' => 'رسائل واتساب شهرية'],
            ],
            //Facebook Integration
            [
                'key' => 'facebook_integration',
                'name' => ['en' => 'Facebook', 'ar' => 'فيسبوك'],
                'group' => 'third_party_integrations',
                'group_label' => ['en' => 'third_party integrations', 'ar' => 'تكاملات الطرف الثالث'],
                'has_number_field' => 0,
                'number_field_label' => null,
            ],
            //Email Integration
            [
                'key' => 'email_integration',
                'name' => ['en' => 'Email', 'ar' => 'البريد الإلكتروني'],
                'group' => 'third_party_integrations',
                'group_label' => ['en' => 'third_party integrations', 'ar' => 'تكاملات الطرف الثالث'],
                'has_number_field' => 1,
                'number_field_label' => ['en' => 'Monthly Email Messages', 'ar' => 'رسائل البريد الإلكتروني الشهرية'],
            ],
            //Google Ads Integration
            [
                'key' => 'google_ads_integration',
                'name' => ['en' => 'Google Ads', 'ar' => 'إعلانات جوجل'],
                'group' => 'third_party_integrations',
                'group_label' => ['en' => 'third_party integrations', 'ar' => 'تكاملات الطرف الثالث'],
                'has_number_field' => 0,
                'number_field_label' => null,
            ],
            //SMS Integration
            [
                'key' => 'sms_integration',
                'name' => ['en' => 'SMS', 'ar' => 'رسائل نصية'],
                'group' => 'third_party_integrations',
                'group_label' => ['en' => 'third_party integrations', 'ar' => 'تكاملات الطرف الثالث'],
                'has_number_field' => 1,
                'number_field_label' => ['en' => 'SMS Sending Limit', 'ar' => 'حد إرسال الرسائل النصية'],
            ],
            //TikTok Ads Integration
            [
                'key' => 'tiktok_ads_integration',
                'name' => ['en' => 'TikTok Ads', 'ar' => 'إعلانات تيك توك'],
                'group' => 'third_party_integrations',
                'group_label' => ['en' => 'third_party integrations', 'ar' => 'تكاملات الطرف الثالث'],
                'has_number_field' => 0,
                'number_field_label' => null,
            ],

            // Core modules without number fields
            [
                'key' => 'crm_core',
                'name' => ['en' => 'CRM Core', 'ar' => 'اساس إدارة علاقات العملاء'],
                'group' => 'core',
                'group_label' => ['en' => 'Core Features', 'ar' => 'الميزات الأساسية'],
                'has_number_field' => 0,
                'number_field_label' => null,
            ],
            [
                'key' => 'deal_pipeline',
                'name' => ['en' => 'Deal Pipeline', 'ar' => 'خط أنابيب الصفقات'],
                'group' => 'sales',
                'group_label' => ['en' => 'Sales', 'ar' => 'المبيعات'],
                'has_number_field' => 0,
                'number_field_label' => null,
            ],
            [
                'key' => 'reports_analytics',
                'name' => ['en' => 'Reports & Analytics', 'ar' => 'التقارير والتحليلات'],
                'group' => 'analytics',
                'group_label' => ['en' => 'Analytics', 'ar' => 'التحليلات'],
                'has_number_field' => 0,
                'number_field_label' => null,
            ],
            [
                'key' => 'product_catalog',
                'name' => ['en' => 'Product Catalog', 'ar' => 'كتالوج المنتجات'],
                'group' => 'products',
                'group_label' => ['en' => 'Products', 'ar' => 'المنتجات'],
                'has_number_field' => 0,
                'number_field_label' => null,
            ],
            [
                'key' => 'mobile_app_access',
                'name' => ['en' => 'Mobile App Access', 'ar' => 'الوصول لتطبيق الهاتف'],
                'group' => 'access',
                'group_label' => ['en' => 'Access', 'ar' => 'الوصول'],
                'has_number_field' => 0,
                'number_field_label' => null,
            ],
            [
                'key' => 'lead_management',
                'name' => ['en' => 'Lead Management', 'ar' => 'إدارة العملاء المحتملين'],
                'group' => 'sales',
                'group_label' => ['en' => 'Sales', 'ar' => 'المبيعات'],
                'has_number_field' => 0,
                'number_field_label' => null,
            ],
            [
                'key' => 'task_management',
                'name' => ['en' => 'Task Management', 'ar' => 'إدارة المهام'],
                'group' => 'productivity',
                'group_label' => ['en' => 'Productivity', 'ar' => 'الإنتاجية'],
                'has_number_field' => 0,
                'number_field_label' => null,
            ],
            [
                'key' => 'workflow_automation',
                'name' => ['en' => 'Workflow Automation', 'ar' => 'أتمتة سير العمل'],
                'group' => 'automation',
                'group_label' => ['en' => 'Automation', 'ar' => 'الأتمتة'],
                'has_number_field' => 0,
                'number_field_label' => null,
            ],
            [
                'key' => 'invoicing_billing',
                'name' => ['en' => 'Invoicing & Billing', 'ar' => 'الفواتير والفوترة'],
                'group' => 'finance',
                'group_label' => ['en' => 'Finance', 'ar' => 'المالية'],
                'has_number_field' => 0,
                'number_field_label' => null,
            ],
            [
                'key' => 'api_access',
                'name' => ['en' => 'API Access', 'ar' => 'الوصول لواجهة برمجة التطبيقات'],
                'group' => 'access',
                'group_label' => ['en' => 'Access', 'ar' => 'الوصول'],
                'has_number_field' => 0,
                'number_field_label' => null,
            ],
            [
                'key' => 'contact_management',
                'name' => ['en' => 'Contact Management', 'ar' => 'إدارة جهات الاتصال'],
                'group' => 'core',
                'group_label' => ['en' => 'Core Features', 'ar' => 'الميزات الأساسية'],
                'has_number_field' => 0,
                'number_field_label' => null,
            ],
            [
                'key' => 'calendar_scheduling',
                'name' => ['en' => 'Calendar & Scheduling', 'ar' => 'التقويم والجدولة'],
                'group' => 'productivity',
                'group_label' => ['en' => 'Productivity', 'ar' => 'الإنتاجية'],
                'has_number_field' => 0,
                'number_field_label' => null,
            ],
            [
                'key' => 'forms_landing_pages',
                'name' => ['en' => 'Forms & Landing Pages', 'ar' => 'النماذج وصفحات الهبوط'],
                'group' => 'marketing',
                'group_label' => ['en' => 'Marketing', 'ar' => 'التسويق'],
                'has_number_field' => 0,
                'number_field_label' => null,
            ],
            [
                'key' => 'custom_fields',
                'name' => ['en' => 'Custom Fields', 'ar' => 'الحقول المخصصة'],
                'group' => 'customization',
                'group_label' => ['en' => 'Customization', 'ar' => 'التخصيص'],
                'has_number_field' => 0,
                'number_field_label' => null,
            ],
        ];

        foreach ($modules as $module) {
            Module::updateOrCreate(
                ['key' => $module['key']], // The unique identifier to check
                [
                    'name' => $module['name'],
                    'group' => $module['group'],
                    'group_label' => $module['group_label'],
                    'has_number_field' => $module['has_number_field'],
                    'number_field_label' => $module['number_field_label'],
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }
    }
}
