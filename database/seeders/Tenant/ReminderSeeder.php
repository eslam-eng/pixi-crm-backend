<?php

namespace Database\Seeders\Tenant;

use App\Models\Tenant\Reminder;
use Illuminate\Database\Seeder;

class ReminderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $reminders = [
            [
                'name' => [
                    'en' => 'On time',
                    'ar' => 'في الوقت المحدد',
                    'es' => 'A tiempo',
                    'fr' => 'À l\'heure'
                ],
                'time_value' => 0,
                'time_unit' => 'on_time',
                'is_default' => false,
                'sort_order' => 1,
            ],
            [
                'name' => [
                    'en' => '5 minutes before',
                    'ar' => '5 دقائق قبل',
                    'es' => '5 minutos antes',
                    'fr' => '5 minutes avant'
                ],
                'time_value' => 5,
                'time_unit' => 'minutes',
                'is_default' => false,
                'sort_order' => 2,
            ],
            [
                'name' => [
                    'en' => '15 minutes before',
                    'ar' => '15 دقيقة قبل',
                    'es' => '15 minutos antes',
                    'fr' => '15 minutes avant'
                ],
                'time_value' => 15,
                'time_unit' => 'minutes',
                'is_default' => false,
                'sort_order' => 3,
            ],
            [
                'name' => [
                    'en' => '30 minutes before',
                    'ar' => '30 دقيقة قبل',
                    'es' => '30 minutos antes',
                    'fr' => '30 minutes avant'
                ],
                'time_value' => 30,
                'time_unit' => 'minutes',
                'is_default' => true, // This is the default selected option
                'sort_order' => 4,
            ],
            [
                'name' => [
                    'en' => '1 hour before',
                    'ar' => 'ساعة واحدة قبل',
                    'es' => '1 hora antes',
                    'fr' => '1 heure avant'
                ],
                'time_value' => 1,
                'time_unit' => 'hours',
                'is_default' => false,
                'sort_order' => 5,
            ],
            [
                'name' => [
                    'en' => '2 hours before',
                    'ar' => 'ساعتين قبل',
                    'es' => '2 horas antes',
                    'fr' => '2 heures avant'
                ],
                'time_value' => 2,
                'time_unit' => 'hours',
                'is_default' => false,
                'sort_order' => 6,
            ],
            [
                'name' => [
                    'en' => '1 day before',
                    'ar' => 'يوم واحد قبل',
                    'es' => '1 día antes',
                    'fr' => '1 jour avant'
                ],
                'time_value' => 1,
                'time_unit' => 'days',
                'is_default' => false,
                'sort_order' => 7,
            ],
            [
                'name' => [
                    'en' => '2 days before',
                    'ar' => 'يومين قبل',
                    'es' => '2 días antes',
                    'fr' => '2 jours avant'
                ],
                'time_value' => 2,
                'time_unit' => 'days',
                'is_default' => false,
                'sort_order' => 8,
            ],
            [
                'name' => [
                    'en' => '1 week before',
                    'ar' => 'أسبوع واحد قبل',
                    'es' => '1 semana antes',
                    'fr' => '1 semaine avant'
                ],
                'time_value' => 1,
                'time_unit' => 'weeks',
                'is_default' => false,
                'sort_order' => 9,
            ],
        ];

        foreach ($reminders as $reminderData) {
            Reminder::updateOrCreate(
                [
                    'time_value' => $reminderData['time_value'],
                    'time_unit' => $reminderData['time_unit'],
                ],
                $reminderData
            );
        }
    }
}
