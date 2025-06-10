<?php

namespace App\Http\Controllers;

use App\Models\Prodi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProdiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.metadata.prodi.index');
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
            'kode_prodi' => 'required|string|max:10|unique:prodis,kode_prodi',
            'nama'       => 'required|string|max:255',
            'lama_studi' => 'required|integer|min:1|max:12',
        ]);

        DB::beginTransaction();

        try {
            Prodi::create([
                'kode_prodi' => $request->kode_prodi,
                'nama'       => $request->nama,
                'lama_studi' => $request->lama_studi,
            ]);

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Data prodi berhasil ditambahkan.'
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan saat menyimpan data prodi.',
                'error' => $e->getMessage(),
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

            $prodi = Prodi::findOrFail($id);

            DB::commit();

            return response()->json([
                'status' => true,
                'data' => $prodi
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
        $request->validate([
            'kode_prodi' => 'required|string|max:10|unique:prodis',
            'nama'       => 'required|string|max:255',
            'lama_studi' => 'required|integer|min:1|max:12',
        ]);

        DB::beginTransaction();

        try {
            $prodi = Prodi::findOrFail($id);

            $prodi->update([
                'kode_prodi' => $request->kode_prodi,
                'nama'       => $request->nama,
                'lama_studi' => $request->lama_studi,
            ]);

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Data prodi berhasil diperbarui.',
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan saat memperbarui data prodi.',
                'error' => $e->getMessage(),
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

            $prodi = Prodi::findOrFail($id);

            $prodi->delete();

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Data prodi berhasil dihapus.'
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => 'Gagal menghapus data prodi.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function datatable(Request $request)
    {
        $data = Prodi::get();

        return datatables()->of($data)->make(true);
    }
}
