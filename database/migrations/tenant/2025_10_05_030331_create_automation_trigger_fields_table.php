<?php

use Database\Seeders\Tenant\AutomationTriggerFieldSeeder;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('automation_trigger_fields', function (Blueprint $table) {
            $table->id();
            $table->foreignId('automation_trigger_id')->constrained('automation_triggers')->onDelete('cascade');
            $table->string('field_name'); // e.g., 'email', 'contact.email'
            $table->string('field_type'); // string, integer, decimal, boolean, date, array, text,enum
            $table->string('field_label'); // Human-readable label
            $table->string('field_category')->default('direct'); // direct, relationship, nested
            $table->boolean('is_relationship')->default(false);
            $table->text('description')->nullable();
            $table->string('example_value')->nullable();
            $table->integer('order')->default(0);
            $table->timestamps();

            // Index for faster lookups
            $table->index('automation_trigger_id');
        });

        // Seed the table
        $seeder = new AutomationTriggerFieldSeeder();
        $seeder->run();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('automation_trigger_fields');
    }
};
