<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('teams', function (Blueprint $table) {
            $table->string('description')->nullable();
            $table->string('status')->default('active');
            $table->string('period_type')->nullable();
            $table->boolean('leader_has_target')->default(false);
        });
    }

    public function down(): void
    {
        Schema::table('teams', function (Blueprint $table) {
            $table->dropColumn('description');
            $table->dropColumn('status');
            $table->dropColumn('period_type');
            $table->dropColumn('leader_has_target');
        });
    }
};
