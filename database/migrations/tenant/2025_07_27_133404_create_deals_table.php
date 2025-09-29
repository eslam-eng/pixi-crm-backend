<?php

use App\Enums\ApprovalStatusEnum;
use App\Enums\DealTypeEnum;
use App\Enums\DiscountTypeEnum;
use App\Enums\PaymentStatusEnum;
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
        Schema::create('deals', function (Blueprint $table) {
            $table->id();

            // basic info
            $table->string('deal_name');
            $table->foreignId('lead_id')->constrained('leads');
            $table->date('sale_date');


            // tax info & discount
            $table->enum('discount_type', DiscountTypeEnum::values())->nullable();
            $table->decimal('discount_value', 10, 2)->nullable();
            $table->decimal('tax_rate', 10, 2);

            // Assignment
            $table->foreignId('assigned_to_id')->constrained('users');
            $table->enum('payment_status', PaymentStatusEnum::values());
            $table->foreignId('payment_method_id')->constrained('payment_methods');

            // total amount
            $table->decimal('total_amount', 10, 2)->default(0);
            
            $table->decimal('partial_amount_paid', 10, 2)->default(0);
            $table->decimal('amount_due', 10, 2)->default(0);


            $table->enum('approval_status', ApprovalStatusEnum::values())->default(ApprovalStatusEnum::PENDING->value);
            $table->foreignId('created_by_id')->constrained('users')->restrictOnDelete();

            // Notes
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deals');
    }
};
