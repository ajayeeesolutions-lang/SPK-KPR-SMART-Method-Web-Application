<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubmissionDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'submission_id',
        'document_type',
        'file_path',
        'original_name',
        'file_size',
    ];

    public function submission()
    {
        return $this->belongsTo(KprSubmission::class, 'submission_id');
    }
}
