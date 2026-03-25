<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $fillable = [
        'eid',
        'campus',
        'rfid',
        'firstname',
        'middlename',
        'lastname',
        'department',
        'position',
        'employment_type'
    ];

    public function transactions()
    {
        return $this->morphMany(Transaction::class, 'borrower', 'borrower_type', 'borrower_id', 'id');
    }
}
