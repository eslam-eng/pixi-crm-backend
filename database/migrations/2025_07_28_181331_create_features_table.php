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
        Schema::create('features', function (Blueprint $table) {
            $table->id();
            $table->string('slug');
            $table->json('name'); // as it will be translatable
            $table->tinyInteger('group')->comment('is features for limits or for modules values from enum Feature Group'); // limit = numeric quota, feature = boolean
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->softDeletes();
            $table->timestamps();
            // Virtual column that is null if deleted_at is not null
            $table->string('active_slug')->virtualAs('IF(deleted_at IS NULL, slug, NULL)');

            // Unique constraint on the virtual column
            $table->unique('active_slug');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('features');
    }
};
