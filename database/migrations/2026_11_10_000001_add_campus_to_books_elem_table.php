<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('books_elem', function (Blueprint $table) {
            $table->string('campus')->nullable()->after('author');
            $table->string('status')->default('Available')->after('campus');
        });

        // Every book in this table belongs to the BED Elementary campus.
        DB::table('books_elem')->whereNull('campus')->update(['campus' => 'DCC BED Elementary']);
        DB::table('books_elem')->whereNull('status')->update(['status' => 'Available']);
    }

    public function down(): void
    {
        Schema::table('books_elem', function (Blueprint $table) {
            $table->dropColumn('campus');
        });
    }
};
