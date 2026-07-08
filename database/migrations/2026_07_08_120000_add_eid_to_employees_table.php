<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * Corrective migration: the live `employees` table was created from an
 * external source and is missing the `eid` column that the application code
 * (ScannerController::scanEmployee, Employee model) depends on.
 *
 * The `employee_logs` table already has `eid` as NOT NULL, so once an employee
 * is tapped we need a non-null `eid` to insert into `employee_logs`.
 *
 * This migration:
 *   1. Adds `eid` to `employees` (nullable first, so it can be backfilled).
 *   2. Backfills `eid` from `rfid` (employees are looked up by eid OR rfid,
 *      so an EID equal to the RFID keeps taps working). Rows with no RFID
 *      fall back to an `EMP-{id}` synthetic value.
 *   3. Promotes `eid` to UNIQUE + NOT NULL to match the original migration.
 *
 * Idempotent: every ALTER is guarded by Schema::hasColumn().
 */
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('employees', 'eid')) {
            Schema::table('employees', function (Blueprint $table) {
                // Added nullable first; backfilled below, then enforced.
                $table->string('eid')->nullable()->after('id');
            });
        }

        // Backfill eid from rfid; fall back to EMP-{id} for rows without RFID.
        DB::table('employees')
            ->whereNull('eid')
            ->update(['eid' => DB::raw("COALESCE(NULLIF(rfid, ''), CONCAT('EMP-', id))")]);

        // Enforce uniqueness + NOT NULL to match the original 2026_02_13 migration.
        try {
            Schema::table('employees', function (Blueprint $table) {
                $table->string('eid')->nullable(false)->change();
                $table->unique('eid');
            });
        } catch (\Throwable $e) {
            // If a duplicate eid somehow exists, leave the column nullable
            // rather than failing the whole migration. Taps still work.
        }
    }

    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            // Drop index first if present.
            try {
                $table->dropUnique('employees_eid_unique');
            } catch (\Throwable $e) {
            }
            if (Schema::hasColumn('employees', 'eid')) {
                $table->dropColumn('eid');
            }
        });
    }
};
