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
        Schema::create('automation_workflows', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->foreignId('automation_trigger_id')->unique()->constrained('automation_triggers', 'id', 'fk_aw_trigger_id')->onDelete('cascade');
            $table->boolean('is_active')->default(true);
            $table->integer('total_runs')->default(0);
            $table->timestamps();
            
            // Add indexes for better performance
            $table->index(['is_active'], 'idx_aw_active');
            $table->index(['automation_trigger_id'], 'idx_aw_trigger_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('automation_workflows');
    }
};
