<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class KprSubmissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'harga_rumah'           => 'required|numeric|min:10000000',
            'uang_muka_dp'          => 'required|numeric|min:0',
            'nilai_pinjaman'        => 'required|numeric|min:10000000',
            'tenor_tahun'           => 'required|integer|min:1|max:30',
            // 5 Kriteria SMART — Wajib diisi di form pengajuan
            'c1_riwayat_kredit'     => 'required|string',
            'c2_penghasilan_bersih' => 'required|numeric|min:0',
            'c3_status_pekerjaan'   => 'required|string',
            'c3_lama_bekerja_bulan' => 'required|integer|min:0',
            'c4_usia'               => 'required|integer|min:17|max:80',
            'c5_jumlah_tanggungan'  => 'required|integer|min:0',
            // Dokumen
            'ktp'           => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'kk'            => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'slip_gaji'     => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'npwp'          => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'rekening_koran'=> 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'sk_kerja'      => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'pendukung'     => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ];
    }

    public function messages(): array
    {
        return [
            'c1_riwayat_kredit.required'     => 'Riwayat SLIK OJK (C1) wajib dipilih.',
            'c2_penghasilan_bersih.required'  => 'Penghasilan Bersih (C2) wajib diisi.',
            'c3_status_pekerjaan.required'    => 'Status Pekerjaan (C3) wajib dipilih.',
            'c3_lama_bekerja_bulan.required'  => 'Lama Bekerja (C3) wajib diisi.',
            'c4_usia.required'                => 'Usia (C4) wajib diisi.',
            'c5_jumlah_tanggungan.required'   => 'Jumlah Tanggungan (C5) wajib diisi.',
        ];
    }
}
