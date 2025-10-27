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
        Schema::create('source_payout_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('source_payout_batch_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('activation_code_id')->constrained()->cascadeOnDelete();
            $table->decimal('payout_amount', 10, 2);
            $table->timestamp('collected_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('source_payout_items');
    }
};
