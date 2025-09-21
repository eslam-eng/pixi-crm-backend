<?php

use App\Enums\OpportunityStatus;
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
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contact_id')->constrained('contacts')->onDelete('cascade');
            // Opportunity Details
            $table->enum('status', OpportunityStatus::values())->default(OpportunityStatus::ACTIVE->value);
            $table->foreignId('stage_id')->constrained('stages')->onDelete('cascade');
            $table->decimal('deal_value', 10, 2);
            $table->decimal('win_probability', 10, 2);
            $table->timestamp('expected_close_date')->nullable();
            $table->foreignId('assigned_to_id')->nullable()->constrained('users')->onDelete('cascade'); // Required if status = 'lost'
            $table->text('notes')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};
