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
        // Schema::create('departments', function (Blueprint $table) {
        //     $table->id();
        //     $table->json('name'); // Multilingual names: {en: "Sales", ar: "المبيعات", fr: "Ventes", es: "Ventas"}
        //     $table->text('description')->nullable();
        //     $table->boolean('is_active')->default(true);
        //     $table->timestamps();
        // });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('departments');
    }
};
