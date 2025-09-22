<?php

use App\Enums\CompanySizes;
use App\Enums\ContactMethods;
use App\Enums\ContactStatus;
use App\Enums\IdenticalContactType;
use App\Enums\IndustryStatus;
use App\Enums\MergeContactType;
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
        Schema::create('contacts', function (Blueprint $table) {
            $table->id();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('email')->unique()->nullable();
            $table->string('job_title')->nullable();
            $table->string('department')->nullable();
            $table->enum('status', ContactStatus::values())->default(ContactStatus::ACTIVE->value)->nullable();
            $table->foreignId('source_id')->nullable()->constrained('sources');
            $table->enum('contact_method', ContactMethods::values())->nullable();
            $table->boolean('email_permission')->nullable()->default(0);
            $table->boolean('phone_permission')->nullable()->default(0);
            $table->boolean('whatsapp_permission')->nullable()->default(0);
            $table->string('company_name')->nullable();
            $table->string('campaign_name')->nullable();
            $table->string('website')->nullable();
            $table->enum('industry', IndustryStatus::values())->nullable();
            $table->enum('company_size', CompanySizes::values())->nullable();
            $table->string('address')->nullable();
            $table->foreignId('country_id')->nullable()->constrained('countries')->onDelete('cascade');
            $table->foreignId('city_id')->nullable()->constrained('cities')->onDelete('cascade');
            $table->string('state')->nullable();
            $table->string('zip_code')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->json('tags')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('contact_merges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contact_id')->constrained('contacts')->onDelete('cascade');
            $table->enum('identical_contact_type', IdenticalContactType::values())->nullable();
            $table->enum('merge_status', MergeContactType::values())->default(MergeContactType::PENDING->value)->nullable();
            $table->string('contact_phone')->nullable();
            // contact info
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('email')->nullable();
            $table->string('job_title')->nullable();
            $table->string('department')->nullable();
            $table->enum('status', ContactStatus::values())->default(ContactStatus::ACTIVE->value)->nullable();
            $table->foreignId('source_id')->nullable()->constrained('sources');
            $table->enum('contact_method', ContactMethods::values())->nullable();
            $table->boolean('email_permission')->nullable()->default(0);
            $table->boolean('phone_permission')->nullable()->default(0);
            $table->boolean('whatsapp_permission')->nullable()->default(0);
            $table->string('company_name')->nullable();
            $table->string('campaign_name')->nullable();
            $table->string('website')->nullable();
            $table->enum('industry', IndustryStatus::values())->nullable();
            $table->enum('company_size', CompanySizes::values())->nullable();
            $table->string('address')->nullable();
            $table->foreignId('country_id')->nullable()->constrained('countries')->onDelete('cascade');
            $table->foreignId('city_id')->nullable()->constrained('cities')->onDelete('cascade');
            $table->string('state')->nullable();
            $table->string('zip_code')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->json('tags')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contacts');
        Schema::dropIfExists('contact_merges');
    }
};
