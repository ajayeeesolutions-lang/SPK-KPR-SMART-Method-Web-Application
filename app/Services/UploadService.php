<?php

namespace App\Services;

use App\Models\KprSubmission;
use App\Models\SubmissionDocument;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UploadService
{
    /**
     * Upload document for a KPR submission.
     */
    public function uploadDocument(KprSubmission $submission, string $docType, UploadedFile $file): SubmissionDocument
    {
        $folder = "documents/submission_{$submission->id}";
        $filename = "{$docType}_" . Str::random(10) . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs($folder, $filename, 'public');

        // Delete previous document of same type if exists
        $existing = SubmissionDocument::where('submission_id', $submission->id)
            ->where('document_type', $docType)
            ->first();

        if ($existing) {
            Storage::disk('public')->delete($existing->file_path);
            $existing->delete();
        }

        return SubmissionDocument::create([
            'submission_id' => $submission->id,
            'document_type' => $docType,
            'file_path' => $path,
            'original_name' => $file->getClientOriginalName(),
            'file_size' => $file->getSize(),
        ]);
    }

    /**
     * Upload profile avatar photo.
     */
    public function uploadPhoto(UploadedFile $file): string
    {
        $filename = "foto_" . time() . '_' . Str::random(8) . '.' . $file->getClientOriginalExtension();
        return $file->storeAs('photos', $filename, 'public');
    }
}
