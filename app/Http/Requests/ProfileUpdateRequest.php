<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProfileUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId = $this->user()->id;
        $profileId = $this->user()->profile?->id;

        return [
            'nik' => 'required|string|size:16|unique:nasabah_profiles,nik,' . $profileId,
            'nama_lengkap' => 'required|string|max:255',
            'tempat_lahir' => 'required|string|max:100',
            'tanggal_lahir' => 'required|date|before:today',
            'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
            'alamat' => 'required|string',
            'no_hp' => 'required|string|max:20',
            'status_pernikahan' => 'required|in:Belum Menikah,Menikah,Cerai',
            'jumlah_tanggungan' => 'required|integer|min:0|max:20',
            'pekerjaan' => 'required|string|max:100',
            'status_pekerjaan' => 'required|in:PNS/BUMN,Pegawai Tetap Swasta,Wirausaha,Pegawai Kontrak,Lainnya',
            'lama_bekerja_bulan' => 'required|integer|min:0',
            'penghasilan_bulanan' => 'required|numeric|min:0',
            'penghasilan_pasangan' => 'nullable|numeric|min:0',
            'pengeluaran_bulanan' => 'required|numeric|min:0',
            'cicilan_lain' => 'nullable|numeric|min:0',
            'riwayat_kredit' => 'required|in:Lancar,Dalam Perhatian,Tidak Lancar',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ];
    }
}
