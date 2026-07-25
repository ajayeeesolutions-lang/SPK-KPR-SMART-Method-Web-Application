<?php

namespace App\Services;

use App\Models\Criterion;
use App\Models\KprSubmission;
use App\Models\NasabahProfile;
use App\Models\Setting;
use App\Models\SmartAnalysisResult;

class SmartService
{
    /**
     * Run SMART Method calculation on a given KPR Submission.
     */
    public function analyzeSubmission(KprSubmission $submission): SmartAnalysisResult
    {
        $submission->load(['user.profile']);
        $profile = $submission->user->profile;

        if (!$profile) {
            throw new \Exception("Profil calon nasabah belum dilengkapi.");
        }

        $criteria = Criterion::with('subCriteria')->where('is_active', true)->get();
        if ($criteria->isEmpty()) {
            throw new \Exception("Kriteria SMART belum dikonfigurasi.");
        }

        // 1. Initial Weights & Normalization
        $totalWeight = $criteria->sum('weight');
        if ($totalWeight <= 0) {
            $totalWeight = 100;
        }

        $initialWeights = [];
        $normalizedWeights = [];

        foreach ($criteria as $criterion) {
            $initialWeights[$criterion->code] = (float) $criterion->weight;
            $normalizedWeights[$criterion->code] = round((float) $criterion->weight / $totalWeight, 4);
        }

        // 2. Map Profile Metrics to Criteria Values
        $rawValues = [
            'C1' => (float) ($profile->penghasilan_bulanan + $profile->penghasilan_pasangan),
            'C2' => (float) round($profile->lama_bekerja_bulan / 12, 1), // Lama bekerja dalam tahun
            'C3' => (float) $profile->dti_ratio, // Rasio cicilan (%)
            'C4' => (string) $profile->status_pekerjaan,
            'C5' => (string) $profile->riwayat_kredit,
        ];

        // 3. Utility Values Calculation
        $utilities = [];
        $explanations = [];

        foreach ($criteria as $criterion) {
            $code = $criterion->code;
            $val = $rawValues[$code] ?? null;

            $matchedUtility = $this->evaluateUtility($criterion, $val);
            $utilities[$code] = $matchedUtility['utility'];
            $explanations[$code] = $matchedUtility['reason'];
        }

        // 4. Weighted Multiplication & Total SMART Score
        $weightedScores = [];
        $totalScore = 0.0;

        foreach ($criteria as $criterion) {
            $code = $criterion->code;
            $w = $normalizedWeights[$code] ?? 0;
            $u = $utilities[$code] ?? 0;
            $weightedVal = round($w * $u, 2);

            $weightedScores[$code] = $weightedVal;
            $totalScore += $weightedVal;
        }

        $totalScore = round($totalScore, 2);

        // 5. Decision Threshold Check
        $threshold = (float) Setting::getByKey('smart_threshold', 80.00);
        $decision = ($totalScore >= $threshold) ? 'DITERIMA' : 'TIDAK DITERIMA';

        // 6. Generate Master Analysis Explanation Summary
        $summaryReasons = [];
        if ($decision === 'DITERIMA') {
            $summaryReasons[] = "Total skor SMART sebesar {$totalScore} telah memenuhi/melebihi ambang batas kelayakan ({$threshold}).";
        } else {
            $summaryReasons[] = "Total skor SMART sebesar {$totalScore} masih berada di bawah standar ambang batas kelayakan ({$threshold}).";
        }

        foreach ($criteria as $criterion) {
            $code = $criterion->code;
            $u = $utilities[$code] ?? 0;
            $reason = $explanations[$code] ?? '';
            $statusStr = ($u >= 80) ? '[POSITIF]' : (($u >= 70) ? '[CUKUP]' : '[PERHATIAN/RISIKO]');
            $summaryReasons[] = "{$statusStr} {$criterion->name}: {$reason} (Utility: {$u})";
        }

        $explanations['summary'] = $summaryReasons;

        // 7. Save Analysis Result
        $result = SmartAnalysisResult::updateOrCreate(
            ['submission_id' => $submission->id],
            [
                'initial_weights' => $initialWeights,
                'normalized_weights' => $normalizedWeights,
                'utilities' => $utilities,
                'weighted_scores' => $weightedScores,
                'total_score' => $totalScore,
                'decision' => $decision,
                'explanations' => $explanations,
                'analyzed_at' => now(),
            ]
        );

        // Update Submission Record Status
        $submission->update([
            'status_pengajuan' => 'analyzed',
            'status_keputusan' => $decision,
            'final_smart_score' => $totalScore,
        ]);

        return $result;
    }

    /**
     * Evaluate utility value based on SubCriterion rules or fallback formulas.
     */
    private function evaluateUtility(Criterion $criterion, mixed $rawVal): array
    {
        $subCriteria = $criterion->subCriteria;

        if ($subCriteria->count() > 0) {
            foreach ($subCriteria as $sub) {
                if ($this->matchSubCriterion($sub, $rawVal)) {
                    return [
                        'utility' => (float) $sub->utility_value,
                        'reason' => "Kategori '{$sub->name}' terpenuhi.",
                    ];
                }
            }
        }

        // Fallback default ratings if sub-criteria array is non-matching or continuous numeric
        if (is_numeric($rawVal)) {
            $val = (float) $rawVal;
            if ($criterion->type === 'cost') {
                // Cost criterion: Lower value -> higher utility
                $utility = max(0, min(100, 100 - ($val * 1.5)));
                return [
                    'utility' => round($utility, 2),
                    'reason' => "Nilai rasio/biaya {$val} dikonversi ke nilai utility.",
                ];
            } else {
                // Benefit criterion
                $utility = min(100, max(50, $val * 5));
                return [
                    'utility' => round($utility, 2),
                    'reason' => "Nilai {$val} dikonversi ke nilai utility.",
                ];
            }
        }

        return [
            'utility' => 70.0,
            'reason' => "Nilai default diberikan ({$rawVal}).",
        ];
    }

    private function matchSubCriterion(mixed $sub, mixed $val): bool
    {
        $op = $sub->operator;

        if ($op === 'equals_text' || !is_numeric($val)) {
            return strcasecmp(trim((string)$sub->text_value), trim((string)$val)) === 0;
        }

        $num = (float) $val;
        $min = (float) $sub->min_val;
        $max = (float) $sub->max_val;

        return match ($op) {
            '>' => $num > $min,
            '>=' => $num >= $min,
            '<' => $num < $min,
            '<=' => $num <= $min,
            '=' => abs($num - $min) < 0.0001,
            'between' => ($num >= $min && $num <= $max),
            default => false,
        };
    }
}
