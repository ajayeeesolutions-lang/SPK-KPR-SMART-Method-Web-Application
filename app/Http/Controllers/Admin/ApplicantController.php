<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KprSubmission;
use App\Models\NasabahProfile;
use App\Models\User;
use App\Services\SmartService;
use App\Services\UploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ApplicantController extends Controller
{
    public function index(Request $request)
    {
        $query = KprSubmission::with(['user.profile', 'smartResult']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('no_pengajuan', 'like', "%{$search}%")
                  ->orWhereHas('user', function($qu) use ($search) {
                      $qu->where('name', 'like', "%{$search}%")
                         ->orWhereHas('profile', function($qp) use ($search) {
                             $qp->where('nik', 'like', "%{$search}%")
                                ->orWhere('nama_lengkap', 'like', "%{$search}%");
                         });
                  });
            });
        }

        if ($request->filled('status')) {
            $query->where('status_keputusan', $request->status);
        }

        $submissions = $query->latest()->paginate(10);

        return view('admin.applicants.index', compact('submissions'));
    }

    public function create()
    {
        return view('admin.applicants.create');
    }

    public function store(Request $request, UploadService $uploadService, SmartService $smartService)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'nik' => 'required|string|size:16|unique:nasabah_profiles,nik',
            'tempat_lahir' => 'required|string|max:100',
            'tanggal_lahir' => 'required|date',
            'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
            'alamat' => 'required|string',
            'no_hp' => 'required|string|max:20',
            'status_pernikahan' => 'required|in:Belum Menikah,Menikah,Cerai',
            'jumlah_tanggungan' => 'required|integer|min:0',
            'pekerjaan' => 'required|string|max:100',
            'status_pekerjaan' => 'required|in:PNS/BUMN,Pegawai Tetap Swasta,Wirausaha,Pegawai Kontrak,Lainnya',
            'lama_bekerja_bulan' => 'required|integer|min:0',
            'penghasilan_bulanan' => 'required|numeric|min:0',
            'penghasilan_pasangan' => 'nullable|numeric|min:0',
            'pengeluaran_bulanan' => 'required|numeric|min:0',
            'cicilan_lain' => 'nullable|numeric|min:0',
            'riwayat_kredit' => 'required|in:Lancar,Dalam Perhatian,Tidak Lancar',
            'harga_rumah' => 'required|numeric|min:10000000',
            'uang_muka_dp' => 'required|numeric|min:0',
            'nilai_pinjaman' => 'required|numeric|min:10000000',
            'tenor_tahun' => 'required|integer|min:1|max:30',
            'foto' => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['no_hp'],
            'role' => 'nasabah',
            'password' => Hash::make('password123'),
        ]);

        $fotoPath = null;
        if ($request->hasFile('foto')) {
            $fotoPath = $uploadService->uploadPhoto($request->file('foto'));
        }

        $profile = NasabahProfile::create([
            'user_id' => $user->id,
            'nik' => $validated['nik'],
            'nama_lengkap' => $validated['name'],
            'tempat_lahir' => $validated['tempat_lahir'],
            'tanggal_lahir' => $validated['tanggal_lahir'],
            'jenis_kelamin' => $validated['jenis_kelamin'],
            'alamat' => $validated['alamat'],
            'no_hp' => $validated['no_hp'],
            'status_pernikahan' => $validated['status_pernikahan'],
            'jumlah_tanggungan' => $validated['jumlah_tanggungan'],
            'pekerjaan' => $validated['pekerjaan'],
            'status_pekerjaan' => $validated['status_pekerjaan'],
            'lama_bekerja_bulan' => $validated['lama_bekerja_bulan'],
            'penghasilan_bulanan' => $validated['penghasilan_bulanan'],
            'penghasilan_pasangan' => $validated['penghasilan_pasangan'] ?? 0,
            'pengeluaran_bulanan' => $validated['pengeluaran_bulanan'],
            'cicilan_lain' => $validated['cicilan_lain'] ?? 0,
            'riwayat_kredit' => $validated['riwayat_kredit'],
            'foto_path' => $fotoPath,
        ]);

        $noPengajuan = 'NAS-' . date('Y') . '-' . Str::padLeft(KprSubmission::count() + 1, 3, '0');

        $submission = KprSubmission::create([
            'no_pengajuan' => $noPengajuan,
            'user_id' => $user->id,
            'harga_rumah' => $validated['harga_rumah'],
            'uang_muka_dp' => $validated['uang_muka_dp'],
            'nilai_pinjaman' => $validated['nilai_pinjaman'],
            'tenor_tahun' => $validated['tenor_tahun'],
            'status_pengajuan' => 'pending',
        ]);

        // Auto run SMART analysis
        try {
            $smartService->analyzeSubmission($submission);
        } catch (\Exception $e) {
            // handle gracefully
        }

        return redirect()->route('admin.applicants.show', $submission->id)
            ->with('success', 'Data calon nasabah berhasil ditambahkan & dianalisis!');
    }

    public function show(KprSubmission $applicant)
    {
        $submission = $applicant;
        $submission->load(['user.profile', 'documents', 'smartResult', 'approver']);
        return view('admin.applicants.show', compact('submission'));
    }

    public function edit(KprSubmission $applicant)
    {
        $submission = $applicant;
        $submission->load(['user.profile']);
        return view('admin.applicants.edit', compact('submission'));
    }

    public function update(Request $request, KprSubmission $applicant, SmartService $smartService)
    {
        $submission = $applicant;
        $user = $submission->user;
        $profile = $user->profile;

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'harga_rumah' => 'required|numeric|min:10000000',
            'uang_muka_dp' => 'required|numeric|min:0',
            'nilai_pinjaman' => 'required|numeric|min:10000000',
            'tenor_tahun' => 'required|integer|min:1|max:30',
            'penghasilan_bulanan' => 'required|numeric|min:0',
            'lama_bekerja_bulan' => 'required|integer|min:0',
            'status_pekerjaan' => 'required|string',
            'riwayat_kredit' => 'required|string',
        ]);

        $user->update(['name' => $validated['name']]);
        if ($profile) {
            $profile->update([
                'nama_lengkap' => $validated['name'],
                'penghasilan_bulanan' => $validated['penghasilan_bulanan'],
                'lama_bekerja_bulan' => $validated['lama_bekerja_bulan'],
                'status_pekerjaan' => $validated['status_pekerjaan'],
                'riwayat_kredit' => $validated['riwayat_kredit'],
            ]);
        }

        $submission->update([
            'harga_rumah' => $validated['harga_rumah'],
            'uang_muka_dp' => $validated['uang_muka_dp'],
            'nilai_pinjaman' => $validated['nilai_pinjaman'],
            'tenor_tahun' => $validated['tenor_tahun'],
        ]);

        // Re-run SMART
        try {
            $smartService->analyzeSubmission($submission);
        } catch (\Exception $e) {}

        return redirect()->route('admin.applicants.show', ['applicant' => $submission->id])
            ->with('success', 'Data pengajuan berhasil diperbarui!');
    }

    public function destroy(KprSubmission $applicant)
    {
        $applicant->delete();
        return redirect()->route('admin.applicants.index')->with('success', 'Data pengajuan berhasil dihapus.');
    }
}
