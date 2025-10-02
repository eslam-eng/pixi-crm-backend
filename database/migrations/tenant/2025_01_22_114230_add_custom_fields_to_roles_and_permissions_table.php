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

        // Check if columns don't exist before adding them
        if (!Schema::hasColumn('roles', 'description')) {
            Schema::table('roles', function (Blueprint $table) {
                $table->text('description')->nullable()->after('name');
            });
        }

        if (!Schema::hasColumn('roles', 'is_system')) {
            Schema::table('roles', function (Blueprint $table) {
                $table->boolean('is_system')->default(false)->after('guard_name');
            });
        }

        // Add custom fields to permissions table
        if (!Schema::hasColumn('permissions', 'group')) {
            Schema::table('permissions', function (Blueprint $table) {
                $table->string('group')->nullable()->after('name');
            });
        }

        if (!Schema::hasColumn('permissions', 'description')) {
            Schema::table('permissions', function (Blueprint $table) {
                $table->text('description')->nullable()->after('group');
            });
        }

        // Add indexes for better performance
        Schema::table('roles', function (Blueprint $table) {
            if (!$this->hasIndex('roles', 'roles_is_system_index')) {
                $table->index('is_system');
            }
        });

        Schema::table('permissions', function (Blueprint $table) {
            if (!$this->hasIndex('permissions', 'permissions_group_index')) {
                $table->index('group');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('roles', function (Blueprint $table) {
            // Drop indexes first
            if ($this->hasIndex('roles', 'roles_is_system_index')) {
                $table->dropIndex('roles_is_system_index');
            }

            // Then drop columns if they exist
            $columns = ['description', 'is_system'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('roles', $column)) {
                    $table->dropColumn($column);
                }
            }
        });

        Schema::table('permissions', function (Blueprint $table) {
            // Drop index first
            if ($this->hasIndex('permissions', 'permissions_group_index')) {
                $table->dropIndex('permissions_group_index');
            }

            // Then drop columns if they exist
            $columns = ['group', 'description'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('permissions', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }

    /**
     * Check if table has index
     */
    private function hasIndex(string $table, string $index): bool
    {
        $indexes = Schema::getConnection()
            ->getSchemaBuilder()
            ->getIndexes($table);

        return array_key_exists($index, $indexes);
    }
};
