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
        Schema::create('feature_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('subscription_id')->constrained('subscriptions')->cascadeOnDelete(); // ->constrained('subscriptions')
            $table->integer('feature_id');
            $table->string('slug');
            $table->json('name'); // as it will be translatable
            $table->enum('group', ['limit', 'feature']);
            $table->string('value');
            $table->unsignedBigInteger('usage')->default(0); // Track usage, e.g., number of accounts created
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('feature_subscriptions');
    }
};
