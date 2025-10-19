<?php

use App\Enums\PlatformEnum;
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
        Schema::create('integrated_forms', function (Blueprint $table) {
            $table->id();
            $table->string('external_form_id')->unique();
            $table->string('form_name');
            $table->enum('platform', PlatformEnum::values())->default(PlatformEnum::META->value);
            $table->integer('total_contacts_count')->default(0);
            $table->boolean('is_active')->default(true); // Added is_active column
            $table->timestamps();
            
            $table->index('external_form_id');
            $table->index('platform');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('integrated_forms');
    }
};