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
        Schema::create('automation_workflow_step_delays', function (Blueprint $table) {
            $table->id();
            $table->foreignId('automation_workflow_step_id')->constrained('automation_workflow_steps', 'id', 'fk_awsd_step_id')->onDelete('cascade');
            $table->integer('duration');
            $table->string('unit'); // minutes, hours, days, etc.
            $table->timestamps();
            
            // Add indexes for better performance
            $table->index(['automation_workflow_step_id'], 'idx_awsd_step_id');
            $table->index(['unit'], 'idx_awsd_unit');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('automation_workflow_step_delays');
    }
};
