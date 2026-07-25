<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NasabahProfile extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'nik',
        'nama_lengkap',
        'tempat_lahir',
        'tanggal_lahir',
        'jenis_kelamin',
        'alamat',
        'no_hp',
        'status_pernikahan',
        'jumlah_tanggungan',
        'pekerjaan',
        'status_pekerjaan',
        'lama_bekerja_bulan',
        'penghasilan_bulanan',
        'penghasilan_pasangan',
        'pengeluaran_bulanan',
        'cicilan_lain',
        'riwayat_kredit',
        'foto_path',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'penghasilan_bulanan' => 'decimal:2',
        'penghasilan_pasangan' => 'decimal:2',
        'pengeluaran_bulanan' => 'decimal:2',
        'cicilan_lain' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getTotalPenghasilanAttribute(): float
    {
        return (float) ($this->penghasilan_bulanan + $this->penghasilan_pasangan);
    }

    public function getDtiRatioAttribute(): float
    {
        $totalIncome = $this->total_penghasilan;
        if ($totalIncome <= 0) return 100.0;
        return round(($this->cicilan_lain / $totalIncome) * 100, 2);
    }
}
