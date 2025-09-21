<?php

use App\Enums\CompanySizes;
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
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            // Basic Information
            $table->string('stripe_id')->nullable();
            $table->string('company_name')->unique();////
            $table->string('contact_name')->unique()->nullable();
            $table->string('contact_email')->unique();
            $table->string('contact_phone')->unique();
            $table->string('job_title')->nullable();
            $table->string('website')->nullable();
            $table->string('subdomain')->unique();
            //// company info
            $table->enum('company_size', CompanySizes::values())->nullable();
            $table->string('industry')->nullable();

            // Location Information
            $table->foreignId('city_id')->constrained('cities')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('address')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {   
        Schema::dropIfExists('clients');
    }
};
