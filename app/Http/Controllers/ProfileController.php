<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class ProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        return view('admin.profile.index', compact('user'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'firstName' => 'required|string|max:255',
            'lastName' => 'required|string|max:255',
            'jenis_kelamin' => 'required|string|max:10',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'jabatan_fungsional' => 'nullable|string|max:255',
            'bidang_keahlian' => 'nullable|string|max:255',
        ]);

        $user = auth()->user();

        if ($user->hasRole('Mahasiswa')) {
            $mahasiswa = $user->detailMahasiswa;

            if (!$mahasiswa) {
                // Buat data baru jika belum ada
                $mahasiswa = $user->detailMahasiswa()->create([
                    'first_name' => $request->firstName,
                    'last_name' => $request->lastName,
                    'jenis_kelamin' => $request->jenis_kelamin,
                ]);
            } else {
                $mahasiswa->update([
                    'first_name' => $request->firstName,
                    'last_name' => $request->lastName,
                    'jenis_kelamin' => $request->jenis_kelamin,
                ]);
            }

            // Upload foto jika ada
            if ($request->hasFile('profile_image')) {
                $path = $request->file('profile_image')->store('profile_images_mahasiswa', 'public');
                $mahasiswa->update(['file_path' => $path]);
            }

        } elseif ($user->hasRole('Dosen')) {
            $dosen = $user->detailDosen;

            if (!$dosen) {
                $dosen = $user->detailDosen()->create([
                    'first_name' => $request->firstName,
                    'last_name' => $request->lastName,
                    'jenis_kelamin' => $request->jenis_kelamin,
                    'jabatan_fungsional' => $request->jabatan_fungsional,
                    'bidang_keahlian' => $request->bidang_keahlian,
                ]);
            } else {
                $dosen->update([
                    'first_name' => $request->firstName,
                    'last_name' => $request->lastName,
                    'jenis_kelamin' => $request->jenis_kelamin,
                    'jabatan_fungsional' => $request->jabatan_fungsional,
                    'bidang_keahlian' => $request->bidang_keahlian,
                ]);
            }

            if ($request->hasFile('profile_image')) {
                $path = $request->file('profile_image')->store('profile_images_dosen', 'public');
                $dosen->update(['file_path' => $path]);
            }
        }

        return redirect()->route('profile.index')->with('success', 'Profil berhasil diperbarui');
    }


    public function ubahPassword(Request $request)
    {
        $request->validate([
            'password_lama' => ['required'],
            'password_baru' => ['required', 'min:6', 'confirmed'],
        ]);

        $user = auth()->user();

        if (!Hash::check($request->password_lama, $user->password)) {
            return back()->withErrors(['password_lama' => 'Password lama tidak sesuai.']);
        }

        $user->password = bcrypt($request->password_baru);
        $user->save();

        Session::flash('success', 'Password berhasil diperbarui. Silakan login kembali.');

        Auth::logout();

        return redirect()->route('mahasiswa.login')->with('success', 'Password berhasil diperbarui. Silakan login kembali.');
    }




}
