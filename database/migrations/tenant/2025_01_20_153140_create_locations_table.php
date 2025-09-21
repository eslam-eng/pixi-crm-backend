<?php

use App\Enums\ActivationStatus;
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
        Schema::create('locations', function (Blueprint $table) {
            $table->id();
            $table->text('title');
            $table->enum('status', ActivationStatus::values())->default(ActivationStatus::INACTIVE->value);
            $table->unsignedInteger('_lft');
            $table->unsignedInteger('_rgt');
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->timestamps();

            $table->foreign('parent_id')
            ->references('id')
            ->on('locations')
            ->onDelete('RESTRICT')
            ->onUpdate('RESTRICT'); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('locations');
    }
};
