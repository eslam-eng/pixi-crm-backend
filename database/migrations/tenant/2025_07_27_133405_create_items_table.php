<?php

use App\Enums\ItemType;
use App\Enums\ServiceDuration;
use App\Enums\ServiceType;
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
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('sku')->nullable()->unique();
            $table->decimal('price', 10, 2)->nullable();
            $table->integer('stock')->nullable();
            $table->enum('service_type', ServiceType::values())->nullable();
            $table->enum('duration', ServiceDuration::values())->nullable();
            $table->foreignId('category_id')->constrained('item_categories');
            $table->enum('type', ItemType::values());
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
