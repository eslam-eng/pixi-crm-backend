<?php

namespace Database\Seeders;

use App\Settings\TasksSettings;
use Illuminate\Database\Seeder;

class TasksSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tasksSettings = new TasksSettings();
        
        // Set default values
        $tasksSettings->enable_escalation = true;
        $tasksSettings->enable_priority = true;
        $tasksSettings->escalation_time_hours = 24;
        $tasksSettings->default_followers_users = [];
        $tasksSettings->notify_manager = true;
        
        // Save the settings
        $tasksSettings->save();
    }
}