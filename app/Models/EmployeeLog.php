<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeLog extends Model
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
        'employment_type',
        'time_in',
        'time_out'
    ];

    protected $casts = [
        'time_in' => 'datetime',
        'time_out' => 'datetime',
    ];
}
