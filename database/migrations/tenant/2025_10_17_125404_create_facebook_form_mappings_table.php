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
        Schema::create('facebook_form_mappings', function (Blueprint $table) {
            $table->id();
            $table->string('facebook_form_id')->unique();
            $table->string('form_name');
            $table->integer('total_contacts_count')->default(0);
            $table->timestamps();
            
            $table->index('facebook_form_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('facebook_form_mappings');
    }
};