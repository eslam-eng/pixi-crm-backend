<?php
// database/migrations/create_forms_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('forms', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('slug')->unique();
            $table->boolean('is_active')->default(true);
            $table->integer('submissions_count')->default(0);
            $table->timestamps();
        });

        Schema::create('form_fields', function (Blueprint $table) {
            $table->id();
            $table->foreignId('form_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('label');
            $table->enum('type', ['text', 'email', 'textarea', 'select', 'checkbox', 'radio', 'number', 'file']);
            $table->json('options')->nullable(); // For select/radio/checkbox options
            $table->boolean('required')->default(false);
            $table->string('placeholder')->nullable();
            $table->integer('order')->default(0);
            $table->timestamps();
        });

        Schema::create('form_actions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('form_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['redirect', 'email', 'webhook']);
            $table->json('settings'); // Different settings for each action type
            $table->boolean('is_active')->default(true);
            $table->integer('order')->default(0);
            $table->timestamps();
        });

        Schema::create('form_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('form_id')->constrained()->cascadeOnDelete();
            $table->json('data'); // Form field values
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('form_submissions');
        Schema::dropIfExists('form_actions');
        Schema::dropIfExists('form_fields');
        Schema::dropIfExists('forms');
    }
};
