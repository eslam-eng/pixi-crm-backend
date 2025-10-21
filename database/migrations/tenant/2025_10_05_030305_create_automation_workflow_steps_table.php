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
        Schema::create('automation_workflow_steps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('automation_workflow_id')->constrained('automation_workflows', 'id', 'fk_aws_workflow_id')->onDelete('cascade');
            $table->enum('type', ['condition', 'action', 'delay']);
            $table->integer('order')->default(0);
            $table->timestamps();
            
            // Add indexes for better performance
            $table->index(['automation_workflow_id'], 'idx_aws_workflow_id');
            $table->index(['type'], 'idx_aws_type');
            $table->index(['order'], 'idx_aws_order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('automation_workflow_steps');
    }
};
