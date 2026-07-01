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
        Schema::table('transactions', function (Blueprint $table) {
            $table->dateTime('date_borrowed')->change();
            $table->dateTime('due_date')->change();
            $table->dateTime('date_returned')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->date('date_borrowed')->change();
            $table->date('due_date')->change();
            $table->date('date_returned')->change();
        });
    }
};
