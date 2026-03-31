<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'borrower_id', 'borrower_type', 'borrow_type', 'book_section',
        'accession_no', 'date_borrowed', 'due_date', 'date_returned',
        'fine', 'status'
    ];

    public function borrower()
    {
        return $this->morphTo(__FUNCTION__, 'borrower_type', 'borrower_id');
    }

    public function book()
    {
        return $this->belongsTo(Book::class, 'accession_no', 'accession_no');
    }
}
