<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inout extends Model
{
    protected $fillable = [
        'sid',
        'campus',
        'rfid',
        'profile',
        'firstname',
        'middlename',
        'lastname',
        'department',
        'course',
        'section',
        'year',
        'time_in',
        'time_out',
    ];

    protected function casts(): array
    {
        return [
            'time_in' => 'datetime',
            'time_out' => 'datetime',
        ];
    }
}
