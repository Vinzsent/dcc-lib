<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('book_types')) {
            Schema::create('book_types', function (Blueprint $table) {
                $table->id();
                $table->string('name')->unique();
                $table->text('description')->nullable();
                $table->enum('status', ['ACTIVE', 'INACTIVE'])->default('ACTIVE');
                $table->timestamps();
            });

            // Seed default book sections
            $defaults = [
                ['name' => 'Reserved', 'description' => 'Reserved books collection for limited in-library or short-term loan', 'status' => 'ACTIVE'],
                ['name' => 'Filipiniana', 'description' => 'Philippine publications and materials on the Philippines', 'status' => 'ACTIVE'],
                ['name' => 'Circulation', 'description' => 'General collection books available for standard regular checkout', 'status' => 'ACTIVE'],
                ['name' => 'Fiction', 'description' => 'Novels, storybooks, and literary fiction materials', 'status' => 'ACTIVE'],
                ['name' => 'Thesis & Dissertation', 'description' => 'Academic research papers, theses, and dissertations', 'status' => 'ACTIVE'],
            ];

            foreach ($defaults as $item) {
                DB::table('book_types')->insert([
                    'name' => $item['name'],
                    'description' => $item['description'],
                    'status' => $item['status'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('book_types');
    }
};
