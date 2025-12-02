<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('contacts', function (Blueprint $table) {
            // 1) Drop existing string column
            $table->dropColumn('department');
        });

        Schema::table('contacts', function (Blueprint $table) {
            // 2) Add new unsigned big integer with FK
            $table->foreignId('department_id')->after('job_title')
                ->nullable()
                ->constrained('departments')
                ->nullOnDelete();
        });
    }

    public function down()
    {
        Schema::table('contacts', function (Blueprint $table) {
            // Reverse change
            $table->dropForeign(['department_id']);
            $table->dropColumn('department_id');

            $table->string('department')->nullable();
        });
    }
};
