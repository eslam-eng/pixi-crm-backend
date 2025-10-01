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
        Schema::create('attendance_punches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['in', 'out']);              // click-in / click-out
            $table->timestamp('happened_at');                // UTC
            $table->string('source')->nullable();            // web, mobile, kiosk
            $table->string('ip')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();   // ~1cm precision
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('user_agent')->nullable();
            $table->uuid('request_uuid')->nullable();        // idempotency key
            $table->timestamps();

            $table->index(['latitude', 'longitude']);
            $table->index(['user_id', 'happened_at']);
            $table->unique(['user_id', 'request_uuid']);      // optional
        });

        Schema::create('attendance_days', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->date('work_date');                       // in user's local tz (e.g., Africa/Cairo)
            $table->integer('total_minutes')->default(0);    // worked minutes for that date
            $table->integer('paid_break_minutes')->default(0);
            $table->integer('unpaid_break_minutes')->default(0);
            $table->json('intervals')->nullable();           // [["in":"08:59","out":"13:01"], ...]
            $table->enum('status', ['open', 'closed', 'approved'])->default('open');
            $table->timestamps();

            $table->unique(['user_id', 'work_date']);
            $table->index(['work_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance_punches');
        Schema::dropIfExists('attendance_days');
    }
};
