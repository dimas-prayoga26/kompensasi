<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KelasController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.metadata.kelas.index');
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
            'nama' => 'required|string|max:50|unique:kelas,nama',
        ]);

        try {
            DB::beginTransaction();

            Kelas::create([
                'nama' => $request->nama,
            ]);

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Kelas berhasil ditambahkan.'
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => 'Gagal menyimpan kelas: ' . $e->getMessage(),
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

            $kelas = Kelas::findOrFail($id);

            DB::commit();

            return response()->json([
                'status' => true,
                'data' => $kelas
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
            'nama' => 'required|string|max:50|unique:kelas,nama,' . $id,
        ]);

        try {
            DB::beginTransaction();

            $kelas = Kelas::findOrFail($id);
            $kelas->update([
                'nama' => $request->nama
            ]);

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Kelas berhasil diperbarui.',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Gagal memperbarui kelas.',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function destroy(string $id)
    {
        try {
            DB::beginTransaction();

            $kelas = Kelas::find($id);
            
            $kelas->delete();

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Data kelas berhasil dihapus.'
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function datatable(Request $request)
    {
        $data = Kelas::get();

        return datatables()->of($data)->make(true);
    }
}
