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
        Schema::table('students', function (Blueprint $table) {
            $table->string('campus')->nullable()->after('sid');
        });

        Schema::table('inouts', function (Blueprint $table) {
            $table->string('campus')->nullable()->after('sid');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn('campus');
        });

        Schema::table('inouts', function (Blueprint $table) {
            $table->dropColumn('campus');
        });
    }
};
