<?php

use App\Enums\Landlord\ActivationCodeStatusEnum;
use App\Models\Central\Plan;
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
        Schema::create('activation_codes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('code')->unique();
            $table->foreignId('source_id')->constrained()->cascadeOnDelete();
            $table->integer('validity_days')->default(1);
            $table->string('status')->default(ActivationCodeStatusEnum::AVAILABLE->value);
            $table->foreignIdFor(Plan::class)->constrained()->cascadeOnDelete();
            $table->date('expired_at')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('redeemed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activation_codes');
    }
};
