<?php

use App\Enums\TaskStatusEnum;
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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->nullable()->constrained('leads')->onDelete('cascade');
            $table->string('title');
            $table->text('description');
            $table->foreignId('task_type_id')->constrained('task_types')->onDelete('cascade');
            $table->enum('status', TaskStatusEnum::values())->default(TaskStatusEnum::PENDING->value);
            $table->foreignId('priority_id')->constrained('priorities');
            $table->date('due_date');
            $table->time('due_time');
            $table->foreignId('assigned_to_id')->constrained('users');
            $table->json('tags')->nullable();
            $table->text('additional_notes')->nullable();
            $table->boolean('escalation_sent')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
