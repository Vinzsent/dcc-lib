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
        Schema::create('employee_logs', function (Blueprint $table) {
            $table->id();
            $table->string('eid'); // Employee ID
            $table->string('campus')->nullable();
            $table->string('rfid')->nullable();

            $table->string('firstname');
            $table->string('middlename')->nullable();
            $table->string('lastname');

            $table->string('department')->nullable();
            $table->string('position')->nullable();
            $table->string('employment_type')->nullable();

            $table->dateTime('time_in');
            $table->dateTime('time_out')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_logs');
    }
};
