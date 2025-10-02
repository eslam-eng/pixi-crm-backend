<?php

use App\Enums\TargetType;
use App\Enums\UserType;
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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('image')->nullable();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique();
            $table->string('job_title')->nullable();
            $table->foreignId('team_id')->nullable()->constrained('teams');
            $table->enum('target_type', TargetType::values())->default(TargetType::NONE);
            $table->float('target')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('lang')->default('en');
            $table->timestamp('last_login_at')->nullable();
            $table->string('phone')->nullable();
            $table->foreignId('department_id')->nullable()->constrained('departments');
            $table->boolean('is_active')->default(1);
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::table('teams', function (Blueprint $table) {
            $table->foreign('leader_id')
                ->references('id')->on('users')
                ->nullOnDelete(); // or ->cascadeOnDelete()
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
