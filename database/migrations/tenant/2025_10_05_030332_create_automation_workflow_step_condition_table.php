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
        Schema::create('automation_workflow_step_conditions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('automation_workflow_step_id')->constrained('automation_workflow_steps', 'id', 'fk_awsc_step_id')->onDelete('cascade');
            $table->foreignId('field_id')->constrained('automation_trigger_fields', 'id', 'fk_awsc_field_id')->onDelete('cascade');
            $table->string('operation');
            $table->text('value');
            $table->timestamps();
            
            // Add indexes for better performance
            $table->index(['automation_workflow_step_id'], 'idx_awsc_step_id');
            $table->index(['field_id'], 'idx_awsc_field_id');
            $table->index(['operation'], 'idx_awsc_operation');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('automation_workflow_step_conditions');
    }
};
