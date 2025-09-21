<?php

use App\Enums\landlord\ActivitionMethods;
use App\Enums\landlord\PaymentStatus;
use App\Enums\landlord\SubscriptionsStatus;
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
        Schema::create('subscriptions_landlord', function (Blueprint $table) {
            $table->id();
 
            $table->foreignId('client_id')->constrained('clients')->nullable();
            $table->foreignId('tier_id')->constrained('tiers')->nullable();
            $table->date('subscription_start_date')->nullable();
            $table->date('subscription_end_date')->nullable();
            $table->enum('subscription_status', SubscriptionsStatus::values())->default(SubscriptionsStatus::ACTIVE);
            $table->enum('activition_method', ActivitionMethods::values())->default(ActivitionMethods::API);
            $table->string('source')->nullable();
            $table->enum('auto_renew', ['yes', 'no'])->default('no');
            $table->enum('payment_status', PaymentStatus::values())->default(PaymentStatus::UNPAID);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriptions_landlord');
    }
};
