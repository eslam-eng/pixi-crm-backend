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
        Schema::create('report_executions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('report_id')->constrained('reports')->onDelete('cascade');
            $table->foreignId('executed_by_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('status')->default('pending'); // pending, running, completed, failed
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->integer('execution_time')->nullable(); // in seconds
            $table->integer('records_processed')->nullable();
            $table->string('file_path')->nullable();
            $table->bigInteger('file_size')->nullable(); // in bytes
            $table->text('error_message')->nullable();
            $table->json('parameters')->nullable(); // execution parameters
            $table->timestamps();

            $table->index(['report_id', 'status']);
            $table->index(['executed_by_id', 'created_at']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('report_executions');
    }
};
