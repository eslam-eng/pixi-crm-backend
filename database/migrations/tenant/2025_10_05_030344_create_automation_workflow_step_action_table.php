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
        Schema::create('automation_workflow_step_actions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('automation_workflow_step_id')->constrained('automation_workflow_steps', 'id', 'fk_awsa_step_id')->onDelete('cascade');
            $table->foreignId('automation_action_id')->constrained('automation_actions', 'id', 'fk_awsa_action_id')->onDelete('cascade');
            $table->json('configs')->nullable();
            $table->timestamps();
            
            // Add indexes for better performance
            $table->index(['automation_workflow_step_id'], 'idx_awsa_step_id');
            $table->index(['automation_action_id'], 'idx_awsa_action_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('automation_workflow_step_actions');
    }
};
