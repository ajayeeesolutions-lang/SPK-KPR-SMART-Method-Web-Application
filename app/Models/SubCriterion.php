<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubCriterion extends Model
{
    use HasFactory;

    protected $fillable = [
        'criterion_id',
        'name',
        'operator',
        'min_val',
        'max_val',
        'text_value',
        'utility_value',
    ];

    protected $casts = [
        'min_val' => 'decimal:2',
        'max_val' => 'decimal:2',
        'utility_value' => 'decimal:2',
    ];

    public function criterion()
    {
        return $this->belongsTo(Criterion::class, 'criterion_id');
    }
}
