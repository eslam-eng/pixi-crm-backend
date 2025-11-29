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
        Schema::create('automation_steps_implements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('automation_workflow_id')->constrained('automation_workflows', 'id', 'fk_asi_workflow_id')->onDelete('cascade');
            $table->foreignId('automation_workflow_step_id')->constrained('automation_workflow_steps', 'id', 'fk_asi_step_id')->onDelete('cascade');
            $table->string('triggerable_type');
            $table->unsignedBigInteger('triggerable_id');
            $table->enum('type', ['condition', 'action', 'delay']);
            $table->integer('step_order')->default(0);
            $table->boolean('implemented')->default(false);
            $table->json('step_data')->nullable(); // Store the step configuration data
            $table->json('context_data')->nullable(); // Store context data for execution
            $table->timestamp('implemented_at')->nullable();
            $table->timestamps();
            
            // Add indexes for better performance
            $table->index(['automation_workflow_id'], 'idx_asi_workflow_id');
            $table->index(['automation_workflow_step_id'], 'idx_asi_step_id');
            $table->index(['triggerable_type', 'triggerable_id'], 'idx_asi_triggerable');
            $table->index(['type'], 'idx_asi_type');
            $table->index(['step_order'], 'idx_asi_step_order');
            $table->index(['implemented'], 'idx_asi_implemented');
            $table->index(['implemented_at'], 'idx_asi_implemented_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('automation_steps_implements');
    }
};
