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
        Schema::create('task_reminders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained('tasks')->onDelete('cascade');
            $table->foreignId('reminder_id')->constrained('reminders')->onDelete('cascade');
            $table->timestamp('reminder_at')->nullable(); // When the reminder should be triggered
            $table->boolean('is_sent')->default(false); // Track if reminder has been sent
            $table->timestamp('sent_at')->nullable(); // When the reminder was sent
            $table->timestamps();

            // Ensure unique combination of task and reminder
            $table->unique(['task_id', 'reminder_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_reminders');
    }
};