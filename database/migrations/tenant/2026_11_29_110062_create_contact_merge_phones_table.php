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
        Schema::create('contact_merge_phones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contact_merge_id')->constrained('contact_merges')->onDelete('cascade');
            $table->boolean('is_primary')->default(false);
            $table->boolean('enable_whatsapp')->default(false);
            $table->string('phone');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contact_merge_phones');
    }
};
