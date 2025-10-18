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
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('report_type');
            $table->string('category');
            $table->boolean('is_active')->default(true);
            $table->boolean('is_scheduled')->default(false);
            $table->string('schedule_frequency')->nullable(); // daily, weekly, monthly, quarterly, yearly
            $table->time('schedule_time')->nullable();
            $table->json('recipients')->nullable(); // email addresses
            $table->foreignId('created_by_id')->constrained('users')->onDelete('cascade');
            $table->timestamp('last_run_at')->nullable();
            $table->timestamp('next_run_at')->nullable();
            $table->json('settings')->nullable(); // report-specific settings
            $table->json('permissions')->nullable(); // role-based permissions
            $table->timestamps();

            $table->index(['report_type', 'category']);
            $table->index(['is_active', 'is_scheduled']);
            $table->index('next_run_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
