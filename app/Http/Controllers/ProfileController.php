<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        return view('admin.profile.index', compact('user'));
    }

    public function update(Request $request)
    {
        // dd($request->all());
        // Validasi data
        $request->validate([
            'firstName' => 'required|string|max:255',
            'lastName' => 'required|string|max:255',
            'jenis_kelamin' => 'required|string|max:10',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Validasi gambar
        ]);

        // Mendapatkan pengguna yang sedang login
        $user = auth()->user();

        // Update data profil (nama depan, nama belakang, jenis kelamin, dll.)
        if ($user->hasRole('Mahasiswa')) {
            $user->detailMahasiswa->update([
                'first_name' => $request->firstName,
                'last_name' => $request->lastName,
                'jenis_kelamin' => $request->jenis_kelamin,
            ]);
        } elseif ($user->hasRole('Dosen')) {
            $user->detailDosen->update([
                'first_name' => $request->firstName,
                'last_name' => $request->lastName,
                'jenis_kelamin' => $request->jenis_kelamin, // Update jenis kelamin
            ]);
        }

        // Menangani upload gambar profil
        if ($request->hasFile('profile_image')) {
            $folder = $user->hasRole('Mahasiswa') ? 'profile_images_mahasiswa' : 'profile_images_dosen';

            // Menyimpan gambar di direktori public dan mendapatkan path-nya
            $path = $request->file('profile_image')->store($folder, 'public');
            
            // Update path gambar di database
            if ($user->hasRole('Mahasiswa')) {
                $user->detailMahasiswa->update(['file_path' => $path]);
            } elseif ($user->hasRole('Dosen')) {
                $user->detailDosen->update(['file_path' => $path]);
            }
        }

        // Menyimpan pesan sukses ke sesi
        return redirect()->route('profile.index')->with('success', 'Profil berhasil diperbarui');
    }



}
