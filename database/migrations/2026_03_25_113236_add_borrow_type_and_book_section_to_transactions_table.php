<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->string('borrow_type')->nullable()->after('borrower_type'); // Student, Faculty, Staff
            $table->string('book_section')->nullable()->after('borrow_type'); // Reserved, Filipiniana, etc.
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn(['borrow_type', 'book_section']);
        });
    }
};
