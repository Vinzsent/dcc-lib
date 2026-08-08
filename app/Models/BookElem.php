<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookElem extends Model
{
    protected $table = 'books_elem';
    protected $primaryKey = 'accession_number';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'accession_number',
        'barcode',
        'call_number',
        'title',
        'author',
        'location',
        'shelf_number',
        'campus',
        'status',
    ];
}
