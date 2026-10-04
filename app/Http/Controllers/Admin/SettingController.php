<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $smartThreshold = Setting::getByKey('smart_threshold', '80.00');
        $bankName       = Setting::getByKey('bank_name', 'PT Citra Pasada Properti');
        $bankAddress    = Setting::getByKey('bank_address', 'Jl. Jenderal Sudirman, Jakarta');
        $bankPhone      = Setting::getByKey('bank_phone', '(021) 555-8888');
        $adminWa        = Setting::getByKey('admin_wa', '6281234567890');

        // Field tanda tangan laporan PDF
        $jabatanTtd = Setting::getByKey('jabatan_ttd', 'Manager Analis Kredit KPR');
        $namaTtd    = Setting::getByKey('nama_ttd', 'Anisa Kencana, SE, MM');
        $nipTtd     = Setting::getByKey('nip_ttd', '19880415 201201 2 004');

        return view('admin.settings.index', compact(
            'smartThreshold', 'bankName', 'bankAddress', 'bankPhone', 'adminWa',
            'jabatanTtd', 'namaTtd', 'nipTtd'
        ));
    }

    public function update(Request $request)
    {
        $request->validate([
            'smart_threshold' => 'required|numeric|min:0|max:100',
            'bank_name'       => 'required|string|max:255',
            'bank_address'    => 'required|string|max:500',
            'bank_phone'      => 'required|string|max:50',
            'admin_wa'        => 'required|string|max:50',
            'jabatan_ttd'     => 'required|string|max:150',
            'nama_ttd'        => 'required|string|max:150',
            'nip_ttd'         => 'nullable|string|max:100',
        ]);

        Setting::setKey('smart_threshold', $request->smart_threshold);
        Setting::setKey('bank_name',       $request->bank_name);
        Setting::setKey('bank_address',    $request->bank_address);
        Setting::setKey('bank_phone',      $request->bank_phone);
        Setting::setKey('admin_wa',        $request->admin_wa);
        Setting::setKey('jabatan_ttd',     $request->jabatan_ttd);
        Setting::setKey('nama_ttd',        $request->nama_ttd);
        Setting::setKey('nip_ttd',         $request->nip_ttd ?? '');

        return redirect()->route('admin.settings.index')->with('success', 'Pengaturan sistem berhasil diperbarui.');
    }
}
