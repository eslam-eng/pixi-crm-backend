<?php

namespace Database\Seeders\Tenant;

use App\Enums\ApprovalStatusEnum;
use App\Enums\DiscountTypeEnum;
use App\Enums\PaymentStatusEnum;
use App\Models\Tenant\Deal;
use Illuminate\Database\Seeder;

class DealSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (Deal::count() > 0) return;

        $deal = Deal::updateOrCreate(
            ['deal_name' => 'Deal 1'],
            [
                'lead_id' => 1,
                'sale_date' => '2025-01-01',
                'discount_type' => DiscountTypeEnum::PERCENTAGE->value,
                'discount_value' => 10,
                'tax_rate' => 10,
                'payment_status' => PaymentStatusEnum::PAID->value,
                'payment_method_id' => 1,
                'notes' => 'Notes',
                'assigned_to_id' => 6,
                'total_amount' => 100,
                'partial_amount_paid' => 50,
                'amount_due' => 50,
                'approval_status' => ApprovalStatusEnum::APPROVED->value,
                'created_by_id' => 6,
            ]
        );
    }
}
