<?php

namespace Database\Seeders\Tenant;

use App\Models\Tenant\FormSection;
use Illuminate\Database\Seeder;

class FormSectionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sections = [
            'contacts' => [
                'contacts_basic_info' => ['en' => 'Basic Contact Information', 'ar' => 'معلومات الاتصال الأساسية', 'fr' => 'Informations de contact de base', 'es' => 'Información básica de contacto'],
                'status_classification' => ['en' => 'Contact Status & Classification', 'ar' => 'حالة وتصنيف الاتصال', 'fr' => 'Statut et classification du contact', 'es' => 'Estado y clasificación del contacto'],
                'communication_preferences' => ['en' => 'Communication Preferences', 'ar' => 'تفضيلات التواصل', 'fr' => 'Préférences de communication', 'es' => 'Preferencias de comunicación'],
                'company_info' => ['en' => 'Company Information', 'ar' => 'معلومات الشركة', 'fr' => 'Informations sur l\'entreprise', 'es' => 'Información de la empresa'],
                'address_info' => ['en' => 'Address Information', 'ar' => 'معلومات العنوان', 'fr' => 'Informations sur l\'adresse', 'es' => 'Información de la dirección'],
                'system_fields' => ['en' => 'System Fields', 'ar' => 'حقول النظام', 'fr' => 'Champs système', 'es' => 'Campos del sistema'],
                'additional_info' => ['en' => 'Additional Information', 'ar' => 'معلومات إضافية', 'fr' => 'Informations supplémentaires', 'es' => 'Información adicional'],
            ],
            'leads' => [
                'leads_basic_info' => ['en' => 'Basic Information', 'ar' => 'المعلومات الأساسية', 'fr' => 'Informations de base', 'es' => 'Información básica'],
                'opportunity_details' => ['en' => 'Opportunity Details', 'ar' => 'تفاصيل الفرصة', 'fr' => 'Détails de l\'opportunité', 'es' => 'Detalles de la oportunidad'],
                'notes' => ['en' => 'Notes', 'ar' => 'ملاحظات', 'fr' => 'Remarques', 'es' => 'Notas'],
            ],
            'deals' => [
                'deals_basic_info' => ['en' => 'Basic Information', 'ar' => 'المعلومات الأساسية', 'fr' => 'Informations de base', 'es' => 'Información básica'],
                'tax_discount' => ['en' => 'Tax & Discount', 'ar' => 'الضريبة والخصم', 'fr' => 'Taxes et remises', 'es' => 'Impuestos y descuentos'],
                'deal_summary' => ['en' => 'Deal Summary', 'ar' => 'ملخص الصفقة', 'fr' => 'Résumé de l\'affaire', 'es' => 'Resumen del trato'],
                'assignment' => ['en' => 'Assignment', 'ar' => 'التعيين', 'fr' => 'Affectation', 'es' => 'Asignación'],
                'payment_status' => ['en' => 'Payment & Status', 'ar' => 'الدفع والحالة', 'fr' => 'Paiement et statut', 'es' => 'Pago y estado'],
            ],
            'tasks' => [
                'opportunity_assignment' => ['en' => 'Opportunity & Assignment', 'ar' => 'الفرصة والتعيين', 'fr' => 'Opportunité et affectation', 'es' => 'Oportunidad y asignación'],
                'task_info' => ['en' => 'Task Information', 'ar' => 'معلومات المهمة', 'fr' => 'Informations sur la tâche', 'es' => 'Información de la tarea'],
                'timeline_reminders' => ['en' => 'Timeline & Reminders', 'ar' => 'الجدول الزمني والتذكيرات', 'fr' => 'Chronologie et rappels', 'es' => 'Cronología y recordatorios'],
                'tags_notes' => ['en' => 'Tags & Notes', 'ar' => 'العلامات والملاحظات', 'fr' => 'Balises et notes', 'es' => 'Etiquetas y notas'],
            ],
        ];

        foreach ($sections as $module => $moduleSections) {
            $order = 0;
            foreach ($moduleSections as $key => $name) {
                FormSection::firstOrCreate(
                    ['module' => $module, 'key' => $key],
                    ['name' => $name, 'ordering' => ++$order]
                );
            }
        }
    }
}
