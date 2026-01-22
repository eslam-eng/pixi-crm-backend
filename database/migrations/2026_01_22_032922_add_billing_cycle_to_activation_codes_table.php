<?php

use App\Enums\Landlord\SubscriptionBillingCycleEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('activation_codes', function (Blueprint $table) {
            $table->enum('billing_cycle', SubscriptionBillingCycleEnum::values())->default(SubscriptionBillingCycleEnum::MONTHLY->value);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('activation_codes', function (Blueprint $table) {
            $table->dropColumn('billing_cycle');
        });
    }
};
