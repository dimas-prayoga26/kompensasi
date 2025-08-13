<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BidangKeahlian;
use Illuminate\Support\Facades\DB;

class BidangKeahlianController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.metadata.bidang-keahlian.index');
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
            'nama_keahlian' => 'required|string',
        ]);

        try {
            DB::beginTransaction();

            BidangKeahlian::create([
                'nama_keahlian' => $request->nama_keahlian,
            ]);

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Keahlian berhasil ditambahkan.'
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => 'Gagal menyimpan keahlian: ' . $e->getMessage(),
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

            $bidangKeahlian = BidangKeahlian::findOrFail($id);

            DB::commit();

            return response()->json([
                'status' => true,
                'data' => $bidangKeahlian
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => 'Data bidang keahlian tidak ditemukan atau terjadi kesalahan.',
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
            'nama_keahlian' => 'required|string',
        ]);

        try {
            DB::beginTransaction();

            $bidangKeahlian = BidangKeahlian::findOrFail($id);
            $bidangKeahlian->update([
                'nama_keahlian' => $request->nama_keahlian
            ]);

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Keahlian berhasil diperbarui.',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Gagal memperbarui keahlian.',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function destroy(string $id)
    {
        try {
            DB::beginTransaction();

            $bidangKeahlian = BidangKeahlian::find($id);
            
            $bidangKeahlian->delete();

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Data keahlian berhasil dihapus.'
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
        $bidangKeahlian = BidangKeahlian::get();

        return datatables()->of($bidangKeahlian)->make(true);
    }
}
