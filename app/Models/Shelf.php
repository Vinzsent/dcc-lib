<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shelf extends Model
{
    protected $fillable = [
        'shelf_number',
        'description',
        'campus'
    ];

    public function books()
    {
        return $this->hasMany(Book::class, 'shelf_number', 'shelf_number');
    }
}
