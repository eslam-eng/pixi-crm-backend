<?php

namespace Database\Seeders\tenant;

use App\Models\Tenant\PaymentMethod;
use Illuminate\Database\Seeder;

class PaymentMethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Default payment methods (pre-defined)
        PaymentMethod::create([
            'name' => 'Cash',
            'is_checked' => true,
            'is_default' => true,
            'is_manual_added' => false,
        ]);
        
        PaymentMethod::create([
            'name' => 'Bank Transfer',
            'is_checked' => true,
            'is_default' => false,
            'is_manual_added' => false,
        ]);
        
        PaymentMethod::create([
            'name' => 'Crypto',
            'is_checked' => true,
            'is_default' => false,
            'is_manual_added' => false,
        ]);
        
        PaymentMethod::create([
            'name' => 'Card',
            'is_checked' => true,
            'is_default' => false,
            'is_manual_added' => false,
        ]);
        
        PaymentMethod::create([
            'name' => 'Check',
            'is_checked' => true,
            'is_default' => false,
            'is_manual_added' => false,
        ]);
        
        PaymentMethod::create([
            'name' => 'Paypal',
            'is_checked' => true,
            'is_default' => false,
            'is_manual_added' => false,
        ]);
    }
}
