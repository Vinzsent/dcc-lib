<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Check if eid column exists
        if (!Schema::hasColumn('employees', 'eid')) {
            // If eid column doesn't exist, add it
            Schema::table('employees', function (Blueprint $table) {
                $table->string('eid')->nullable()->after('id');
            });
        }
        
        // Update existing records to populate eid
        // Using DB::statement with database-specific syntax
        $connectionName = DB::connection()->getName();
        if ($connectionName === 'sqlite') {
            // SQLite uses || for string concatenation
            DB::statement('UPDATE "employees" SET "eid" = COALESCE(NULLIF(rfid, ""), \'EMP-\' || id) WHERE "eid" IS NULL');
        } else {
            // MySQL and other databases
            DB::statement('UPDATE "employees" SET "eid" = COALESCE(NULLIF(rfid, ""), CONCAT(\'EMP-\', id)) WHERE "eid" IS NULL');
        }

        // Ensure eid is required and has unique index
        // We need to modify the column to be not null and add unique index
        // But we should check current state first to avoid errors
        
        // Make eid not null (if it isn't already)
        try {
            Schema::table('employees', function (Blueprint $table) {
                $table->string('eid')->nullable(false)->change();
            });
        } catch (\Throwable $e) {
            // Column might already be not null, or there might be null values
            // If there are null values, we need to handle them first
            if (str_contains($e->getMessage(), 'not null')) {
                // Update any remaining null values
                $connectionName = DB::connection()->getName();
                if ($connectionName === 'sqlite') {
                    DB::table('employees')->whereNull('eid')->update(['eid' => DB::raw('\'EMP-\' || id')]);
                } else {
                    DB::table('employees')->whereNull('eid')->update(['eid' => DB::raw('CONCAT(\'EMP-\', id)')]);
                }
                // Now try again to make it not null
                Schema::table('employees', function (Blueprint $table) {
                    $table->string('eid')->nullable(false)->change();
                });
            }
        }
        
        // Add unique index if it doesn't exist
        try {
            Schema::table('employees', function (Blueprint $table) {
                $table->unique('eid');
            });
        } catch (\Throwable $e) {
            // Index might already exist
            if (!str_contains($e->getMessage(), 'Duplicate') && !str_contains($e->getMessage(), 'already exists')) {
                throw $e;
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            // Drop index first if present.
            try {
                $table->dropUnique('employees_eid_unique');
            } catch (\Throwable $e) {
                // Ignore if index doesn't exist
            }
            // Only drop the column if we added it (but we can't easily track that)
            // For safety, we'll check if it exists before dropping
            if (Schema::hasColumn('employees', 'eid')) {
                $table->dropColumn('eid');
            }
        });
    }
};
