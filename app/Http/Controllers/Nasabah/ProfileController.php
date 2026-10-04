<?php

namespace App\Http\Controllers\Nasabah;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProfileUpdateRequest;
use App\Models\NasabahProfile;
use App\Services\UploadService;

class ProfileController extends Controller
{
    public function edit()
    {
        $user = auth()->user();
        $profile = $user->profile ?? new NasabahProfile();
        return view('nasabah.profile', compact('user', 'profile'));
    }

    public function update(ProfileUpdateRequest $request, UploadService $uploadService)
    {
        $user = auth()->user();
        $validated = $request->validated();

        if ($request->hasFile('foto')) {
            $user->avatar = $request->file('foto')->store('avatars', 'public');
            $user->save();
        }

        // Update user name as well
        if (isset($validated['nama_lengkap'])) {
            $user->name = $validated['nama_lengkap'];
            $user->save();
        }

        $profile = NasabahProfile::updateOrCreate(
            ['user_id' => $user->id],
            $validated
        );

        return redirect()->route('nasabah.dashboard')->with('success', 'Profil Anda berhasil diperbarui!');
    }
}
