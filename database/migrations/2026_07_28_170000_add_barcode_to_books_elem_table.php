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
        if (!Schema::hasTable('books_elem')) {
            Schema::create('books_elem', function (Blueprint $table) {
                $table->string('accession_number')->primary();
                $table->string('barcode')->nullable()->unique();
                $table->string('call_number');
                $table->string('title');
                $table->string('author');
            });

            return;
        }

        if (!Schema::hasColumn('books_elem', 'barcode')) {
            Schema::table('books_elem', function (Blueprint $table) {
                $table->string('barcode')->nullable()->after('accession_number')->unique();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('books_elem') && Schema::hasColumn('books_elem', 'barcode')) {
            Schema::table('books_elem', function (Blueprint $table) {
                $table->dropColumn('barcode');
            });
        }
    }
};
