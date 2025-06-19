<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Kelas;
use App\Models\Prodi;
use App\Models\Semester;
use App\Models\DetailDosen;
use Illuminate\Http\Request;
use App\Models\DetailMahasiswa;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{

    public function index()
    {
        return view('admin.metadata.user.index');
    }


    public function create()
    {

    }


    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            $role = $request->input('role');

            if ($role === 'mahasiswa') {
                $request->validate([
                    'nim' => 'required|digits_between:5,10|unique:users,nim',
                    'kelas_id' => 'required|exists:kelas,id',
                ]);

                $nim = $request->nim;

                $angkaTahun = (int) substr($nim, 0, 2);
                $tahunSekarang = (int) date('Y');
                $prefixTahun = substr($tahunSekarang, 0, 2);
                if ($angkaTahun > ((int) substr($tahunSekarang, 2, 2)) + 2) {
                    $prefixTahun = (string)((int)$prefixTahun - 1);
                }
                $tahunMasuk = (int) ($prefixTahun . str_pad($angkaTahun, 2, '0', STR_PAD_LEFT));

                $kodeProdi = substr($nim, 2, 2);
                $prodi = Prodi::where('kode_prodi', $kodeProdi)->first();
                if (!$prodi) {
                    return response()->json(['status' => false, 'message' => 'Prodi tidak ditemukan.'], 422);
                }

                $kelas = Kelas::find($request->kelas_id);
                if (!$kelas) {
                    return response()->json(['status' => false, 'message' => 'Kelas tidak ditemukan.'], 422);
                }

                $user = User::create([
                    'nim' => $nim,
                    'password' => bcrypt($nim),
                ]);
                $user->assignRole('Mahasiswa');

                $tahunAjaran = "{$tahunMasuk}/" . ($tahunMasuk + 1);
                Semester::firstOrCreate([
                    'tahun_ajaran' => $tahunAjaran,
                    'semester' => 'Ganjil',
                ], [
                    'no_semester' => 1
                ]);

                DetailMahasiswa::create([
                    'user_id' => $user->id,
                    'tahun_masuk' => $tahunMasuk,
                    'prodi_id' => $prodi->id,
                    'kelas' => $kelas->nama,
                ]);

                $semesterAktif = Semester::where('aktif', true)
                    ->orderByDesc('tahun_ajaran')
                    ->orderByDesc('id')
                    ->first();

                if (!$semesterAktif) {
                    DB::rollBack();
                    return response()->json([
                        'status' => false,
                        'message' => 'Semester aktif tidak ditemukan.'
                    ], 422);
                }

                KelasSemesterMahasiswa::create([
                    'user_id' => $user->id,
                    'semester_id' => $semesterAktif->id,
                    'kelas_id' => $kelas->id,
                    'semester_lokal' => 1,
                    'is_active' => true,
                ]);

                DB::commit();
                return response()->json(['status' => true, 'message' => 'Mahasiswa berhasil dibuat.']);
            }
            elseif ($role === 'dosen') {
                $request->validate([
                    'nip' => 'required|digits:18|unique:users,nip',
                ]);

                $nip = $request->nip;

                $user = User::create([
                    'nip' => $nip,
                    'password' => bcrypt($nip),
                ]);
                $user->assignRole('Dosen');

                DetailDosen::create([
                    'user_id' => $user->id,
                ]);

                DB::commit();
                return response()->json(['status' => true, 'message' => 'Dosen berhasil dibuat.']);
            }


            return response()->json(['status' => false, 'message' => 'Role tidak valid.'], 400);

        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function show(string $id)
    {
        try {
            DB::beginTransaction();

            $user = User::with(['detailMahasiswa.prodi', 'detailDosen', 'kelasSemesterMahasiswas'])->find($id);

            if (!$user) {
                DB::rollBack();
                return response()->json([
                    'status' => false,
                    'message' => 'User tidak ditemukan.'
                ], 404);
            }

            DB::commit();

            return response()->json([
                'status' => true,
                'data' => $user
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan: ' . $th->getMessage()
            ], 500);
        }
    }


    public function edit(string $id)
    {

    }


    public function update(Request $request, string $id)
    {
        try {
            DB::beginTransaction();

            $user = User::findOrFail($id);

            if ($user->hasRole('Mahasiswa')) {
                $request->validate([
                    'first_name' => 'required|string|max:255',
                    'last_name' => 'nullable|string|max:255',
                    'kelas' => 'required|string|max:50',
                ]);

                $detail = $user->detailMahasiswa;
                if (!$detail) {
                    throw new \Exception("Detail Mahasiswa tidak ditemukan.");
                }

                $detail->update([
                    'first_name' => $request->input('first_name'),
                    'last_name' => $request->input('last_name'),
                    'kelas' => $request->input('kelas'),
                ]);
            } elseif ($user->hasRole('Dosen')) {
                $request->validate([
                    'first_name' => 'required|string|max:255',
                    'last_name' => 'nullable|string|max:255',
                    'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
                    'jabatan_fungsional' => 'nullable|string|max:255',
                    'bidang_keahlian' => 'nullable|string|max:255',
                ]);

                $detail = $user->detailDosen;
                if (!$detail) {
                    throw new \Exception("Detail Dosen tidak ditemukan.");
                }

                $detail->update([
                    'first_name' => $request->input('first_name'),
                    'last_name' => $request->input('last_name'),
                    'jenis_kelamin' => $request->input('jenis_kelamin'),
                    'jabatan_fungsional' => $request->input('jabatan_fungsional'),
                    'bidang_keahlian' => $request->input('bidang_keahlian'),
                ]);
            } else {
                throw new \Exception("Role user tidak valid.");
            }

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Data berhasil diperbarui',
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => 'Gagal memperbarui data: ' . $e->getMessage(),
            ], 500);
        }
    }



    public function destroy(string $id)
    {
        try {

            $user = User::findOrFail($id);

            DB::beginTransaction();

            if ($user->detailMahasiswa) {
                $user->detailMahasiswa->delete();
            }

            if ($user->detailDosen) {
                $user->detailDosen->delete();
            }

            $user->delete();

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Data user berhasil dihapus.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => 'Gagal menghapus data: ' . $e->getMessage()
            ], 500);
        }
    }


    public function datatable(Request $request)
    {
        $role = $request->get('role');

        $query = User::with([
            'roles',
            'detailMahasiswa.prodi',
            'detailDosen'
        ])->whereHas('roles', function ($q) use ($role) {
            $q->whereIn('name', ['Mahasiswa', 'Dosen']);
            if ($role) {
                $q->where('name', $role);
            }
        });

        return datatables()->eloquent($query)
            ->addColumn('nama_lengkap', function ($user) use ($role) {
                $detail = $role === 'Dosen' ? $user->detailDosen : $user->detailMahasiswa;
                return trim(($detail->first_name ?? '') . ' ' . ($detail->last_name ?? ''));
            })
            ->addColumn('kolom4', function ($user) use ($role) {
                return $role === 'Dosen'
                    ? $user->detailDosen->jabatan ?? null
                    : $user->detailMahasiswa->tahun_masuk ?? null;
            })
            ->addColumn('kolom5', function ($user) use ($role) {
                $detail = $role === 'Dosen' ? $user->detailDosen : $user->detailMahasiswa;
                return $role === 'Dosen'
                    ? ($detail->jenis_kelamin ?? null)
                    : ($detail->kelas ?? null);
            })

            ->addColumn('kolom6', function ($user) use ($role) {
                if ($role === 'Dosen') {
                    return $user->detailDosen->bidang_keahlian ?? null;
                }

                return $user->detailMahasiswa->prodi->nama ?? null;
            })

            ->filterColumn('nama_lengkap', function ($query, $keyword) use ($role) {
                $query->where(function ($q) use ($keyword, $role) {
                    if ($role === 'Dosen') {
                        $q->whereHas('detailDosen', function ($q2) use ($keyword) {
                            $q2->whereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$keyword}%"]);
                        });
                    } else {
                        $q->whereHas('detailMahasiswa', function ($q2) use ($keyword) {
                            $q2->whereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$keyword}%"]);
                        });
                    }
                });
            })
            ->make(true);
    }

    public function select2Kelas(Request $request)
    {
        $query = $request->get('q');

        $kelass = Kelas::where('nama', 'like', "%{$query}%")
            ->where('nama', 'like', '%1%') // Hanya kelas yang mengandung angka 1
            ->get();

        $results = $kelass->map(function($kelas) {
            return [
                'id' => $kelas->id,
                'text' => $kelas->nama
            ];
        });

        return response()->json(['results' => $results]);
    }

    public function detailSelect2Kelas(Request $request)
    {
        $query = $request->get('q');
        $prodiId = $request->get('prodi_id');
        
        $prodi = Prodi::find($prodiId);
        if (!$prodi) {
            return response()->json(['results' => []]);
        }

        $awalan = collect(explode(' ', $prodi->nama))
            ->map(fn($word) => strtoupper(substr($word, 0, 1)))
            ->implode('');

        $kelas = Kelas::where('nama', 'like', "{$awalan}%")
            ->when($query, function ($q) use ($query) {
                $q->where('nama', 'like', "%{$query}%");
            })
            ->get();

        $results = $kelas->map(function ($item) {
            return [
                'id' => $item->id,
                'text' => $item->nama
            ];
        });

        return response()->json(['results' => $results]);
    }



    // public function getProdi(Request $request)
    // {
    //     $prodis = Prodi::select('id', 'nama')->orderBy('nama')->get();

    //     return response()->json([
    //         'status' => true,
    //         'data' => $prodis
    //     ]);
    // }

}
