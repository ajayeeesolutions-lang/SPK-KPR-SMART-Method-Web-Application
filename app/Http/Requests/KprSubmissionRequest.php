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
            'harga_rumah' => 'required|numeric|min:10000000',
            'uang_muka_dp' => 'required|numeric|min:0',
            'nilai_pinjaman' => 'required|numeric|min:10000000',
            'tenor_tahun' => 'required|integer|min:1|max:30',
            'ktp' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'kk' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'slip_gaji' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'npwp' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'rekening_koran' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'sk_kerja' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'pendukung' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ];
    }
}
