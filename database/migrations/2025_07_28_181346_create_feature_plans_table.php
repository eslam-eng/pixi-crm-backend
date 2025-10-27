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
        Schema::create('feature_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plan_id')->constrained()->onDelete('cascade');
            $table->foreignId('feature_id')->constrained()->onDelete('cascade');
            $table->string('value'); // "true", "10", "1000", etc.
            $table->boolean('is_unlimited')->default(\App\Enums\Landlord\ActivationStatusEnum::INACTIVE->value);
            $table->timestamps();
            $table->unique(['plan_id', 'feature_id']);
            $table->index('plan_id'); // For quick plan feature lookups
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('feature_plans');
    }
};
