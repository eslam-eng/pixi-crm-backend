<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            // REMOVE columns
            $table->dropColumn([
                'currency_code',
                'trial_days'
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            // ROLLBACK added columns
            $table->string('currency_code');
            $table->integer('trial_days');
        });
    }
};
