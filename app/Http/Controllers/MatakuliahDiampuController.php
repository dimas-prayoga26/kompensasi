<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Kelas;
use App\Models\Semester;
use App\Models\Kompensasi;
use App\Models\Matakuliah;
use App\Models\DetailDosen;
use Illuminate\Http\Request;
use App\Models\DosenMatakuliah;
use App\Exports\KompensasiExport;
use App\Models\MatakuliahSemester;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TugasKompensasiExport;
use App\Models\KelasSemesterMahasiswa;

class MatakuliahDiampuController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $dosen = User::role('Dosen')->with('detailDosen')->get();

        $semesterAktif = Semester::where('aktif', 1)->first();

        return view('admin.matakuliah-diampu.index', compact('dosen', 'semesterAktif'));
    }



    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // dd($request);
        $request->validate([
            'dosen_id' => 'required|exists:users,id',
            'matakuliah_id' => 'required|exists:matakuliahs,id',
            'kelas_id' => 'required|exists:kelas,id',
        ]);

        DB::beginTransaction();

        try {
            $semester = Semester::where('aktif', true)->first();

            if (!$semester) {
                throw new \Exception('Semester aktif tidak ditemukan.');
            }

            $kelas = Kelas::findOrFail($request->kelas_id);
            $namaKelas = $kelas->nama;

            $dosenMatakuliah = DosenMatakuliah::create([
                'dosen_id' => $request->dosen_id,
                'matakuliah_id' => $request->matakuliah_id,
                'kelas_id' => $request->kelas_id,
                'semester_id' => $semester->id,
            ]);


            $matkulSemester = MatakuliahSemester::where('matakuliah_id', $request->matakuliah_id)->first();

            $semesterLokal = $matkulSemester ? $matkulSemester->semester_lokal : null;

           $mahasiswaList = User::role('Mahasiswa')
                ->whereHas('detailMahasiswa', function ($query) use ($namaKelas) {
                    $query->where('kelas', $namaKelas);
                })
                ->whereHas('kelasSemesterMahasiswas', function ($query) use ($semesterLokal) {
                    $query->where('is_active', true)
                        ->where('semester_lokal', $semesterLokal);
                })
                ->get();

            if ($mahasiswaList->isEmpty()) {
                throw new \Exception('Tidak ada mahasiswa aktif di semester tersebut. Semester belum berlangsung.');
            }


            foreach ($mahasiswaList as $mahasiswa) {
                Kompensasi::create([
                    'user_id' => $mahasiswa->id,
                    'dosen_matakuliah_id' => $dosenMatakuliah->id,
                    'menit_kompensasi' => 0,
                    'keterangan' => null,
                    'is_active' => true,
                    'semester_lokal' => $semesterLokal,
                ]);
            }


            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'matakuliah yg diampun berhasil ditambahkan.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => 'Gagal menyimpan data matakuliah yg diampun: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $dosenMatakuliah = DosenMatakuliah::with([
            'dosen.detailDosen',
            'matakuliah',
            'kelas',
            'kompensasis.user.detailMahasiswa'
        ])->findOrFail($id);

        return view('admin.matakuliah-diampu.detail', compact('dosenMatakuliah'));
    }



    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            DB::beginTransaction();

            Kompensasi::where('dosen_matakuliah_id', $id)
                ->where('is_active', true)
                ->update(['is_active' => false]); // ✅ Hanya ubah status, tidak menghapus

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => "Berhasil menonaktifkan data kompensasi."
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan saat menonaktifkan data: ' . $e->getMessage()
            ], 500);
        }
    }


    public function datatable(Request $request)
    {
        if (auth()->user()->hasRole('Dosen')) {
            $data = DosenMatakuliah::with([
                'dosen.detailDosen', 
                'matakuliah.matakuliahSemesters', 
                'kelas', 
                'semesters'
            ])
            ->whereHas('dosen', function($query) {
                $query->where('dosen_id', auth()->user()->id);
            })
            ->get();
        } elseif (auth()->user()->hasRole('superAdmin')) {
            $data = DosenMatakuliah::with([
                'dosen.detailDosen', 
                'matakuliah.matakuliahSemesters', 
                'kelas', 
                'semesters'
            ]);

            if ($request->has('dosen_id') && $request->dosen_id != '') {
                $data->whereHas('dosen', function($query) use ($request) {
                    $query->where('dosen_id', $request->dosen_id);
                });
            }

            $data = $data->get();
        } else {
            return response()->json(['error' => 'Anda tidak memiliki akses'], 403);
        }

        return datatables()->of($data)
            ->addColumn('dosen_name', function ($row) {
                $detail = $row->dosen->detailDosen;
                return $detail ? $detail->first_name . ' ' . $detail->last_name : 'Nama Dosen Tidak Ditemukan';
            })
            ->addColumn('matakuliah_name', function ($row) {
                return $row->matakuliah ? $row->matakuliah->nama : 'Matakuliah Tidak Ditemukan';
            })
            ->addColumn('kelas_name', function ($row) {
                return $row->kelas ? $row->kelas->nama : 'Kelas Tidak Ditemukan';
            })
            ->addColumn('semester_lokal', function ($row) {
                return optional($row->matakuliah->matakuliahSemesters)->semester_lokal ?? 'Semester Lokal Tidak Ditemukan';
            })
            ->make(true);
    }


    public function select2Kelas(Request $request)
    {
        $query = $request->get('q');

        $kelass = Kelas::where('nama', 'like', "%{$query}%")->get();

        $results = $kelass->map(function($kelas) {
            return [
                'id' => $kelas->id,
                'text' => $kelas->nama
            ];
        });

        return response()->json(['results' => $results]); // ✅ PERBAIKI DI SINI
    }


    public function select2Dosen(Request $request)
    {
        $searchTerm = $request->get('q');

        $dosen = User::role('Dosen')
            ->with('detailDosen')
            ->where(function ($subQuery) use ($searchTerm) {
                $subQuery->where('nip', 'like', "%{$searchTerm}%")
                        ->orWhereHas('detailDosen', function($innerQuery) use ($searchTerm) {
                            $innerQuery->where('first_name', 'like', "%{$searchTerm}%")
                                        ->orWhere('last_name', 'like', "%{$searchTerm}%");
                        });
            })
            ->get();

        $results = $dosen->map(function ($user) {
            $fullName = $user->detailDosen ? $user->detailDosen->first_name . ' ' . $user->detailDosen->last_name : 'Data Tidak Tersedia';
            return [
                'id' => $user->id,
                'text' => $fullName . ' - ' . $user->nip 
            ];
        });

        return response()->json(['results' => $results]);
    }

    public function select2Matakuliah(Request $request)
    {
        $query = $request->get('q');

        $matakuliahs = Matakuliah::with(['matakuliahSemesters'])
            ->where('nama', 'like', "%{$query}%")
            ->get();

        $results = $matakuliahs->map(function ($matakuliah) {
            $semesterLokal = optional($matakuliah->matakuliahSemesters)->semester_lokal;

            $text = $matakuliah->nama;
            if ($semesterLokal) {
                $text .= ' (Semester ' . $semesterLokal . ')';
            }

            return [
                'id' => $matakuliah->id,
                'text' => $text
            ];
        });

        return response()->json(['results' => $results]);
    }

    public function datatableKompensasi($id)
    {
        $data = Kompensasi::with([
                'user.detailMahasiswa',
                'dosenMatakuliah.matakuliah',
            ])
            ->where('dosen_matakuliah_id', $id)
            ->where('is_active', true)
            ->get();

        return datatables()->of($data)
            ->addColumn('nama_mahasiswa', function ($row) {
                $first = $row->user->detailMahasiswa->first_name ?? '-';
                $last = $row->user->detailMahasiswa->last_name ?? '';
                return trim($first . ' ' . $last);
            })
            ->addColumn('menit_kompensasi', function ($row) {
                return $row->menit_kompensasi . ' menit';
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }


    public function kompensasiDetail(string $id)
    {
        try {
            DB::beginTransaction();

            $kompensasi = Kompensasi::with([
                'user.detailMahasiswa',
                'dosenMatakuliah.matakuliah',
                'dosenMatakuliah.dosen.detailDosen'
            ])->find($id);

            if (!$kompensasi) {
                return response()->json([
                    'status' => false,
                    'message' => 'Data kompensasi tidak ditemukan.'
                ], 404);
            }

            DB::commit();

            return response()->json([
                'status' => true,
                'data' => $kompensasi
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => 'Data kelas tidak ditemukan atau terjadi kesalahan.',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    public function kompensasiUpdate(Request $request, $id)
    {
        // dd($request->all());
        $request->validate([
            'menit_kompensasi' => 'required|integer|min:0',
            'keterangan' => 'nullable|string'
        ]);

        try {
            $kompensasi = Kompensasi::findOrFail($id);

            $kompensasi->update([
                'menit_kompensasi' => $request->menit_kompensasi,
                'keterangan' => $request->keterangan
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Data kompensasi berhasil diperbarui.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Gagal memperbarui data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getTahunAjaranLamaBaru($id)
    {
        $dosenMatakuliah = DosenMatakuliah::findOrFail($id);
        $semesterLama = Semester::findOrFail($dosenMatakuliah->semester_id);

        $kompensasiAktifAda = Kompensasi::where('dosen_matakuliah_id', $dosenMatakuliah->id)
            ->where('is_active', true)
            ->exists();

        $semesterBaru = Semester::where('aktif', true)
            ->where('id', '!=', $semesterLama->id)
            ->where('tahun_ajaran', '>', $semesterLama->tahun_ajaran)
            ->first();

        if (!$semesterBaru && $kompensasiAktifAda) {
            return response()->json([
                'status' => false,
                'tahun_ajaran_lama' => $semesterLama->tahun_ajaran,
                'kompensasi_aktif_ada' => $kompensasiAktifAda,
                'message' => 'Belum ada tahun ajaran baru yang aktif untuk melakukan pembaruan.'
            ]);
        }

        return response()->json([
            'status' => true,
            'tahun_ajaran_lama' => $semesterLama->tahun_ajaran,
            'tahun_ajaran_baru' => $semesterBaru ? $semesterBaru->tahun_ajaran : null,
            'kompensasi_aktif_ada' => $kompensasiAktifAda
        ]);
    }


    public function refreshKompensasi($id)
    {
        DB::beginTransaction();

        try {
            $dosenMatakuliah = DosenMatakuliah::with(['kelas', 'matakuliah', 'kompensasis'])->findOrFail($id);
            $namaKelas = $dosenMatakuliah->kelas->nama;

            $kompensasiAktifAda = Kompensasi::where('dosen_matakuliah_id', $dosenMatakuliah->id)
                ->where('is_active', true)
                ->exists();

            $matkulSemester = MatakuliahSemester::where('matakuliah_id', $dosenMatakuliah->matakuliah_id)->first();
            $semesterLokal = $matkulSemester ? $matkulSemester->semester_lokal : null;

            
            if (!$kompensasiAktifAda) {
                $kelasId = $dosenMatakuliah->kelas_id;

                $mahasiswaAktif = KelasSemesterMahasiswa::where('kelas_id', $kelasId)
                    ->where('semester_lokal', $semesterLokal)
                    ->where('is_active', 1)
                    ->pluck('user_id');

                    
                    if ($mahasiswaAktif->isEmpty()) {
                        return response()->json([
                            'status' => false,
                            'message' => 'Tidak ada mahasiswa aktif pada semester ini.'
                        ]);
                    }

                foreach ($mahasiswaAktif as $userId) {
                    Kompensasi::create([
                        'user_id' => $userId,
                        'dosen_matakuliah_id' => $dosenMatakuliah->id,
                        'menit_kompensasi' => 0,
                        'keterangan' => null,
                        'is_active' => true,
                        'semester_lokal' => $semesterLokal
                    ]);
                }

                DB::commit();

                return response()->json([
                    'status' => true,
                    'message' => 'Data kompensasi berhasil dibuat hanya untuk mahasiswa semester ' . $semesterLokal
                ]);
            } elseif ($kompensasiAktifAda) {

                $semesterBaru = Semester::where('aktif', true)
                    ->where('id', '!=', $dosenMatakuliah->semester_id)
                    ->first();

                if (!$semesterBaru) {
                    throw new \Exception('Semester aktif baru tidak ditemukan.');
                }

                $dosenMatakuliah->semester_id = $semesterBaru->id;
                $dosenMatakuliah->save();

                Kompensasi::where('dosen_matakuliah_id', $dosenMatakuliah->id)
                    ->update(['is_active' => false]);

                $mahasiswaBaru = User::role('Mahasiswa')
                    ->whereHas('detailMahasiswa', function ($query) use ($namaKelas) {
                        $query->where('kelas', $namaKelas);
                    })
                    ->whereHas('kelasSemesterMahasiswas', function ($query) {
                        $query->where('is_active', true);
                    })
                    ->get();

                foreach ($mahasiswaBaru as $mahasiswa) {
                    Kompensasi::create([
                        'user_id' => $mahasiswa->id,
                        'dosen_matakuliah_id' => $dosenMatakuliah->id,
                        'menit_kompensasi' => 0,
                        'keterangan' => null,
                        'is_active' => true,
                        'semester_lokal' => $semesterLokal
                    ]);
                }

                DB::commit();
                return response()->json([
                    'status' => true,
                    'message' => 'Data kompensasi berhasil diperbarui untuk semester baru.'
                ]);
            } else {
                // CASE fallback tidak terduga
                throw new \Exception('Kondisi tidak diketahui saat memproses kompensasi.');
            }

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Gagal memperbarui data kompensasi: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function editDataMahasiswaAktif($id)
    {
        try {
            $dosenMatakuliah = DosenMatakuliah::with(['kelas', 'matakuliah', 'kompensasis'])->findOrFail($id);

            $semesterLokal = optional($dosenMatakuliah->kompensasis->first())->semester_lokal;
            $kelasId = $dosenMatakuliah->kelas_id;

            $mahasiswaAktif = KelasSemesterMahasiswa::where('kelas_id', $kelasId)
                ->where('semester_lokal', $semesterLokal)
                ->where('is_active', 1)
                ->pluck('user_id');

            $sudahAda = Kompensasi::where('dosen_matakuliah_id', $id)
                ->pluck('user_id');
                
            $belumAda = $mahasiswaAktif->diff($sudahAda);


            if ($belumAda->isEmpty()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Tidak ada mahasiswa inaktif.'
                ]);
            }

            foreach ($belumAda as $userId) {
                Kompensasi::create([
                    'user_id' => $userId,
                    'dosen_matakuliah_id' => $dosenMatakuliah->id,
                    'menit_kompensasi' => 0,
                    'keterangan' => null,
                    'is_active' => true,
                    'semester_lokal' => $semesterLokal,
                ]);
            }

            return response()->json([
                'status' => true,
                'message' => $belumAda->count() . ' mahasiswa berhasil ditambahkan ke tabel kompensasi.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Gagal menambahkan data kompensasi: ' . $e->getMessage()
            ], 500);
        }
    }

    public function exportExcel($id)
    {
        $dosenMatakuliah = DosenMatakuliah::with(['matakuliah', 'kelas', 'kompensasis'])
                                        ->findOrFail($id);
        return Excel::download(new KompensasiExport($dosenMatakuliah), 'Daftar_Mahasiswa_Kompen_' . $dosenMatakuliah->matakuliah->nama . '.xlsx');

    }

}
