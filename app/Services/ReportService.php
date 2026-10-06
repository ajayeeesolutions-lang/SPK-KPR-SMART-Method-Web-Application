<?php

namespace App\Services;

use App\Models\KprSubmission;
use App\Models\Criterion;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportService
{
    /**
     * Generate PDF Decision Report for KPR Submission.
     */
    public function generateSubmissionPdf(KprSubmission $submission)
    {
        abort_unless($submission->c1_verified_at && $submission->smartResult, 404);

        $submission->load(['user.profile', 'smartResult', 'approver']);
        $criteria = Criterion::where('is_active', true)->get()->keyBy('code');

        $bankName = Setting::getByKey('bank_name', 'BANK KPR SEJAHTERA');
        $bankAddress = Setting::getByKey('bank_address', 'Jl. Jenderal Sudirman No. 88, Jakarta Selatan');
        $bankPhone = Setting::getByKey('bank_phone', '(021) 555-8888');

        $jabatanTtd = Setting::getByKey('jabatan_ttd', 'Manager Analis Kredit KPR');
        $namaTtd = Setting::getByKey('nama_ttd', 'Anisa Kencana, SE, MM');
        $nipTtd = Setting::getByKey('nip_ttd', '19880415 201201 2 004');

        $pdf = Pdf::loadView('reports.submission_pdf', [
            'submission' => $submission,
            'criteria' => $criteria,
            'bankName' => $bankName,
            'bankAddress' => $bankAddress,
            'bankPhone' => $bankPhone,
            'jabatanTtd' => $jabatanTtd,
            'namaTtd' => $namaTtd,
            'nipTtd' => $nipTtd,
            'generatedAt' => now()->translatedFormat('d F Y H:i'),
        ]);

        $pdf->setPaper('A4', 'portrait');

        return $pdf;
    }
}
