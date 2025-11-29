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
        Schema::create('integrated_form_fields_mapping', function (Blueprint $table) {
            $table->id();
            $table->foreignId('form_id')->references('id')->on('integrated_forms')->onDelete('cascade');
            $table->string('external_field_key');
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
        Schema::dropIfExists('integrated_form_fields_mapping');
    }
};