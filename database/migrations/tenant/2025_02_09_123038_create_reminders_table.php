<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('reminders', function (Blueprint $table) {
            $table->id();
            $table->json('name'); // Translatable name field
            $table->integer('time_value'); // Time value (e.g., 5, 15, 30, 1, 2, 7)
            $table->enum('time_unit', ['minutes', 'hours', 'days', 'weeks', 'on_time']); // Time unit
            $table->boolean('is_default')->default(false);
            $table->integer('sort_order')->default(0); // For ordering in dropdown
            $table->timestamps();
        });

        \Artisan::call('tenants:seed', ['--class' => \Database\Seeders\Tenant\ReminderSeeder::class]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reminders');
    }
};
