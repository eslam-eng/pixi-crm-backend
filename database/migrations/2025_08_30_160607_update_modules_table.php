<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB; // Added this import for DB facade

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('modules', function (Blueprint $table) {
            $table->string('key')->unique();
            $table->string('group');
            $table->json('group_label');
            $table->tinyInteger('has_number_field')->default(0);
            $table->json('number_field_label')->nullable();
        });

        // Add new name_json column
        Schema::table('modules', function (Blueprint $table) {
            $table->json('name_json')->after('name');
        });

        // Migrate existing name data to JSON format
        $modules = DB::table('modules')->get();
        foreach ($modules as $module) {
            $nameData = [
                'en' => $module->name,
                'ar' => $module->name // You can update Arabic names later
            ];
            
            DB::table('modules')
                ->where('id', $module->id)
                ->update(['name_json' => json_encode($nameData)]);
        }

        // Drop old name column and rename name_json to name
        Schema::table('modules', function (Blueprint $table) {
            $table->dropColumn('name');
        });

        Schema::table('modules', function (Blueprint $table) {
            $table->renameColumn('name_json', 'name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('modules', function (Blueprint $table) {
            $table->dropColumn([
                'key',
                'group',
                'group_label',
                'has_number_field',
                'number_field_label'
            ]);
            
            // Change name back to string
            $table->string('name')->change();
        });
    }
};
