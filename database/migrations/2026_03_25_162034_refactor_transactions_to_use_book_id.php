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
        Schema::table('transactions', function (Blueprint $table) {
            $table->unsignedBigInteger('book_id')->nullable()->after('borrower_type');
        });

        // Migrate data
        $transactions = DB::table('transactions')->get();
        foreach ($transactions as $txn) {
            $book = DB::table('books')->where('accession_no', $txn->accession_no)->first();
            if ($book) {
                DB::table('transactions')->where('id', $txn->id)->update(['book_id' => $book->id]);
            }
        }

        Schema::table('transactions', function (Blueprint $table) {
            $table->dropForeign(['accession_no']);
            $table->dropColumn('accession_no');
            $table->foreign('book_id')->references('id')->on('books')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->string('accession_no')->nullable()->after('borrower_type');
        });

        // Reverse data
        $transactions = DB::table('transactions')->get();
        foreach ($transactions as $txn) {
            $book = DB::table('books')->where('id', $txn->book_id)->first();
            if ($book) {
                DB::table('transactions')->where('id', $txn->id)->update(['accession_no' => $book->accession_no]);
            }
        }

        Schema::table('transactions', function (Blueprint $table) {
            $table->dropForeign(['book_id']);
            $table->dropColumn('book_id');
            $table->foreign('accession_no')->references('accession_no')->on('books')->onDelete('cascade');
        });
    }
};
