<?php

use App\Enums\Landlord\SubscriptionStatusEnum;
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
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('subscription_number')->unique();
            $table->foreignIdFor(Plan::class)->constrained();
            $table->tinyInteger('status')->comment('active,canceled,expired,..')->default(SubscriptionStatusEnum::PENDING->value);
            $table->timestamp('starts_at'); // When subscription becomes active
            $table->timestamp('ends_at')->nullable(); // When subscription expires
            $table->timestamp('trial_ends_at')->nullable(); // Trial period end
            $table->timestamp('cancelled_at')->nullable(); // When cancelled
            $table->decimal('amount', 10, 2); // Amount for this billing period
            $table->string('currency', 10)->default('USD');
            $table->boolean('auto_renew')->default(false);
            $table->string('billing_cycle')->nullable();
            $table->json('plan_snapshot');
            $table->integer('monthly_credit_tokens')->default(0);
            $table->foreignUuid('tenant_id')->constrained();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
