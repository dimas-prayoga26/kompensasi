<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\Semester;
use App\Models\Kompensasi;
use Illuminate\Http\Request;
use App\Models\DetailMahasiswa;
use Illuminate\Support\Facades\DB;
use App\Models\KelasSemesterMahasiswa;

class SemesterController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.metadata.semester.index');
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
        $request->validate([
            'tahun_ajaran' => 'required|string',
            'semester' => 'required|in:Ganjil,Genap',
        ]);
    
        DB::beginTransaction();
    
        try {
            // Validasi: pastikan hanya ada satu semester aktif sebelumnya
            $semesterAktifCount = Semester::where('aktif', true)->count();
            if ($semesterAktifCount > 1) {
                return response()->json([
                    'status' => false,
                    'message' => 'Terdapat lebih dari satu semester aktif. Periksa data terlebih dahulu.',
                ], 422);
            }
    
            // Nonaktifkan semester aktif sebelumnya
            Semester::where('aktif', true)->update(['aktif' => false]);
    
            // Buat semester baru dan langsung aktif
            $semesterBaru = Semester::create([
                'tahun_ajaran' => $request->tahun_ajaran,
                'semester' => $request->semester,
                'no_semester' => Semester::max('no_semester') + 1,
                'aktif' => true,
            ]);
    
            $semesterIdBaru = $semesterBaru->id;
    
            // Ambil semua mahasiswa dari semester sebelumnya yang non-aktif (artinya semester sebelumnya)
            $mahasiswaList = KelasSemesterMahasiswa::whereHas('semester', function ($query) {
                $query->where('aktif', false);
            })->get();
    
            foreach ($mahasiswaList as $item) {
                $semesterLokalBaru = $item->semester_lokal + 1;
    
                $detail = DetailMahasiswa::where('user_id', $item->user_id)->first();
                $prodi = $detail?->prodi;
    
                if (!$prodi) continue;
    
                $maxSemester = $prodi->lama_studi ?? 8;
    
                // Jika sudah melebihi lama studi, nonaktifkan mahasiswa
                if ($semesterLokalBaru > $maxSemester) {
                    $item->update(['is_active' => false]);
                    continue;
                }
    
                // Nonaktifkan kompensasi aktif
                Kompensasi::where('user_id', $item->user_id)
                    ->where('is_active', true)
                    ->update(['is_active' => false]);
    
                // Proses pemindahan kelas jika semester baru adalah "Ganjil"
                $kelasLama = Kelas::find($item->kelas_id);
                $kelasBaruId = $item->kelas_id;
    
                if ($semesterBaru->semester === 'Ganjil' && $kelasLama && preg_match('/^(TI|RPL)(\d)([A-Z])$/', $kelasLama->nama, $matches)) {
                    $prefix = $matches[1]; // TI / RPL
                    $tingkat = (int)$matches[2] + 1; // naik tingkat
                    $huruf = $matches[3]; // A / B / dst
                    $kelasBaruNama = $prefix . $tingkat . $huruf;
    
                    $kelasBaru = Kelas::where('nama', $kelasBaruNama)->first();
                    if ($kelasBaru) {
                        $kelasBaruId = $kelasBaru->id;
    
                        // Update nama kelas di detail mahasiswa
                        DetailMahasiswa::where('user_id', $item->user_id)
                            ->update(['kelas' => $kelasBaru->nama]);
                    }
                }
    
                // Update data semester mahasiswa
                $item->update([
                    'semester_id' => $semesterIdBaru,
                    'semester_lokal' => $semesterLokalBaru,
                    'kelas_id' => $kelasBaruId,
                    'is_active' => true,
                ]);
            }
    
            DB::commit();
    
            return response()->json([
                'status' => true,
                'message' => 'Semester baru berhasil ditambahkan dan data mahasiswa diperbarui.',
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
    
            return response()->json([
                'status' => false,
                'message' => 'Gagal menambahkan semester baru.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
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
        //
    }

    public function datatable(Request $request)
    {
        $data = Semester::orderByDesc('no_semester')->get();

        return datatables()->of($data)->make(true);
    }


}
