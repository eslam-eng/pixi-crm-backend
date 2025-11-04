<?php

namespace Database\Seeders;

use App\Enums\Landlord\InvoiceStatusEnum;
use App\Enums\Landlord\PaymentMethodEnum;
use App\Models\Central\Invoice;
use App\Models\Central\InvoiceItem;
use App\Models\Central\Plan;
use App\Models\Central\Subscription;
use App\Models\Central\Tenant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class InvoiceTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $invoices = [
            [
                'tenant_id' => Tenant::query()->inRandomOrder()->first()->id,
                'subscription_id' => Subscription::query()->inRandomOrder()->first()->id,
                'subtotal' => 500,
                'tax_amount' => 0,
                'discount_percentage' => 0,
                'total' => 500,
                'status' => InvoiceStatusEnum::PAID->value,
                'paid_at' => now(),
                'payment_method' => PaymentMethodEnum::ACTIVATION_CODE->value,
            ],
            [
                'tenant_id' => Tenant::query()->inRandomOrder()->first()->id,
                'subscription_id' => Subscription::query()->inRandomOrder()->first()->id,
                'subtotal' => 299,
                'tax_amount' => 0,
                'discount_percentage' => 0,
                'total' => 299,
                'status' => InvoiceStatusEnum::PAID->value,
                'paid_at' => now(),
                'payment_method' => PaymentMethodEnum::CARD->value,
                'payment_reference' => Str::uuid(),
            ],
        ];

        foreach ($invoices as $invoiceData) {
            $invoiceData['invoice_number'] = Invoice::generateInvoiceNumber();

            $invoice = Invoice::create($invoiceData);
            $plan = Plan::query()->inRandomOrder()->first();
            $invoiceItem = [
                'invoice_id' => $invoice->id,
                'description' => "Plan {$plan->name}",
                'unit_price' => $invoice->total,
                'total' => $invoice->total,
            ];
            InvoiceItem::create($invoiceItem);
        }
    }
}
