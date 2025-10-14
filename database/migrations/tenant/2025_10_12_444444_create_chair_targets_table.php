<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('chair_targets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chair_id')->constrained()->onDelete('cascade');
            $table->enum('period_type', ['monthly', 'quarterly', 'yearly']);
            $table->integer('year');
            $table->integer('period_number')->comment('1-12 monthly, 1-4 quarterly, 1 yearly');
            $table->decimal('target_value', 15, 2);
            $table->date('effective_from');
            $table->date('effective_to')->nullable();
            $table->timestamps();

            // Composite unique index
            $table->unique(['chair_id', 'period_type', 'year', 'period_number', 'effective_from'],'unique_chair_target');
        });
    }

    public function down()
    {
        Schema::dropIfExists('chair_targets');
    }
};
