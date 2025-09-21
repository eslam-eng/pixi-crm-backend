<?php

use App\Enums\ItemType;
use App\Enums\ServiceDuration;
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
        Schema::create('item_variants_attribute_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('variant_id')->constrained('item_variants');
            $table->foreignId('item_attribute_id')->constrained('item_attributes');
            $table->foreignId('item_attribute_value_id')->constrained('item_attribute_values');
            $table->timestamps();

            $table->unique(['variant_id', 'item_attribute_id'], 'variant_attribute_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_variants_attribute_values');
    }
};
