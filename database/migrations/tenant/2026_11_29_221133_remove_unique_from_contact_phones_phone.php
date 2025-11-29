<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contact_phones', function (Blueprint $table) {
            // Drop the unique index
            $table->dropUnique('contact_phones_phone_unique');
        });
    }

    public function down(): void
    {
        Schema::table('contact_phones', function (Blueprint $table) {
            // Re-add the unique index if rollback
            $table->unique('phone');
        });
    }
};
