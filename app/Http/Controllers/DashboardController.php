<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        return view('admin.index'); // View untuk form login
    }

    public function authLogout()
    {
        $role = Auth::user()?->getRoleNames()->first();

        Auth::logout();

        if ($role === 'Mahasiswa') {
            return response()->json(['redirect_url' => route('mahasiswa.login')]);
        }

        if ($role === 'Dosen') {
            return response()->json(['redirect_url' => route('dosen.login')]);
        }

        return response()->json(['redirect_url' => route('mahasiswa.login')]);
    }

}
