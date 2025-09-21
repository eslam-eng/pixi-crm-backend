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
        Schema::create('loss_reasons', function (Blueprint $table) {
            $table->id();
            $table->string('label');
            $table->string('value');
            $table->string('description')->nullable();
            $table->foreignId('pipeline_id')->constrained('pipelines');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loss_reasons');
    }
};
