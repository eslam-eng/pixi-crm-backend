<?php

use App\Enums\Landlord\ActivationCodeStatusEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('discount_codes', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        Schema::table('discount_codes', function (Blueprint $table) {
            $table->enum('status', ActivationCodeStatusEnum::values())
                ->default(ActivationCodeStatusEnum::AVAILABLE->value)
                ->after('expires_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('discount_codes', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        Schema::table('discount_codes', function (Blueprint $table) {
            $table->tinyInteger('status')->default(1)->after('expires_at');
        });
    }
};
