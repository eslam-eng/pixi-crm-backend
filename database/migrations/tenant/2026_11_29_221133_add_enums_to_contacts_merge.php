<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE contact_merges MODIFY merge_status ENUM('pending', 'merged', 'ignored', 'duplicated')");
    }
};
