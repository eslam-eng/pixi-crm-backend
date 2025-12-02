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
        Schema::create('automation_logs', function (Blueprint $table) {
            $table->id();
            $table->string('automation_type'); // reminder, escalation, auto_assignment, status_update
            $table->string('entity_type'); // Task, Event, Deal, etc.
            $table->unsignedBigInteger('entity_id');
            $table->enum('status', ['success', 'failed', 'skipped', 'running'])->default('success');
            $table->text('action_taken')->nullable(); // Description of what happened
            $table->json('metadata')->nullable(); // Additional data
            $table->text('error_message')->nullable();
            $table->foreignId('triggered_by_id')->nullable()->constrained('users')->onDelete('set null'); // NULL for system-triggered
            $table->timestamps();
            
            // Indexes
            $table->index('automation_type');
            $table->index('entity_type');
            $table->index(['entity_type', 'entity_id']);
            $table->index('status');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('automation_logs');
    }
};

