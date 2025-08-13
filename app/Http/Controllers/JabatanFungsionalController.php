<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\JabatanFungsional;
use Illuminate\Support\Facades\DB;

class JabatanFungsionalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.metadata.jabatan-fungsional.index');
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
            'nama_jabatan' => 'required|string',
        ]);

        try {
            DB::beginTransaction();

            JabatanFungsional::create([
                'nama_jabatan' => $request->nama_jabatan,
            ]);

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Jabatan berhasil ditambahkan.'
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => 'Gagal menyimpan jabatan: ' . $e->getMessage(),
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

            $jabatan = JabatanFungsional::findOrFail($id);

            DB::commit();

            return response()->json([
                'status' => true,
                'data' => $jabatan
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => 'Data jabatan tidak ditemukan atau terjadi kesalahan.',
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
            'nama_jabatan' => 'required|string',
        ]);

        try {
            DB::beginTransaction();

            $jabatanFungsional = JabatanFungsional::findOrFail($id);
            $jabatanFungsional->update([
                'nama_jabatan' => $request->nama_jabatan
            ]);

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Jabatan berhasil diperbarui.',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Gagal memperbarui jabatan.',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function destroy(string $id)
    {
        try {
            DB::beginTransaction();

            $jabatanFungsional = JabatanFungsional::find($id);
            
            $jabatanFungsional->delete();

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Data jabatan berhasil dihapus.'
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
        $data = JabatanFungsional::get();

        return datatables()->of($data)->make(true);
    }
}
