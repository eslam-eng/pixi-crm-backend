<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('custom_fields', function (Blueprint $table) {
            $table->id();
            $table->string('module')->index(); // 'contacts', 'leads', 'deals', 'tasks'
            $table->foreignId('form_section_id')->constrained('form_sections')->onDelete('cascade');
            $table->string('type'); // text, select, image, etc.
            $table->string('name');
            $table->json('label'); // Translatable
            $table->json('placeholder')->nullable(); // Translatable
            $table->json('help_text')->nullable(); // Translatable
            $table->json('options')->nullable(); // For select/radio
            $table->json('validation_rules')->nullable();
            $table->boolean('is_required')->default(false);
            $table->boolean('is_active')->default(true);
            $table->integer('ordering')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('custom_fields');
    }
};
