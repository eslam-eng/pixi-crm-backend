<?php

use App\Enums\DurationUnits;
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
        Schema::create('tiers', function (Blueprint $table) {
            $table->id();
            $table->string('package_name')->unique();
            $table->string('description')->nullable();
            $table->decimal('price', 10, 2)->default(0.00);
            $table->enum('duration_unit', DurationUnits::values());
            $table->integer('duration');
            $table->integer('refund_period');
            $table->integer('max_users')->nullable();
            $table->integer('max_contacts');
            $table->integer('storage_limit');
            $table->json('modules'); // Store array of module enum values
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->enum('availability', ['Public', 'Private'])->default('Public');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tiers');
    }
};
