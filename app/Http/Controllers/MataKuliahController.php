<?php

namespace App\Http\Controllers;

use App\Models\Prodi;
use App\Models\Semester;
use App\Models\Matakuliah;
use Illuminate\Http\Request;
use App\Models\MatakuliahSemester;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class MataKuliahController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.metadata.mata-kuliah.index');
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
            'kode' => 'required|string|unique:matakuliahs,kode',
            'nama' => 'required|string',
            'sks' => 'required|integer|min:1|max:6',
            'deskripsi' => 'nullable|string',
            'prodi_id' => 'required|exists:prodis,id',
            'semester' => 'required|integer|min:1|max:14', // semester lokal
        ]);

        try {
            DB::beginTransaction();

            $matakuliah = Matakuliah::create([
                'kode' => $request->kode,
                'nama' => $request->nama,
                'sks' => $request->sks,
                'deskripsi' => $request->deskripsi,
                'prodi_id' => $request->prodi_id,
            ]);

            // Ambil semester global yang aktif
            $semesterAktif = Semester::where('aktif', true)->first();

            if (!$semesterAktif) {
                throw new \Exception('Semester aktif tidak ditemukan.');
            }

            MatakuliahSemester::create([
                'matakuliah_id' => $matakuliah->id,
                'semester_id' => $semesterAktif->id,
                'semester_lokal' => $request->semester,
            ]);

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Matakuliah berhasil ditambahkan.',
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => 'Gagal menyimpan matakuliah: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            DB::beginTransaction();

            $matakuliah = MataKuliah::with([
                'prodi',
                'matakuliahSemesters' => function ($query) {
                    $query->select('id', 'matakuliah_id', 'semester_id', 'semester_lokal');
                }
            ])->findOrFail($id);

            DB::commit();

            return response()->json([
                'status' => true,
                'data' => $matakuliah
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => 'Data matakuliah tidak ditemukan atau terjadi kesalahan.',
                'error' => $th->getMessage()
            ], 500);
        }
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
        // dd($request->all());
        $request->validate([
            'kode' => 'required|string|max:20',
            'nama' => 'required|string|max:255',
            'sks' => 'required|integer|min:1|max:6',
            'deskripsi' => 'nullable|string',
            'prodi_id' => 'required|exists:prodis,id',
            'semester' => 'required|integer|min:1|max:14', // lokal
        ]);

        try {
            DB::beginTransaction();

            $mataKuliah = MataKuliah::findOrFail($id);

            // Update data utama
            $mataKuliah->update([
                'kode' => $request->kode,
                'nama' => $request->nama,
                'sks' => $request->sks,
                'deskripsi' => $request->deskripsi,
                'prodi_id' => $request->prodi_id,
            ]);

            // Ambil semester aktif
            $semesterAktif = Semester::where('aktif', true)->first();
            if (!$semesterAktif) {
                throw new \Exception('Semester aktif tidak ditemukan.');
            }

            // Hapus data semester lama
            $mataKuliah->matakuliahSemesters()->delete();

            // Simpan data semester baru
            $mataKuliah->matakuliahSemesters()->create([
                'semester_id' => $semesterAktif->id,
                'semester_lokal' => $request->semester,
            ]);

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Data mata kuliah berhasil diperbarui.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal update mata kuliah: ' . $e->getMessage());

            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan saat memperbarui data.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            DB::beginTransaction();

            $matakuliah = Matakuliah::find($id);

            if (!$matakuliah) {
                return response()->json([
                    'status' => false,
                    'message' => 'Data matakuliah tidak ditemukan.'
                ], 404);
            }

            $matakuliah->matakuliahSemesters()->delete();

            $matakuliah->delete();

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Data matakuliah berhasil dihapus.'
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function datatable(Request $request)
    {
        $data = Matakuliah::with('prodi')->get();

        return datatables()->of($data)->make(true);
    }

    public function getProdi(Request $request)
    {
        $prodis = Prodi::select('id', 'nama', 'lama_studi')->orderBy('nama')->get();

        return response()->json([
            'status' => true,
            'data' => $prodis
        ]);
    }

}
