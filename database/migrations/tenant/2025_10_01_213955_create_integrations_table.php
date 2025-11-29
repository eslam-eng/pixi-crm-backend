<?php

use App\Enums\IntegrationStatusEnum;
use Database\Seeders\IntegrationSeeder;
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
        Schema::create('integrations', function (Blueprint $table) {
            $table->id();
            $table->string('name'); 
            $table->string('platform')->nullable();
            $table->longText('access_token')->nullable();
            $table->longText('refresh_token')->nullable();
            $table->timestamp('token_expires_at')->nullable();
            $table->timestamp('last_sync')->nullable();
            $table->enum('status', IntegrationStatusEnum::values())->default(IntegrationStatusEnum::DISCONNECTED->value);
            $table->boolean('is_active')->default(false);
            $table->timestamps();
        });
        
        // Seed default integrations
        (new IntegrationSeeder())->run();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('integrations');
    }
};
