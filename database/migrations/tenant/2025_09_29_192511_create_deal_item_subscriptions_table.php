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
        Schema::create('deal_item_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('deal_item_id')->constrained('deal_items')->onDelete('cascade');
            $table->date('start_at');
            $table->date('end_at');
            $table->enum('billing_cycle', ['monthly', 'quarterly', 'yearly']);
            $table->timestamps();
            
            // Add indexes for better performance
            $table->index(['deal_item_id', 'start_at']);
            $table->index(['billing_cycle']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deal_item_subscriptions');
    }
};