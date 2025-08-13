<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Kelas;
use App\Models\Prodi;
use App\Models\Semester;
use App\Models\DetailDosen;
use App\Imports\UsersImport;
use Illuminate\Http\Request;
use App\Models\BidangKeahlian;
use App\Models\DetailMahasiswa;
use App\Models\JabatanFungsional;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\KelasSemesterMahasiswa;

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

            $user = User::with([
                'roles', 
                'detailMahasiswa.prodi', 
                'detailDosen.jabatanFungsional',
                'detailDosen.bidangKeahlian', 
                'kelasSemesterMahasiswas'
            ])->find($id);

            if (!$user) {
                DB::rollBack();
                return response()->json([
                    'status' => false,
                    'message' => 'User tidak ditemukan.'
                ], 404);
            }

            $jabatanFungsionalList = JabatanFungsional::all();
            $bidangKeahlianList = BidangKeahlian::all();

            DB::commit();

            return response()->json([
                'status' => true,
                'data' => $user,
                'jabatanFungsionalList' => $jabatanFungsionalList,
                'bidangKeahlianList' => $bidangKeahlianList
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

            // Jika role user adalah Mahasiswa
            if ($user->hasRole('Mahasiswa')) {
                $request->validate([
                    'first_name' => 'required|string|max:255',
                    'last_name' => 'nullable|string|max:255',
                    'kelas_id' => 'required|exists:kelas,id',
                    'semester_lokal' => 'required|integer|min:1',
                    'is_active' => 'required|boolean',
                ]);

                $detail = $user->detailMahasiswa;
                if (!$detail) {
                    throw new \Exception("Detail Mahasiswa tidak ditemukan.");
                }

                $kelas = Kelas::findOrFail($request->input('kelas_id'));
                $detail->update([
                    'first_name' => $request->input('first_name'),
                    'last_name' => $request->input('last_name'),
                    'kelas' => $kelas->nama,
                ]);

                KelasSemesterMahasiswa::updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'kelas_id' => $request->input('kelas_id'),
                        'semester_lokal' => $request->input('semester_lokal'),
                        'is_active' => $request->boolean('is_active'),
                    ]
                );

            // Jika role user adalah Dosen
            } elseif ($user->hasRole('Dosen')) {
                $request->validate([
                    'nip' => 'required|string|max:20', // Validasi untuk NIP
                    'first_name' => 'required|string|max:255',
                    'last_name' => 'nullable|string|max:255',
                    'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
                    'jabatan_fungsional_id' => 'nullable|exists:jabatan_fungsionals,id', // Validasi ID jabatan fungsional
                    'bidang_keahlian_id' => 'nullable|exists:bidang_keahlians,id', // Validasi ID bidang keahlian
                ]);

                $detail = $user->detailDosen;
                if (!$detail) {
                    throw new \Exception("Detail Dosen tidak ditemukan.");
                }

                // Update NIP untuk Dosen
                $user->update([
                    'nip' => $request->input('nip'), // Update NIP
                ]);

                // Update detail dosen
                $detail->update([
                    'first_name' => $request->input('first_name'),
                    'last_name' => $request->input('last_name'),
                    'jenis_kelamin' => $request->input('jenis_kelamin'),
                    'jabatan_fungsional_id' => $request->input('jabatan_fungsional_id'), // Update dengan ID jabatan fungsional
                    'bidang_keahlian_id' => $request->input('bidang_keahlian_id'), // Update dengan ID bidang keahlian
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
            'detailDosen.bidangKeahlian',        // relasi ke tabel bidang_keahlian
            'detailDosen.jabatanFungsional',    // relasi ke tabel jabatan_fungsional
            'kelasSemesterMahasiswas'
        ])->whereHas('roles', function ($q) use ($role) {
            $q->whereIn('name', ['Mahasiswa', 'Dosen']);
            if ($role) {
                $q->where('name', $role);
            }
        });

        return datatables()->eloquent($query)
            ->addColumn('nama_lengkap', function ($user) {
                $detailMhs = $user->detailMahasiswa;
                $detailDsn = $user->detailDosen;

                if ($detailMhs) {
                    return trim(($detailMhs->first_name ?? '') . ' ' . ($detailMhs->last_name ?? ''));
                } elseif ($detailDsn) {
                    return trim(($detailDsn->first_name ?? '') . ' ' . ($detailDsn->last_name ?? ''));
                }
                return '';
            })
            ->addColumn('kolom4', function ($user) {
                if ($user->detailDosen) {
                    // Ambil nama jabatan fungsional
                    return $user->detailDosen->jabatanFungsional->nama_jabatan ?? '<span class="badge bg-danger">Data belum lengkap</span>';
                }

                if ($user->detailMahasiswa && $user->kelasSemesterMahasiswas) {
                    $tahunMasuk = $user->detailMahasiswa->tahun_masuk;
                    $statusAktif = $user->kelasSemesterMahasiswas->is_active == 1 ? 'Aktif' : 'Lulus';
                    return "{$tahunMasuk} ({$statusAktif})";
                }

                return '<span class="badge bg-danger">Data belum lengkap</span>';
            })
            ->addColumn('kolom5', function ($user) {
                if ($user->detailDosen) {
                    // Ambil nama bidang keahlian
                    return $user->detailDosen->bidangKeahlian->nama_keahlian ?? '<span class="badge bg-danger">Data belum lengkap</span>';
                }
                return $user->detailMahasiswa->prodi->nama ?? '<span class="badge bg-danger">Data belum lengkap</span>';
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

    public function import(Request $request)
    {
        $request->validate([
            'file_excel' => 'required|mimes:xlsx,xls'
        ]);

        Excel::import(new UsersImport, $request->file('file_excel'));

        return response()->json(['message' => 'Import berhasil!']);
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
