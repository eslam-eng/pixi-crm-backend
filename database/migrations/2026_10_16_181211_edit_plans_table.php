<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            // ADD columns
            $table->decimal('price')->nullable()->after('description');
            $table->enum('duration_unit', ['days', 'months', 'years', 'lifetime'])->nullable()->after('description');
            $table->decimal('duration')->nullable()->after('description');
            $table->decimal('refund_period')->nullable()->after('description');

            // REMOVE columns
            $table->dropColumn([
                'currency_code', 
                'refund_days', 
                'sort_order',
                'monthly_price', 
                'annual_price', 
                'lifetime_price', 
                'trial_days'
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            // ROLLBACK added columns
            $table->dropColumn(['price', 'duration_unit', 'duration', 'refund_period']);
        });
    }
};
