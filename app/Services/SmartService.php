<?php

namespace App\Services;

use App\Models\Criterion;
use App\Models\KprSubmission;
use App\Models\NasabahProfile;
use App\Models\Setting;
use App\Models\SmartAnalysisResult;
use Carbon\Carbon;

class SmartService
{
    /**
     * TAHAP 1 — Normalisasi Bobot Kriteria
     * Wj = wj / Σwj
     */
    private function normalizeWeights(iterable $criteria): array
    {
        $totalWeight = collect($criteria)->sum('weight');
        if ($totalWeight <= 0) $totalWeight = 100;

        $normalized = [];
        foreach ($criteria as $criterion) {
            $normalized[$criterion->code] = round((float) $criterion->weight / $totalWeight, 6);
        }
        return $normalized;
    }

    /**
     * TAHAP 2 — Ambil nilai mentah (raw) dari submission dan profil nasabah.
     * Mapping DINAMIS: baca kode kriteria dari DB, cocokkan dengan kolom submission/profil.
     * Kredibilitas SLIK hanya diambil dari nilai yang dikonfirmasi admin di submission.
     */
    private function extractRawValues(KprSubmission $submission, iterable $criteria): array
    {
        $profile = $submission->user->profile;

        // Hitung lama bekerja dalam tahun dari bulan (untuk C4 subkriteria "Tetap > 5 Tahun" dll)
        $lamaBekerjabulan = (int) ($submission->c3_lama_bekerja_bulan ?? $profile?->lama_bekerja_bulan ?? 0);
        $statusPekerjaan  = (string) ($submission->c3_status_pekerjaan ?? $profile?->status_pekerjaan ?? '');
        $statusLama = $this->mapStatusLamaPekerjaan($statusPekerjaan, $lamaBekerjabulan);

        $usia = (int) ($submission->c4_usia
            ?? ($profile?->tanggal_lahir ? \Carbon\Carbon::parse($profile->tanggal_lahir)->age : 0));

        $penghasilanBersih = (float) ($submission->c2_penghasilan_bersih
            ?? $profile?->penghasilan_bulanan
            ?? 0);

        $rawValues = [];
        foreach ($criteria as $criterion) {
            $code = $criterion->code;
            $name = strtolower($criterion->name);

            if (str_contains($name, 'slik') || str_contains($name, 'kredit')) {
                $rawValues[$code] = $submission->c1_riwayat_kredit;
            } elseif (str_contains($name, 'penghasilan')) {
                $rawValues[$code] = $penghasilanBersih;
            } elseif (str_contains($name, 'pekerjaan') || str_contains($name, 'kerja')) {
                $rawValues[$code] = $statusLama;
            } elseif (str_contains($name, 'usia') || str_contains($name, 'umur')) {
                $rawValues[$code] = $usia;
            } elseif (str_contains($name, 'tanggungan')) {
                $rawValues[$code] = (int) ($submission->c5_jumlah_tanggungan ?? $profile?->jumlah_tanggungan ?? 0);
            } else {
                $rawValues[$code] = null;
            }
        }

        return $rawValues;
    }

    /**
     * Konversi Status Pekerjaan + Lama Bekerja (bulan) ke label subkriteria
     * yang sesuai dengan subkriteria "Tetap > 5 Tahun", "Tetap 3-5 Tahun", dll.
     */
    private function mapStatusLamaPekerjaan(string $status, int $bulan): string
    {
        $tahun = $bulan / 12;

        $isPNS      = str_contains(strtolower($status), 'pns') || str_contains(strtolower($status), 'bumn');
        $isTetap    = str_contains(strtolower($status), 'tetap') || $isPNS || str_contains(strtolower($status), 'profesional');
        $isKontrak  = str_contains(strtolower($status), 'kontrak');
        $isWirausaha= str_contains(strtolower($status), 'wirausaha');

        if ($isTetap && $tahun > 5) return 'Tetap > 5 Tahun';
        if ($isTetap && $tahun >= 3) return 'Tetap 3-5 Tahun';
        if ($isKontrak) return 'Kontrak';
        if ($isWirausaha) return 'Freelance';

        return 'Freelance'; // default
    }

    /**
     * TAHAP 3 — Hitung nilai utility ui(ai) dari subkriteria yang sudah didefinisikan.
     * Subkriteria = lookup table utility (0.0 – 1.0) sesuai naskah BAB III.
     * Rumus: ui(ai) = nilai utility dari subkriteria yang cocok.
     */
    private function computeUtility(Criterion $criterion, mixed $rawVal): array
    {
        $subCriteria = $criterion->subCriteria;

        // Cari subkriteria yang cocok (lookup table)
        if ($subCriteria->count() > 0) {
            foreach ($subCriteria as $sub) {
                if ($this->matchSubCriterion($sub, $rawVal)) {
                    return [
                        'utility' => (float) $sub->utility_value,
                        'reason'  => "Masuk kategori '{$sub->name}' → utility = {$sub->utility_value}.",
                    ];
                }
            }
            // Jika tidak ada yang cocok, ambil utility terendah sebagai default
            $minUtility = $subCriteria->min('utility_value');
            return [
                'utility' => (float) $minUtility,
                'reason'  => "Nilai '{$rawVal}' tidak cocok dengan subkriteria apapun. Utility minimum ({$minUtility}) diterapkan.",
            ];
        }

        // Fallback jika belum ada subkriteria: gunakan rumus normalisasi SMART
        // ui(ai) = (Cout - Cmin) / (Cmax - Cmin) untuk BENEFIT
        // ui(ai) = (Cmax - Cout) / (Cmax - Cmin) untuk COST
        if (is_numeric($rawVal)) {
            $val = (float) $rawVal;
            $utility = ($criterion->type === 'cost')
                ? max(0.0, min(1.0, 1 - ($val / 10)))
                : max(0.0, min(1.0, $val / 10));
            return [
                'utility' => round($utility, 4),
                'reason'  => "Subkriteria belum dikonfigurasi. Fallback normalisasi linear untuk nilai {$val}.",
            ];
        }

        return [
            'utility' => 0.5,
            'reason'  => "Nilai '{$rawVal}' tidak dapat dievaluasi. Utility default 0.5 diberikan.",
        ];
    }

    /**
     * TAHAP 4 — Hitung total skor SMART.
     * u(ai) = Σ Wj × ui(ai)
     */
    private function computeTotalScore(array $normalizedWeights, array $utilities): float
    {
        $total = 0.0;
        foreach ($normalizedWeights as $code => $weight) {
            $total += $weight * ($utilities[$code] ?? 0);
        }
        return round($total, 4);
    }

    /**
     * TAHAP 5 — Tentukan keputusan berdasarkan threshold.
     * ≥ 0.80 → LAYAK | 0.60 – 0.79 → DIPERTIMBANGKAN | < 0.60 → TIDAK LAYAK
     */
    private function determineDecision(float $totalScore): string
    {
        $threshold = (float) Setting::getByKey('smart_threshold', 0.80);

        if ($totalScore >= $threshold) {
            return 'LAYAK';
        } elseif ($totalScore >= 0.60) {
            return 'DIPERTIMBANGKAN';
        }
        return 'TIDAK LAYAK';
    }

    /**
     * Analisis satu submission. Entry point utama dari controller.
     * Mengimplementasikan 5 tahap metode SMART:
     *   1. Normalisasi bobot Wj = wj / Σwj
     *   2. Ekstraksi nilai mentah dari profil nasabah
     *   3. Konversi ke nilai utility ui(ai) via subkriteria lookup
     *   4. Perhitungan total skor: u(ai) = Σ Wj × ui(ai)
     *   5. Keputusan berdasarkan threshold
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

        $hasCredibilityCriterion = $criteria->contains(function (Criterion $criterion) {
            $name = strtolower($criterion->name);
            return str_contains($name, 'slik') || str_contains($name, 'kredit');
        });

        if ($hasCredibilityCriterion && (!$submission->c1_riwayat_kredit || !$submission->c1_verified_at)) {
            throw new \Exception("Kredibilitas SLIK harus dikonfirmasi admin sebelum analisis SMART dijalankan.");
        }

        // TAHAP 1: Normalisasi Bobot
        $initialWeights    = [];
        foreach ($criteria as $c) $initialWeights[$c->code] = (float) $c->weight;
        $normalizedWeights = $this->normalizeWeights($criteria);

        // TAHAP 2: Nilai Mentah dari input submission langsung (dinamis berdasarkan nama kriteria di DB)
        $rawValues = $this->extractRawValues($submission, $criteria);

        // TAHAP 3: Nilai Utility ui(ai)
        $utilities    = [];
        $explanations = [];
        foreach ($criteria as $criterion) {
            $code  = $criterion->code;
            $result = $this->computeUtility($criterion, $rawValues[$code] ?? null);
            $utilities[$code]    = $result['utility'];
            $explanations[$code] = $result['reason'];
        }

        // TAHAP 4: Weighted Score & Total u(ai)
        $weightedScores = [];
        foreach ($criteria as $criterion) {
            $code = $criterion->code;
            $ws   = round($normalizedWeights[$code] * $utilities[$code], 4);
            $weightedScores[$code] = $ws;
        }
        $totalScore = $this->computeTotalScore($normalizedWeights, $utilities);

        // TAHAP 5: Keputusan
        $decision = $this->determineDecision($totalScore);

        // Buat ringkasan penjelasan (Untuk Orang Awam)
        $threshold     = (float) Setting::getByKey('smart_threshold', 0.80);
        $summaryLines  = [];
        
        $summaryLines[] = "Berdasarkan penilaian sistem, nasabah ini mendapatkan skor akhir " . $totalScore . " dari maksimal 1.00.";
        
        if ($decision === 'LAYAK') {
            $summaryLines[] = "Skor ini memenuhi batas minimum kelayakan bank (skor " . $threshold . "), sehingga nasabah direkomendasikan LAYAK untuk menerima KPR.";
        } elseif ($decision === 'DIPERTIMBANGKAN') {
            $summaryLines[] = "Skor ini berada sedikit di bawah standar ideal kelayakan (skor " . $threshold . "), namun masih cukup baik sehingga nasabah dapat DIPERTIMBANGKAN dengan syarat tambahan.";
        } else {
            $summaryLines[] = "Skor ini jauh di bawah standar kelayakan bank (skor " . $threshold . "), sehingga nasabah dinyatakan TIDAK LAYAK untuk menerima KPR saat ini.";
        }

        $summaryLines[] = "Faktor utama yang memengaruhi hasil ini adalah:";
        
        foreach ($criteria as $criterion) {
            $code  = $criterion->code;
            $ui    = $utilities[$code];
            $raw   = $rawValues[$code] ?? '-';
            
            // Konversi kategori utility ke bahasa awam
            if ($ui >= 0.8) {
                $status = "Sangat Baik";
            } elseif ($ui >= 0.6) {
                $status = "Cukup Baik";
            } else {
                $status = "Perlu Perhatian Khusus";
            }
            
            $summaryLines[] = "• " . $criterion->name . ": " . $status . " (" . $raw . ")";
        }

        $explanations['summary'] = $summaryLines;
        $explanations['raw_values'] = $rawValues;

        // Simpan hasil
        $result = SmartAnalysisResult::updateOrCreate(
            ['submission_id' => $submission->id],
            [
                'initial_weights'    => $initialWeights,
                'normalized_weights' => $normalizedWeights,
                'utilities'          => $utilities,
                'weighted_scores'    => $weightedScores,
                'total_score'        => $totalScore,
                'decision'           => $decision,
                'explanations'       => $explanations,
                'analyzed_at'        => now(),
            ]
        );

        $submission->update([
            'status_pengajuan' => 'analyzed',
            'status_keputusan' => $decision,
            'final_smart_score' => $totalScore,
        ]);

        return $result;
    }

    /**
     * Analisis SEMUA submission sekaligus.
     * Digunakan di SmartEngineController (halaman mesin SMART).
     */
    public function analyzeAllSubmissions(): array
    {
        $submissions = KprSubmission::with(['user.profile'])->get();
        $results = [];

        foreach ($submissions as $submission) {
            try {
                $results[] = $this->analyzeSubmission($submission);
            } catch (\Exception $e) {
                // skip submission yang profilnya belum lengkap
            }
        }

        return $results;
    }

    /**
     * Cocokkan nilai dengan aturan subkriteria.
     */
    private function matchSubCriterion(mixed $sub, mixed $val): bool
    {
        $op = $sub->operator;

        // Untuk operator teks (kategori)
        if ($op === 'equals_text' || !is_numeric($val)) {
            return strcasecmp(trim((string)$sub->text_value), trim((string)$val)) === 0;
        }

        $num = (float) $val;
        $min = (float) $sub->min_val;
        $max = isset($sub->max_val) ? (float) $sub->max_val : PHP_FLOAT_MAX;

        return match ($op) {
            '>'       => $num > $min,
            '>='      => $num >= $min,
            '<'       => $num < $min,
            '<='      => $num <= $min,
            '='       => abs($num - $min) < 0.0001,
            'between' => ($num >= $min && $num <= $max),
            default   => false,
        };
    }
}
