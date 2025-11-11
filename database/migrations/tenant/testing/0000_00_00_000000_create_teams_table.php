<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Schema::create('teams', function (Blueprint $table) {
        //     $table->id();
        //     $table->string('title');
        //     $table->unsignedBigInteger('leader_id')->nullable(); // no ->constrained() yet
        //     $table->timestamps();

        //     $table->index('leader_id');
        // });
    }

    public function down(): void
    {
        Schema::dropIfExists('teams');
    }
};
