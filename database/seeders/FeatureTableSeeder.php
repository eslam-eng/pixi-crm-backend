<?php

namespace Database\Seeders;

use App\Enums\Landlord\ActivationStatusEnum;
use App\Enums\Landlord\FeatureGroupEnum;
use App\Models\Central\Feature;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class FeatureTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // if (Feature::count() > 0) {
        //     return;
        // }

        $features = [
            [
                'name' => [
                    'ar' => 'الحد الاقصي للمستخدمين',
                    'en' => 'Max Users',
                    'fr' => 'Utilisateurs max',
                    'es' => 'Usuarios máximos',
                ],
                'slug' => Str::slug('max users'),
                'group' => FeatureGroupEnum::LIMIT->value,
                'is_active' => ActivationStatusEnum::ACTIVE->value,
            ],
            [
                'name' => [
                    'ar' => 'الحد الأقصى للتخزين',
                    'en' => 'Max Storage',
                    'fr' => 'Stockage max',
                    'es' => 'Almacenamiento máximo',
                ],
                'slug' => Str::slug('max storage'),
                'group' => FeatureGroupEnum::LIMIT->value,
                'is_active' => ActivationStatusEnum::ACTIVE->value,
            ],
            [
                'name' => [
                    'ar' => 'الحد الأقصى لجهات الاتصال',
                    'en' => 'Max Contacts',
                    'fr' => 'Contacts max',
                    'es' => 'Contactos máximos',
                ],
                'slug' => Str::slug('max contacts'),
                'group' => FeatureGroupEnum::LIMIT->value,
                'is_active' => ActivationStatusEnum::ACTIVE->value,
            ],
            [
                'name' => [
                    'ar' => 'الحد الأقصى للتوكينات الشهرية للذكاء الاصطناعي',
                    'en' => 'Max Ai Monthly Tokens',
                    'fr' => 'Max Ai Tokens mensuels',
                    'es' => 'Max Ai Tokens mensuales',
                ],
                'slug' => Str::slug('max monthly tokens'),
                'group' => FeatureGroupEnum::LIMIT->value,
                'is_active' => ActivationStatusEnum::ACTIVE->value,
            ],
            [
                'name' => [
                    'ar' => 'ادارة التكرار',
                    'en' => 'Manage Dublicate',
                    'fr' => 'Gestion des doublons',
                    'es' => 'Gestionar duplicados',
                ],
                'slug' => Str::slug('manage dublicate'),
                'group' => FeatureGroupEnum::FEATURE->value,
                'is_active' => ActivationStatusEnum::ACTIVE->value,
            ],
            [
                'name' => [
                    'ar' => 'ارسال بيانات المنتج',
                    'en' => 'Send Item Data',
                    'fr' => 'Envoyer les données du produit',
                    'es' => 'Enviar datos del producto',
                ],
                'slug' => Str::slug('send item data'),
                'group' => FeatureGroupEnum::FEATURE->value,
                'is_active' => ActivationStatusEnum::ACTIVE->value,
            ],
            [
                'name' => [
                    'ar' => 'تنفيذ التلقيات التلقائية',
                    'en' => 'Max Automation Excution',
                    'fr' => 'Max Exécution automatisée',
                    'es' => 'Max Ejecución de automatización',
                ],
                'slug' => Str::slug('max automation excution'),
                'group' => FeatureGroupEnum::LIMIT->value,
                'is_active' => ActivationStatusEnum::ACTIVE->value,
            ],
            [
                'name' => [
                    'ar' => 'النموذج المضمن',
                    'en' => 'Empeded Form',
                    'fr' => 'Formulaire intégré',
                    'es' => 'Formulario integrado',
                ],
                'slug' => Str::slug('empeded form'),
                'group' => FeatureGroupEnum::FEATURE->value,
                'is_active' => ActivationStatusEnum::ACTIVE->value,
            ],
             [
                'name' => [
                    'ar' => 'النظام المحمول',
                    'en' => 'Mobile App Access',
                    'fr' => 'Accès à l\'application mobile',
                    'es' => 'Acceso a la aplicación móvil',
                ],
                'slug' => Str::slug('mobile app access'),
                'group' => FeatureGroupEnum::FEATURE->value,
                'is_active' => ActivationStatusEnum::ACTIVE->value,
            ],
        ];

        foreach ($features as $feature) {
            Feature::updateOrCreate([
                'slug' => $feature['slug'],
            ], $feature);
        }
    }
}
