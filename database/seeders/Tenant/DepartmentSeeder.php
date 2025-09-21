<?php

namespace Database\Seeders\Tenant;

use App\Models\Tenant\Department;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departments = [
            [
                'name' => [
                    'en' => 'Sales',
                    'ar' => 'المبيعات',
                    'fr' => 'Ventes',
                    'es' => 'Ventas'
                ],
                'description' => 'Sales and business development department',
                'is_active' => true,
            ],
            [
                'name' => [
                    'en' => 'Marketing',
                    'ar' => 'التسويق',
                    'fr' => 'Marketing',
                    'es' => 'Mercadeo'
                ],
                'description' => 'Marketing and advertising department',
                'is_active' => true,
            ],
            [
                'name' => [
                    'en' => 'Support',
                    'ar' => 'الدعم',
                    'fr' => 'Support',
                    'es' => 'Soporte'
                ],
                'description' => 'Customer support and technical assistance',
                'is_active' => true,
            ],
            [
                'name' => [
                    'en' => 'Development',
                    'ar' => 'التطوير',
                    'fr' => 'Développement',
                    'es' => 'Desarrollo'
                ],
                'description' => 'Software development and IT department',
                'is_active' => true,
            ],
            [
                'name' => [
                    'en' => 'Management',
                    'ar' => 'الإدارة',
                    'fr' => 'Gestion',
                    'es' => 'Gestión'
                ],
                'description' => 'Executive management and leadership',
                'is_active' => true,
            ],
            [
                'name' => [
                    'en' => 'Operations',
                    'ar' => 'العمليات',
                    'fr' => 'Opérations',
                    'es' => 'Operaciones'
                ],
                'description' => 'Business operations and logistics',
                'is_active' => true,
            ],
            [
                'name' => [
                    'en' => 'Finance',
                    'ar' => 'المالية',
                    'fr' => 'Finance',
                    'es' => 'Finanzas'
                ],
                'description' => 'Financial management and accounting',
                'is_active' => true,
            ],
            [
                'name' => [
                    'en' => 'HR',
                    'ar' => 'الموارد البشرية',
                    'fr' => 'RH',
                    'es' => 'RRHH'
                ],
                'description' => 'Human resources and personnel management',
                'is_active' => true,
            ],
            [
                'name' => [
                    'en' => 'IT',
                    'ar' => 'تقنية المعلومات',
                    'fr' => 'IT',
                    'es' => 'TI'
                ],
                'description' => 'Information technology and systems',
                'is_active' => true,
            ],
            [
                'name' => [
                    'en' => 'Legal',
                    'ar' => 'القانونية',
                    'fr' => 'Juridique',
                    'es' => 'Legal'
                ],
                'description' => 'Legal affairs and compliance',
                'is_active' => true,
            ],
        ];

        foreach ($departments as $department) {
            Department::create($department);
        }
    }
}
