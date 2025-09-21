<?php

use App\Enums\CustomFieldModules;
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
        Schema::create('custom_fields', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type', ['text', 'number', 'date', 'dropdown']); // Field type
            $table->json('options')->nullable(); // Options for dropdown (if type is dropdown)
            $table->enum('module', CustomFieldModules::values())->default(CustomFieldModules::CONTACT->value);
            $table->boolean('is_required')->default(false);
            $table->boolean('is_active')->default(true);
            $table->string('label')->nullable();
            $table->string('placeholder')->nullable();
            $table->text('help_text')->nullable();
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
