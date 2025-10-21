<?php

use Database\Seeders\Tenant\AutomationActionSeeder;
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
        Schema::create('automation_actions', function (Blueprint $table) {
            $table->id();
            $table->json('name'); // Multilingual names (ar, en, fr, es)
            $table->string('key')->unique(); // Unique identifier for the action
            $table->string('icon')->nullable();
            $table->text('description')->nullable(); // Optional description
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            // Add indexes for better performance
            $table->index(['is_active']);
            $table->index(['key']);
        });
        
        // Seed default automation actions
        (new AutomationActionSeeder())->run();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('automation_actions');
    }
};
