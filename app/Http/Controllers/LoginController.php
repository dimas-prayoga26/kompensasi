<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\DetailUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function showLoginForm(Request $request)
    {

        if ($request->is('mahasiswa/*')) {
        $tipe = 'mahasiswa';
        } elseif ($request->is('dosen/*')) {
            $tipe = 'dosen';
        } else {
            $tipe = 'umum';
        }
        
        return view('auth.login', compact('tipe'));
    }

    public function login(Request $request)
    {
        $tipe = $request->is('mahasiswa/*') ? 'nim' : 'nip';

        $request->validate([
            $tipe => 'required|string',
            'password' => 'required|string',
        ]);

        $user = User::where($tipe, $request->$tipe)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return redirect()->route($tipe === 'nim' ? 'mahasiswa.login' : 'dosen.login')
                            ->withInput($request->only($tipe))
                            ->with('error', 'Login gagal! NIM/NIP atau password salah.');
        }

        Auth::login($user);

        return redirect()->intended('/portal/dashboard')->with('success', 'Login berhasil!');
    }



}
