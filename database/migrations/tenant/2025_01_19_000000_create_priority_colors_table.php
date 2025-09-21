<?php

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
        Schema::create('priority_colors', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('hex_code')->unique();
            $table->timestamps();
        });

        \Artisan::call('tenants:seed', ['--class' => \Database\Seeders\Tenant\PriorityColorSeeder::class]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('priority_colors');
    }
};
