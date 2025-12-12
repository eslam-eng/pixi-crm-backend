<?php

namespace Database\Seeders;

use App\Models\Central\Industry;
use Illuminate\Database\Seeder;

class IndustrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $industries = [
            [
                'name' => [
                    'en' => 'Technology',
                    'ar' => 'التكنولوجيا',
                    'fr' => 'Technologie',
                    'es' => 'Tecnología'
                ],
                'description' => 'Software, hardware, and IT services',
                'is_active' => true,
            ],
            [
                'name' => [
                    'en' => 'Healthcare',
                    'ar' => 'الرعاية الصحية',
                    'fr' => 'Santé',
                    'es' => 'Salud'
                ],
                'description' => 'Medical services, pharmaceuticals, and health technology',
                'is_active' => true,
            ],
            [
                'name' => [
                    'en' => 'Finance',
                    'ar' => 'المالية',
                    'fr' => 'Finance',
                    'es' => 'Finanzas'
                ],
                'description' => 'Banking, insurance, and financial services',
                'is_active' => true,
            ],
            [
                'name' => [
                    'en' => 'Retail',
                    'ar' => 'التجزئة',
                    'fr' => 'Commerce de détail',
                    'es' => 'Venta al por menor'
                ],
                'description' => 'Consumer goods and retail services',
                'is_active' => true,
            ],
            [
                'name' => [
                    'en' => 'Manufacturing',
                    'ar' => 'التصنيع',
                    'fr' => 'Fabrication',
                    'es' => 'Fabricación'
                ],
                'description' => 'Production and industrial manufacturing',
                'is_active' => true,
            ],
            [
                'name' => [
                    'en' => 'Education',
                    'ar' => 'التعليم',
                    'fr' => 'Éducation',
                    'es' => 'Educación'
                ],
                'description' => 'Schools, universities, and educational services',
                'is_active' => true,
            ],
            [
                'name' => [
                    'en' => 'Real Estate',
                    'ar' => 'العقارات',
                    'fr' => 'Immobilier',
                    'es' => 'Bienes raíces'
                ],
                'description' => 'Property development, sales, and management',
                'is_active' => true,
            ],
            [
                'name' => [
                    'en' => 'Hospitality',
                    'ar' => 'الضيافة',
                    'fr' => 'Hôtellerie',
                    'es' => 'Hospitalidad'
                ],
                'description' => 'Hotels, restaurants, and tourism',
                'is_active' => true,
            ],
            [
                'name' => [
                    'en' => 'Transportation',
                    'ar' => 'النقل',
                    'fr' => 'Transport',
                    'es' => 'Transporte'
                ],
                'description' => 'Logistics, shipping, and transportation services',
                'is_active' => true,
            ],
            [
                'name' => [
                    'en' => 'Construction',
                    'ar' => 'البناء',
                    'fr' => 'Construction',
                    'es' => 'Construcción'
                ],
                'description' => 'Building and infrastructure development',
                'is_active' => true,
            ],
            [
                'name' => [
                    'en' => 'Energy',
                    'ar' => 'الطاقة',
                    'fr' => 'Énergie',
                    'es' => 'Energía'
                ],
                'description' => 'Oil, gas, renewable energy, and utilities',
                'is_active' => true,
            ],
            [
                'name' => [
                    'en' => 'Telecommunications',
                    'ar' => 'الاتصالات',
                    'fr' => 'Télécommunications',
                    'es' => 'Telecomunicaciones'
                ],
                'description' => 'Communication services and network infrastructure',
                'is_active' => true,
            ],
            [
                'name' => [
                    'en' => 'Media & Entertainment',
                    'ar' => 'الإعلام والترفيه',
                    'fr' => 'Médias et divertissement',
                    'es' => 'Medios y entretenimiento'
                ],
                'description' => 'Broadcasting, publishing, and entertainment',
                'is_active' => true,
            ],
            [
                'name' => [
                    'en' => 'Agriculture',
                    'ar' => 'الزراعة',
                    'fr' => 'Agriculture',
                    'es' => 'Agricultura'
                ],
                'description' => 'Farming, food production, and agribusiness',
                'is_active' => true,
            ],
            [
                'name' => [
                    'en' => 'Automotive',
                    'ar' => 'السيارات',
                    'fr' => 'Automobile',
                    'es' => 'Automotriz'
                ],
                'description' => 'Vehicle manufacturing and automotive services',
                'is_active' => true,
            ],
            [
                'name' => [
                    'en' => 'Consulting',
                    'ar' => 'الاستشارات',
                    'fr' => 'Conseil',
                    'es' => 'Consultoría'
                ],
                'description' => 'Business and professional consulting services',
                'is_active' => true,
            ],
            [
                'name' => [
                    'en' => 'E-commerce',
                    'ar' => 'التجارة الإلكترونية',
                    'fr' => 'Commerce électronique',
                    'es' => 'Comercio electrónico'
                ],
                'description' => 'Online retail and digital marketplaces',
                'is_active' => true,
            ],
            [
                'name' => [
                    'en' => 'Legal Services',
                    'ar' => 'الخدمات القانونية',
                    'fr' => 'Services juridiques',
                    'es' => 'Servicios legales'
                ],
                'description' => 'Law firms and legal consulting',
                'is_active' => true,
            ],
            [
                'name' => [
                    'en' => 'Non-Profit',
                    'ar' => 'غير ربحي',
                    'fr' => 'Sans but lucratif',
                    'es' => 'Sin fines de lucro'
                ],
                'description' => 'Charitable organizations and NGOs',
                'is_active' => true,
            ],
            [
                'name' => [
                    'en' => 'Other',
                    'ar' => 'أخرى',
                    'fr' => 'Autre',
                    'es' => 'Otro'
                ],
                'description' => 'Other industries not listed above',
                'is_active' => true,
            ],
        ];

        foreach ($industries as $industry) {
            Industry::create($industry);
        }
    }
}
