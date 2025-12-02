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
        Schema::create('automation_delays', function (Blueprint $table) {
            $table->id();
            $table->foreignId('automation_steps_implement_id')->constrained('automation_steps_implements', 'id', 'fk_ad_step_implement_id')->onDelete('cascade');
            $table->integer('duration'); // Duration in minutes
            $table->enum('unit', ['minutes', 'hours', 'days']);
            $table->timestamp('execute_at'); // When this step should be executed
            $table->boolean('processed')->default(false);
            $table->timestamp('processed_at')->nullable();
            $table->json('context_data')->nullable(); // Store context data for delayed execution
            $table->timestamps();
            
            // Add indexes for better performance
            $table->index(['automation_steps_implement_id'], 'idx_ad_step_implement_id');
            $table->index(['execute_at'], 'idx_ad_execute_at');
            $table->index(['processed'], 'idx_ad_processed');
            $table->index(['processed_at'], 'idx_ad_processed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('automation_delays');
    }
};
