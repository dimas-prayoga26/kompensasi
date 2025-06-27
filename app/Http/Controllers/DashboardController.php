<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Kelas;
use App\Models\Semester;
use App\Models\Kompensasi;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Auth;
use App\Models\KelasSemesterMahasiswa;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $lamaStudi = null;
        $kompensasiPerSemester = [];
        $kelasAkhir = [];

        // Inisialisasi jumlah mahasiswa aktif & tidak aktif (khusus untuk superAdmin)
        $mahasiswaAktif = 0;
        $mahasiswaTidakAktif = 0;

        // Cek role menggunakan Spatie
        if ($user->hasRole('Mahasiswa')) {
            $detail = $user->detailMahasiswa;

            if ($detail && $detail->prodi) {
                $lamaStudi = $detail->prodi->lama_studi;
                $tahunMasuk = $detail->tahun_masuk;

                $semesterAktif = Semester::where('aktif', true)->first();

                if ($semesterAktif) {
                    $tahunAjaranSekarang = intval(substr($semesterAktif->tahun_ajaran, 0, 4));
                    $semesterType = strtolower($semesterAktif->semester);

                    $selisihTahun = $tahunAjaranSekarang - $tahunMasuk;
                    $jumlahSemesterBerjalan = $selisihTahun * 2 + ($semesterType === 'genap' ? 2 : 1);

                    $semesterMax = min($jumlahSemesterBerjalan, $lamaStudi);

                    for ($i = 1; $i <= $semesterMax; $i++) {
                        $menit = Kompensasi::where('user_id', $user->id)
                            ->where('semester_lokal', $i)
                            ->sum('menit_kompensasi');
                        $kompensasiPerSemester[$i] = $menit;
                    }
                }
            }

        } elseif ($user->hasRole('superAdmin')) {
            // Ambil kelas akhir
            $kelasAkhir = Kelas::where(function ($query) {
                $query->where('nama', 'like', 'TI3%')
                    ->orWhere('nama', 'like', 'RPL4%');
            })->get();

            // Hitung jumlah mahasiswa aktif & tidak aktif
            $mahasiswaAktif = KelasSemesterMahasiswa::where('is_active', true)
                ->distinct('user_id')
                ->count('user_id');

            $mahasiswaTidakAktif = KelasSemesterMahasiswa::where('is_active', false)
                ->distinct('user_id')
                ->count('user_id');

        }

        return view('admin.index', compact(
            'lamaStudi',
            'kompensasiPerSemester',
            'kelasAkhir',
            'mahasiswaAktif',
            'mahasiswaTidakAktif'
        ));
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

    public function mahasiswaDashboardDatatable(Request $request)
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

    public function adminDashboardDatatable(Request $request)
    {
        $kelasId = $request->input('kelas_id');

        if (!$kelasId) {
            return DataTables::of([])->make(true);
        }

        // Ambil nama kelas berdasarkan ID
        $kelas = Kelas::find($kelasId);
        if (!$kelas) {
            return DataTables::of([])->make(true);
        }

        $namaKelas = $kelas->nama; // contoh: TI3A, RPL4B

        // Ambil semester aktif
        $semesterAktif = Semester::where('aktif', true)->first();
        if (!$semesterAktif) {
            return DataTables::of([])->make(true);
        }

        $tahunSekarang = intval(substr($semesterAktif->tahun_ajaran, 0, 4));
        $tipeSemester = strtolower($semesterAktif->semester);
        $semesterBerjalan = ($tahunSekarang - 2000) * 2 + ($tipeSemester === 'genap' ? 2 : 1);

        $mahasiswa = User::role('Mahasiswa')
            ->whereHas('detailMahasiswa', function ($query) use ($namaKelas) {
                $query->where('kelas', $namaKelas);
            })
            ->with('detailMahasiswa.prodi')
            ->get();

        $data = $mahasiswa->map(function ($user) use ($semesterBerjalan) {
            $detail = $user->detailMahasiswa;

            if (!$detail || !$detail->prodi || !$detail->tahun_masuk) {
                return null;
            }

            $tahunMasuk = $detail->tahun_masuk;
            $lamaStudi = $detail->prodi->lama_studi;
            $semesterMax = min($semesterBerjalan, $lamaStudi);

            $totalKompensasi = Kompensasi::where('user_id', $user->id)
                ->whereBetween('semester_lokal', [1, $semesterMax])
                ->sum('menit_kompensasi');

            return [
                'mahasiswa' => "{$detail->first_name} {$detail->last_name}",
                'jumlah' => $totalKompensasi . ' menit',
            ];
        })->filter()->values();

        return DataTables::of($data)
            ->addIndexColumn()
            ->make(true);
    }

}
