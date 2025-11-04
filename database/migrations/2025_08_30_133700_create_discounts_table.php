<?php

use App\Enums\Landlord\ActivationStatusEnum;
use App\Enums\Landlord\DiscountUsageEnum;
use App\Models\Central\Plan;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('discount_codes', function (Blueprint $table) {
            $table->id();
            $table->string('discount_code')->unique();
            $table->foreignIdFor(Plan::class)->constrained()->cascadeOnDelete();
            $table->string('discount_type', 100)->default(DiscountUsageEnum::SINGLE_USE->value);
            $table->decimal('discount_percentage', 5, 2);
            $table->unsignedInteger('users_limit')->nullable();
            $table->unsignedInteger('usage_limit')->nullable();
            $table->dateTime('expires_at')->nullable();
            $table->tinyInteger('status')->default(ActivationStatusEnum::ACTIVE->value);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('discount_codes');
    }
};
