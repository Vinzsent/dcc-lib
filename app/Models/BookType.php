<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookType extends Model
{
    use HasFactory;

    protected $table = 'book_types';

    protected $fillable = [
        'name',
        'level',
        'description',
        'status',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function scopeActive($query)
    {
        return $query->where('status', 'ACTIVE');
    }

    public function scopeForLevel($query, $level)
    {
        if (!$level || $level === 'all') {
            return $query;
        }

        if ($level === 'college') {
            return $query->where(function ($q) {
                $q->where('level', 'College')
                  ->orWhere('level', 'All');
            });
        }

        if ($level === 'elementary') {
            return $query->where(function ($q) {
                $q->where('level', 'Elementary')
                  ->orWhere('level', 'All');
            });
        }

        if ($level === 'highschool' || $level === 'hs_shs') {
            return $query->where(function ($q) {
                $q->where('level', 'High School / Senior High School')
                  ->orWhere('level', 'All');
            });
        }

        return $query->where('level', $level);
    }
}
