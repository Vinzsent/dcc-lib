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
        Schema::create('researches', function (Blueprint $table) {
            $table->string('accession_no')->primary();
            $table->string('barcode')->nullable()->unique();
            $table->string('title');
            $table->string('author');
            $table->string('call_number');
            $table->string('location')->nullable();
            $table->string('shelf_number')->nullable();
            $table->enum('status', ['Available', 'Borrowed'])->default('Available');
            $table->string('campus')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('researches');
    }
};