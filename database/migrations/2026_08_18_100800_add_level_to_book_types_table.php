<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('book_types') && !Schema::hasColumn('book_types', 'level')) {
            Schema::table('book_types', function (Blueprint $table) {
                $table->string('level', 100)->default('All')->after('name');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('book_types') && Schema::hasColumn('book_types', 'level')) {
            Schema::table('book_types', function (Blueprint $table) {
                $table->dropColumn('level');
            });
        }
    }
};
