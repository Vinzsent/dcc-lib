<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Standardizes the collation of the tables that get joined/compared across flows
 * to the application's configured default (utf8mb4_unicode_ci).
 *
 * Without this, cross-table comparisons such as
 *   transactions.accession_no = books_main.accession_no
 * fail with:
 *   SQLSTATE[HY000]: General error: 1267 Illegal mix of collations
 *   (utf8mb4_unicode_ci,IMPLICIT) and (utf8mb4_general_ci,IMPLICIT) for operation '='
 * which breaks the Transaction History and Library Reports pages.
 */
return new class extends Migration
{
    private const TARGET_COLLATION = 'utf8mb4_unicode_ci';
    private const TARGET_CHARSET = 'utf8mb4';

    private const TABLES = [
        'transactions',
        'books_main',
        'shelves',
    ];

    public function up(): void
    {
        // MariaDB/MySQL syntax. Guarded so non-MySQL drivers won't crash.
        $driver = DB::connection()->getDriverName();

        if (! in_array($driver, ['mysql', 'mariadb'], true)) {
            return;
        }

        foreach (self::TABLES as $table) {
            if (! Schema::hasTable($table)) {
                continue;
            }

            DB::statement(
                "ALTER TABLE `{$table}` CONVERT TO CHARACTER SET "
                . self::TARGET_CHARSET . ' COLLATE ' . self::TARGET_COLLATION
            );
        }
    }

    public function down(): void
    {
        // Collation standardization is not meaningfully reversible — the previous
        // mixed collation was itself the bug, so there is nothing safe to restore.
    }
};
