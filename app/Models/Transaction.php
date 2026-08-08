<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class Transaction extends Model
{
    protected $fillable = [
        'borrower_id', 'borrower_type', 'borrow_type', 'book_section',
        'book_id', 'book_type', 'accession_no', 'date_borrowed', 'due_date', 'date_returned',
        'fine', 'status'
    ];

    public function borrower()
    {
        return $this->morphTo(__FUNCTION__, 'borrower_type', 'borrower_id');
    }

    public function book()
    {
        $modelClass = $this->book_type ?? Book::class;
        $relatedModel = new $modelClass();
        $table = $this->getTable();
        $relatedTable = $relatedModel->getTable();
        $ownerKey = $relatedModel->getKeyName();

        if (Schema::hasColumn($table, 'accession_no') && Schema::hasColumn($relatedTable, $ownerKey)) {
            return $this->belongsTo($modelClass, 'accession_no', $ownerKey);
        }

        if (Schema::hasColumn($table, 'book_id') && Schema::hasColumn($relatedTable, $ownerKey)) {
            return $this->belongsTo($modelClass, 'book_id', $ownerKey);
        }

        return $this->belongsTo($modelClass, $this->getKeyName(), $ownerKey);
    }
}