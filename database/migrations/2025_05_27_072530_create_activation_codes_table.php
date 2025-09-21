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
        Schema::create('activation_codes', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->foreignId('tier_id')->constrained()->onDelete('cascade');
            $table->string('source');
            $table->string('status');
            $table->integer('trial_days');
            $table->timestamp('expires_at');
            $table->timestamp('used_at')->nullable();
            $table->softDeletes(); // For soft delete functionality
            $table->foreignId('create_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activation_codes');
    }
};
