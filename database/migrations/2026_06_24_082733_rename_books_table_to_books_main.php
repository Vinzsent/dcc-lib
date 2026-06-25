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
        // Drop the foreign key on transactions referencing books if it exists
        try {
            Schema::table('transactions', function (Blueprint $table) {
                $table->dropForeign(['book_id']);
            });
        } catch (\Throwable $e) {
            // Ignore if it doesn't exist
        }

        // Rename the books table to books_main if books exists and books_main doesn't
        if (Schema::hasTable('books') && !Schema::hasTable('books_main')) {
            Schema::rename('books', 'books_main');
        }

        // Recreate the foreign key pointing to books_main
        try {
            Schema::table('transactions', function (Blueprint $table) {
                $table->foreign('book_id')
                      ->references('id')
                      ->on('books_main')
                      ->onDelete('cascade')
                      ->onUpdate('cascade');
            });
        } catch (\Throwable $e) {
            // Ignore if foreign key creation fails
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop the foreign key referencing books_main
        try {
            Schema::table('transactions', function (Blueprint $table) {
                $table->dropForeign(['book_id']);
            });
        } catch (\Throwable $e) {
            // Ignore if it doesn't exist
        }

        // Rename back to books
        Schema::rename('books_main', 'books');

        // Recreate the foreign key pointing to books
        try {
            Schema::table('transactions', function (Blueprint $table) {
                $table->foreign('book_id')
                      ->references('id')
                      ->on('books')
                      ->onDelete('cascade')
                      ->onUpdate('cascade');
            });
        } catch (\Throwable $e) {
            // Ignore if foreign key creation fails
        }
    }
};
