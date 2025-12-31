<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->string('zapier_api_key', 64)->nullable()->unique()->after('data');
        });

        // Seed existing tenants with a random API key
        $tenants = \Illuminate\Support\Facades\DB::table('tenants')->get();
        foreach ($tenants as $tenant) {
            \Illuminate\Support\Facades\DB::table('tenants')
                ->where('id', $tenant->id)
                ->update(['zapier_api_key' => \Illuminate\Support\Str::random(32)]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn('zapier_api_key');
        });
    }
};
