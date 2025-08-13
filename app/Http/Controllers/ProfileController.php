<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BidangKeahlian;
use App\Models\JabatanFungsional;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class ProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $jabatanFungsionalList = JabatanFungsional::all();
        $bidangKeahlianList = BidangKeahlian::all();

        return view('admin.profile.index', compact('user', 'jabatanFungsionalList', 'bidangKeahlianList'));
    }

    public function update(Request $request)
    {

        // dd($request);
        $request->validate([
            'firstName' => 'required|string|max:255',
            'lastName' => 'required|string|max:255',
            'jenis_kelamin' => 'required|string|max:10',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'jabatan_fungsional_id' => 'nullable|exists:jabatan_fungsionals,id',
            'bidang_keahlian_id' => 'nullable|exists:bidang_keahlians,id',
        ]);

        // Debug untuk melihat apakah request sudah benar
        // dd($request->all());  // Uncomment untuk melihat semua data request

        $user = auth()->user();

        // Update detail mahasiswa jika role Mahasiswa
        if ($user->hasRole('Mahasiswa')) {
            $mahasiswa = $user->detailMahasiswa;

            if (!$mahasiswa) {
                // Buat data mahasiswa baru jika belum ada
                $mahasiswa = $user->detailMahasiswa()->create([
                    'first_name' => $request->firstName,
                    'last_name' => $request->lastName,
                    'jenis_kelamin' => $request->jenis_kelamin,
                ]);
            } else {
                // Update data mahasiswa
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
            // Update detail dosen jika role Dosen
            $dosen = $user->detailDosen;

            if (!$dosen) {
                // Buat data dosen baru jika belum ada
                $dosen = $user->detailDosen()->create([
                    'first_name' => $request->firstName,
                    'last_name' => $request->lastName,
                    'jenis_kelamin' => $request->jenis_kelamin,
                    'jabatan_fungsional_id' => $request->jabatan_fungsional_id ?? null, // Periksa jika null
                    'bidang_keahlian_id' => $request->bidang_keahlian_id ?? null, // Periksa jika null
                ]);
            } else {
                // Update data dosen
                $dosen->update([
                    'first_name' => $request->firstName,
                    'last_name' => $request->lastName,
                    'jenis_kelamin' => $request->jenis_kelamin,
                    'jabatan_fungsional_id' => $request->jabatan_fungsional_id ?? $dosen->jabatan_fungsional_id, // Gunakan nilai lama jika NULL
                    'bidang_keahlian_id' => $request->bidang_keahlian_id ?? $dosen->bidang_keahlian_id, // Gunakan nilai lama jika NULL
                ]);
            }

            // Upload foto jika ada
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
