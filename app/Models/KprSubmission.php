<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class KprSubmission extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'no_pengajuan',
        'user_id',
        'harga_rumah',
        'uang_muka_dp',
        'nilai_pinjaman',
        'tenor_tahun',
        'status_pengajuan',
        'status_keputusan',
        'final_smart_score',
        'manager_notes',
        'approved_by',
        'approved_at',
        // 5 Kriteria SMART
        'c1_riwayat_kredit',
        'c2_penghasilan_bersih',
        'c3_status_pekerjaan',
        'c3_lama_bekerja_bulan',
        'c4_usia',
        'c5_jumlah_tanggungan',
    ];

    protected $casts = [
        'harga_rumah'           => 'decimal:2',
        'uang_muka_dp'          => 'decimal:2',
        'nilai_pinjaman'        => 'decimal:2',
        'c2_penghasilan_bersih' => 'decimal:2',
        'final_smart_score'     => 'decimal:2',
        'approved_at'           => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function documents()
    {
        return $this->hasMany(SubmissionDocument::class, 'submission_id');
    }

    public function smartResult()
    {
        return $this->hasOne(SmartAnalysisResult::class, 'submission_id');
    }
}
