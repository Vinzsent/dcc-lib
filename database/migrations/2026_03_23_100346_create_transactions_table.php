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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('borrower_id');
            $table->string('borrower_type');
            $table->string('accession_no');
            $table->foreign('accession_no')->references('accession_no')->on('books')->onDelete('cascade');
            $table->date('date_borrowed');
            $table->date('due_date');
            $table->date('date_returned')->nullable();
            $table->decimal('fine', 8, 2)->default(0);
            $table->enum('status', ['Borrowed', 'Returned', 'Overdue'])->default('Borrowed');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
