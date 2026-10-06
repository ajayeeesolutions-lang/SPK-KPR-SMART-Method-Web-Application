<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KprSubmission;
use App\Models\NasabahProfile;
use App\Models\User;
use App\Services\SmartService;
use App\Services\UploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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

    public function store(Request $request, UploadService $uploadService)
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

        return redirect()->route('admin.applicants.show', $submission->id)
            ->with('success', 'Data calon nasabah berhasil ditambahkan. Konfirmasi kredibilitas SLIK untuk menjalankan analisis.');
    }

    public function show(KprSubmission $applicant)
    {
        $submission = $applicant;
        $submission->load(['user.profile', 'documents', 'smartResult', 'approver', 'credibilityVerifier']);
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
        ]);

        $user->update(['name' => $validated['name']]);
        if ($profile) {
            $profile->update([
                'nama_lengkap' => $validated['name'],
                'penghasilan_bulanan' => $validated['penghasilan_bulanan'],
                'lama_bekerja_bulan' => $validated['lama_bekerja_bulan'],
                'status_pekerjaan' => $validated['status_pekerjaan'],
            ]);
        }

        $submission->update([
            'harga_rumah' => $validated['harga_rumah'],
            'uang_muka_dp' => $validated['uang_muka_dp'],
            'nilai_pinjaman' => $validated['nilai_pinjaman'],
            'tenor_tahun' => $validated['tenor_tahun'],
        ]);

        if ($submission->c1_verified_at) {
            $smartService->analyzeSubmission($submission);
        }

        return redirect()->route('admin.applicants.show', ['applicant' => $submission->id])
            ->with('success', 'Data pengajuan berhasil diperbarui!');
    }

    public function confirmCredibility(Request $request, KprSubmission $applicant, SmartService $smartService)
    {
        $validated = $request->validate([
            'c1_riwayat_kredit' => 'required|in:Lancar,Dalam Perhatian Khusus,Kurang Lancar,Diragukan,Macet',
        ]);

        DB::transaction(function () use ($applicant, $validated, $smartService) {
            $applicant->update([
                'c1_riwayat_kredit' => $validated['c1_riwayat_kredit'],
                'c1_verified_at' => now(),
                'c1_verified_by' => auth()->id(),
            ]);

            $smartService->analyzeSubmission($applicant);
        });

        return redirect()->route('admin.applicants.show', ['applicant' => $applicant->id])
            ->with('success', 'Kredibilitas SLIK dikonfirmasi dan analisis SMART berhasil dijalankan.');
    }

    public function destroy(KprSubmission $applicant)
    {
        $applicant->delete();
        return redirect()->route('admin.applicants.index')->with('success', 'Data pengajuan berhasil dihapus.');
    }

    public function downloadTemplate()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="Template_Data_Nasabah_KPR.csv"',
        ];

        $columns = [
            'Nama Lengkap', 'Email', 'NIK', 'Tempat Lahir', 'Tanggal Lahir (YYYY-MM-DD)',
            'Jenis Kelamin', 'Alamat', 'No HP',
            'Status Pernikahan', 'Jumlah Tanggungan',
            'Pekerjaan', 'Status Pekerjaan', 'Lama Bekerja (Bulan)',
            'Penghasilan Bulanan', 'Penghasilan Pasangan', 'Pengeluaran Bulanan',
            'Cicilan Lain', 'Riwayat Kredit',
            'Harga Rumah', 'Uang Muka (DP)', 'Nilai Pinjaman', 'Tenor (Tahun)'
        ];

        $callback = function() use ($columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            
            // Contoh pengisian
            fputcsv($file, [
                'Budi Santoso', 'budi@mail.com', '3201012345678901', 'Jakarta', '1990-01-01',
                'Laki-laki', 'Jl. Merdeka No. 1', '08123456789',
                'Menikah', 2,
                'Karyawan Swasta', 'Tetap > 2 Tahun', 36,
                10000000, 0, 4000000,
                1000000, 'Lancar',
                500000000, 100000000, 400000000, 15
            ]);
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function export()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="Export_Data_Nasabah_KPR.csv"',
        ];

        $submissions = KprSubmission::with(['user.profile'])->get();

        $callback = function() use ($submissions) {
            $file = fopen('php://output', 'w');
            $columns = [
                'Nama Lengkap', 'Email', 'NIK', 'Tempat Lahir', 'Tanggal Lahir',
                'Jenis Kelamin', 'Alamat', 'No HP',
                'Status Pernikahan', 'Jumlah Tanggungan',
                'Pekerjaan', 'Status Pekerjaan', 'Lama Bekerja (Bulan)',
                'Penghasilan Bulanan', 'Penghasilan Pasangan', 'Pengeluaran Bulanan',
                'Cicilan Lain', 'Riwayat Kredit',
                'Harga Rumah', 'Uang Muka (DP)', 'Nilai Pinjaman', 'Tenor (Tahun)', 'Status SMART'
            ];
            fputcsv($file, $columns);

            foreach ($submissions as $sub) {
                $prof = $sub->user->profile;
                fputcsv($file, [
                    $prof?->nama_lengkap ?? '-',
                    $sub->user->email,
                    $prof?->nik ?? '-',
                    $prof?->tempat_lahir ?? '-',
                    $prof?->tanggal_lahir ?? '-',
                    $prof?->jenis_kelamin ?? '-',
                    $prof?->alamat ?? '-',
                    $prof?->no_hp ?? '-',
                    $prof?->status_pernikahan ?? '-',
                    $prof?->jumlah_tanggungan ?? 0,
                    $prof?->pekerjaan ?? '-',
                    $prof?->status_pekerjaan ?? '-',
                    $prof?->lama_bekerja_bulan ?? 0,
                    $prof?->penghasilan_bulanan ?? 0,
                    $prof?->penghasilan_pasangan ?? 0,
                    $prof?->pengeluaran_bulanan ?? 0,
                    $prof?->cicilan_lain ?? 0,
                    $prof?->riwayat_kredit ?? '-',
                    $sub->harga_rumah,
                    $sub->uang_muka_dp,
                    $sub->nilai_pinjaman,
                    $sub->tenor_tahun,
                    $sub->status_keputusan ?? 'Belum Dianalisis'
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function import(Request $request, SmartService $smartService)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:2048',
        ]);

        $file = $request->file('file');
        $handle = fopen($file->path(), 'r');
        $header = fgetcsv($handle); // skip header

        DB::beginTransaction();
        try {
            while (($row = fgetcsv($handle)) !== false) {
                // Pastikan format sesuai dengan template
                if (count($row) < 22) continue;

                // 1. Create User
                $user = User::firstOrCreate(
                    ['email' => trim($row[1])],
                    [
                        'name' => trim($row[0]),
                        'phone' => trim($row[7]),
                        'role' => 'debitur',
                        'password' => Hash::make('password123'),
                    ]
                );

                // 2. Create/Update Profile
                NasabahProfile::updateOrCreate(
                    ['nik' => trim($row[2])],
                    [
                        'user_id' => $user->id,
                        'nama_lengkap' => trim($row[0]),
                        'tempat_lahir' => trim($row[3]),
                        'tanggal_lahir' => trim($row[4]),
                        'jenis_kelamin' => trim($row[5]),
                        'alamat' => trim($row[6]),
                        'no_hp' => trim($row[7]),
                        'status_pernikahan' => trim($row[8]),
                        'jumlah_tanggungan' => (int)trim($row[9]),
                        'pekerjaan' => trim($row[10]),
                        'status_pekerjaan' => trim($row[11]),
                        'lama_bekerja_bulan' => (int)trim($row[12]),
                        'penghasilan_bulanan' => (float)trim($row[13]),
                        'penghasilan_pasangan' => (float)trim($row[14]),
                        'pengeluaran_bulanan' => (float)trim($row[15]),
                        'cicilan_lain' => (float)trim($row[16]),
                        'riwayat_kredit' => trim($row[17]),
                    ]
                );

                // 3. Create Submission
                $noPengajuan = 'NAS-' . date('Y') . '-' . Str::padLeft(KprSubmission::count() + 1, 3, '0');
                $submission = KprSubmission::create([
                    'no_pengajuan' => $noPengajuan,
                    'user_id' => $user->id,
                    'harga_rumah' => (float)trim($row[18]),
                    'uang_muka_dp' => (float)trim($row[19]),
                    'nilai_pinjaman' => (float)trim($row[20]),
                    'tenor_tahun' => (int)trim($row[21]),
                    'status_pengajuan' => 'pending',
                ]);

                // Auto Verify C1 if Lancar so it gets analyzed
                if (in_array(trim($row[17]), ['Lancar', 'Dalam Perhatian Khusus', 'Kurang Lancar', 'Diragukan', 'Macet'])) {
                    $submission->update([
                        'c1_riwayat_kredit' => trim($row[17]),
                        'c1_verified_at' => now(),
                        'c1_verified_by' => auth()->id() ?? 1,
                    ]);
                    $smartService->analyzeSubmission($submission);
                }
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal import: ' . $e->getMessage());
        }

        return redirect()->back()->with('success', 'Data CSV berhasil di-import!');
    }

    public function exportPdf()
    {
        $submissions = KprSubmission::with(['user.profile', 'smartResult'])
            ->orderBy('created_at', 'desc')
            ->get();
            
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('manager.submissions.pdf', compact('submissions'))
            ->setPaper('a4', 'landscape');
            
        return $pdf->download('Laporan_Data_Nasabah.pdf');
    }


}
