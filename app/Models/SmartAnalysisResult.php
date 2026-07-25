<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SmartAnalysisResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'submission_id',
        'initial_weights',
        'normalized_weights',
        'utilities',
        'weighted_scores',
        'total_score',
        'decision',
        'explanations',
        'analyzed_at',
    ];

    protected $casts = [
        'initial_weights' => 'array',
        'normalized_weights' => 'array',
        'utilities' => 'array',
        'weighted_scores' => 'array',
        'explanations' => 'array',
        'total_score' => 'decimal:2',
        'analyzed_at' => 'datetime',
    ];

    public function submission()
    {
        return $this->belongsTo(KprSubmission::class, 'submission_id');
    }
}
