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
}
