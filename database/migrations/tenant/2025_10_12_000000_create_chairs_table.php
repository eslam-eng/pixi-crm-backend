<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('chairs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->date('started_at');
            $table->date('ended_at')->nullable();
            $table->timestamps();

            // Unique index for only one active chair per user in team
            $table->unique(['team_id', 'user_id', 'ended_at']);
        });

        // Partial index for active chairs (if using PostgreSQL)
        // DB::statement('CREATE UNIQUE INDEX chairs_active_unique ON chairs (team_id, user_id) WHERE ended_at IS NULL');
    }

    public function down()
    {
        Schema::dropIfExists('chairs');
    }
};
