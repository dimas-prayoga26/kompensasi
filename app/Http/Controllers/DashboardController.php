<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Kelas;
use App\Models\Semester;
use App\Models\Kompensasi;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\KelasSemesterMahasiswa;
use App\Exports\RekapKompensasiAllSemester;

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
            $kelasAkhir = Kelas::where(function ($query) {
                $query->where('nama', 'like', 'TI3%')
                    ->orWhere('nama', 'like', 'RPL4%');
            })->get();

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
        $user = Auth::user();

        $query = Kompensasi::with(['dosenMatakuliah.matakuliah', 'user.detailMahasiswa'])
            ->where('user_id', $user->id);

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('nama_matakuliah', function ($row) {
                return optional(optional($row->dosenMatakuliah)->matakuliah)->nama ?? '-';
            })
            ->addColumn('menit_kompensasi', function ($row) {
                return $row->menit_kompensasi;
            })
            ->addColumn('semester', function ($row) {
                $semesterLokal = $row->semester_lokal;
                $isActive = $row->is_active;

                if ($isActive == 0) {
                    return "Semester {$semesterLokal} (Selesai)";
                }

                return "Semester {$semesterLokal} (Sedang Berlangsung)";
            })
            ->addColumn('keterangan', function ($row) {
                return $row->keterangan ?? '-';
            })
            ->make(true);
    }



    public function adminDashboardDatatable(Request $request)
    {
        $kelasId = $request->input('kelas_id');

        if (!$kelasId) {
            return DataTables::of([])->make(true);
        }

        $kelas = Kelas::find($kelasId);
        if (!$kelas) {
            return DataTables::of([])->make(true);
        }

        $namaKelas = $kelas->nama;

        $semesterAktif = Semester::where('aktif', true)->first();
        if (!$semesterAktif) {
            return DataTables::of([])->make(true);
        }

        $tahunAjaranPalingLama = Semester::orderBy('tahun_ajaran', 'asc')->first()->tahun_ajaran;

        $tahunSekarang = intval(substr($semesterAktif->tahun_ajaran, 0, 4));

        $tipeSemester = strtolower($semesterAktif->semester);

        $tahunAwal = intval(substr($tahunAjaranPalingLama, 0, 4));
        $semesterBerjalan = ($tahunSekarang - $tahunAwal) * 2 + ($tipeSemester === 'genap' ? 2 : 1);

        $mahasiswa = User::role('Mahasiswa')
            ->whereHas('detailMahasiswa', function ($query) use ($namaKelas) {
                $query->where('kelas', $namaKelas);
            })
            ->whereHas('kelasSemesterMahasiswas', function ($query) {
                $query->where('is_active', true);
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
            
            if ($semesterBerjalan < $lamaStudi) {
                return null;
            }

            $totalKompensasi = Kompensasi::where('user_id', $user->id)
                ->whereBetween('semester_lokal', [1, $semesterMax])
                ->sum('menit_kompensasi');

            return [
                'mahasiswa' => "{$detail->first_name} {$detail->last_name}",
                'angkatan' => "{$detail->tahun_masuk}",
                'jumlah' => $totalKompensasi . ' menit',
            ];
        })->filter()->values();

        return DataTables::of($data)
            ->addIndexColumn()
            ->make(true);
    }

    public function exportKompensasi(Request $request)
    {
        $kelasId = $request->input('kelas_id');

        $kelas = Kelas::find($kelasId);
        if (!$kelas) {
            return response()->json(['error' => 'Kelas tidak ditemukan'], 404);
        }

        $namaKelas = $kelas->nama;

        $semesterAktif = Semester::where('aktif', true)->first();
        if (!$semesterAktif) {
            return response()->json(['error' => 'Semester aktif tidak ditemukan'], 404);
        }

        $tahunAwal = intval(substr(Semester::orderBy('tahun_ajaran', 'asc')->first()->tahun_ajaran, 0, 4));

        $tahunSekarang = intval(substr($semesterAktif->tahun_ajaran, 0, 4));

        $tipeSemester = strtolower($semesterAktif->semester);

        $semesterBerjalan = ($tahunSekarang - $tahunAwal) * 2 + ($tipeSemester === 'genap' ? 2 : 1);

        $mahasiswa = User::role('Mahasiswa')
            ->whereHas('detailMahasiswa', function ($query) use ($namaKelas) {
                $query->where('kelas', $namaKelas);
            })
            ->whereHas('kelasSemesterMahasiswas', function ($query) {
                $query->where('is_active', true);
            })
            ->with('detailMahasiswa.prodi')
            ->get();

        $export = new RekapKompensasiAllSemester($mahasiswa, $semesterBerjalan);
        $fileName = 'rekap_kompensasi_mahasiswa_' . strtolower(str_replace(' ', '_', $kelas->nama)) . '.xlsx';

        $folderPath = 'rekap_kompensasi_akhir_semester';

        $filePath = $folderPath . '/' . $fileName;

        $storagePath = storage_path('app/public/' . $folderPath);

        if (!file_exists($storagePath)) {
            mkdir($storagePath, 0777, true);
        }

        Excel::store($export, $filePath, 'public');

        return response()->json([
            'fileUrl' => asset('storage/' . $filePath),
            'fileName' => $fileName,
        ]);
    }


}
