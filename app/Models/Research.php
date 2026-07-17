<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Research extends Model
{
    protected $table = 'researches';

    public $incrementing = false;
    protected $keyType = 'string';
    protected $primaryKey = 'accession_no';

    protected $fillable = [
        'accession_no',
        'barcode',
        'title',
        'author',
        'call_number',
        'location',
        'shelf_number',
        'campus',
        'status',
    ];

    public function transactions(): HasMany
    {
        return $this->hasMany(\App\Models\Transaction::class, 'accession_no', 'accession_no');
    }

    public function shelf(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Shelf::class, 'shelf_number', 'shelf_number');
    }
}
