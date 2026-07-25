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
        $bankName = Setting::getByKey('bank_name', 'BANK KPR SEJAHTERA INDONESIA');
        $bankAddress = Setting::getByKey('bank_address', 'Jl. Jenderal Sudirman No. 88, Kaveling 5, Jakarta Selatan');
        $bankPhone = Setting::getByKey('bank_phone', '(021) 555-8888');

        return view('admin.settings.index', compact('smartThreshold', 'bankName', 'bankAddress', 'bankPhone'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'smart_threshold' => 'required|numeric|min:0|max:100',
            'bank_name' => 'required|string|max:255',
            'bank_address' => 'required|string|max:500',
            'bank_phone' => 'required|string|max:50',
        ]);

        Setting::setKey('smart_threshold', $request->smart_threshold);
        Setting::setKey('bank_name', $request->bank_name);
        Setting::setKey('bank_address', $request->bank_address);
        Setting::setKey('bank_phone', $request->bank_phone);

        return redirect()->route('admin.settings.index')->with('success', 'Pengaturan sistem berhasil diperbarui.');
    }
}
