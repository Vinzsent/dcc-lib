<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookElem extends Model
{
    protected $table = 'books_elem';
    protected $primaryKey = 'accession_number';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'accession_number',
        'call_number',
        'title',
        'author',
    ];
}
