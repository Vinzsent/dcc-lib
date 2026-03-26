<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $fillable = [
        'sid',
        'campus',
        'rfid',

        'firstname',
        'middlename',
        'lastname',
        'department',
        'course',

        'year',
        'grade',
        'section'
    ];

    public function transactions()
    {
        return $this->morphMany(Transaction::class, 'borrower', 'borrower_type', 'borrower_id', 'sid');
    }
}
