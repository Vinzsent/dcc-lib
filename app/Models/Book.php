<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    protected $primaryKey = 'accession_no';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'accession_no',
        'barcode',
        'title',
        'author',
        'callnumber',
        'status'
    ];

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'accession_no', 'accession_no');
    }
}
