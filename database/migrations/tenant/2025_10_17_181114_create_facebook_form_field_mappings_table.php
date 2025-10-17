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
        Schema::create('facebook_form_field_mappings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('form_id')->references('id')->on('facebook_form_mappings')->onDelete('cascade');
            $table->string('facebook_field_key');
            $table->string('contact_column');
            $table->boolean('is_required')->default(false);
            $table->timestamps();

            
            // Indexes
            $table->index('form_id');
            $table->unique(['form_id', 'contact_column']); // Prevent duplicate mappings for same form
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('facebook_form_field_mappings');
    }
};