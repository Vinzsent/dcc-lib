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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('eid')->unique(); // Employee ID
            $table->string('campus')->nullable();
            $table->string('rfid')->unique()->nullable();

            $table->string('firstname');
            $table->string('middlename')->nullable();
            $table->string('lastname');

            $table->string('department')->nullable();
            $table->string('position')->nullable();
            $table->string('employment_type')->nullable(); // Full-time, Part-time, Contract, etc.

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
