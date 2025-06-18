<?php

namespace App\Http\Controllers;

use App\Models\Semester;
use App\Models\Kompensasi;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $detail = $user->detailMahasiswa;

        $lamaStudi = null;
        $tahunMasuk = null;
        $jumlahSemesterBerjalan = 0;
        $kompensasiPerSemester = [];

        if ($detail && $detail->prodi) {
            $lamaStudi = $detail->prodi->lama_studi;
            $tahunMasuk = $detail->tahun_masuk;

            $semesterAktif = Semester::where('aktif', true)->first();

            if ($semesterAktif) {
                $tahunAjaranSekarang = intval(substr($semesterAktif->tahun_ajaran, 0, 4));
                $semesterType = strtolower($semesterAktif->semester);

                $selisihTahun = $tahunAjaranSekarang - $tahunMasuk;
                $jumlahSemesterBerjalan = $selisihTahun * 2 + ($semesterType == 'genap' ? 2 : 1);

                $semesterMax = min($jumlahSemesterBerjalan, $lamaStudi);

                for ($i = 1; $i <= $semesterMax; $i++) {
                    $menit = Kompensasi::where('user_id', $user->id)
                            ->where('semester_lokal', $i)
                            ->sum('menit_kompensasi');
                    $kompensasiPerSemester[$i] = $menit;
                }
            }
        }

        // dd($lamaStudi);

        return view('admin.index', compact('lamaStudi', 'kompensasiPerSemester'));
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

    public function datatable(Request $request)
    {
        if (!$request->filled('semester')) {
            return DataTables::of(collect([]))->make(true);
        }

        $user = Auth::user();

        $query = Kompensasi::with(['dosenMatakuliah.matakuliah'])
            ->where('user_id', $user->id)
            ->where('semester_lokal', $request->semester);

        return DataTables::of($query)
            ->addIndexColumn() // untuk penomoran otomatis
            ->addColumn('nama_matakuliah', function ($row) {
                return optional(optional($row->dosenMatakuliah)->matakuliah)->nama ?? '-';
            })
            ->addColumn('menit_kompensasi', function ($row) {
                return $row->menit_kompensasi;
            })
            ->addColumn('keterangan', function ($row) {
                return $row->keterangan ?? '-';
            })
            ->addColumn('action', function ($row) {
                return '<button class="btn btn-success btn-sm" onclick="bayarKompen(' . $row->id . ')"><i class="fe fe-edit"></i> Bayar Kompen</button>';
            })
            ->make(true);

    }

}
