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
        \DB::table('students')->where('campus', 'DCC Main')->update(['campus' => 'DCC TED']);
        \DB::table('inouts')->where('campus', 'DCC Main')->update(['campus' => 'DCC TED']);
        \DB::table('employees')->where('campus', 'DCC Main')->update(['campus' => 'DCC TED']);
        \DB::table('employee_logs')->where('campus', 'DCC Main')->update(['campus' => 'DCC TED']);
        \DB::table('books_main')->where('campus', 'DCC Main')->update(['campus' => 'DCC TED']);
        \DB::table('shelves')->where('campus', 'DCC Main')->update(['campus' => 'DCC TED']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        \DB::table('students')->where('campus', 'DCC TED')->update(['campus' => 'DCC Main']);
        \DB::table('inouts')->where('campus', 'DCC TED')->update(['campus' => 'DCC Main']);
        \DB::table('employees')->where('campus', 'DCC TED')->update(['campus' => 'DCC Main']);
        \DB::table('employee_logs')->where('campus', 'DCC TED')->update(['campus' => 'DCC Main']);
        \DB::table('books_main')->where('campus', 'DCC TED')->update(['campus' => 'DCC Main']);
        \DB::table('shelves')->where('campus', 'DCC TED')->update(['campus' => 'DCC Main']);
    }
};
