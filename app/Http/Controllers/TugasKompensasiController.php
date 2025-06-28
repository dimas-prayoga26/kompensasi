<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\TugasKompensasi;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use App\Models\MahasiswaKompensasi;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class TugasKompensasiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $dosens = User::role('Dosen')
            ->with('detailDosen:id,user_id,first_name,last_name')
            ->get();

        return view('admin.tugas_kompen.index', compact('dosens'));
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
        $validator = Validator::make($request->all(), [
            'id_dosen' => 'required|exists:users,id',
            'jumlah_mahasiswa' => 'required|integer|min:1',
            'deskripsi_kompensasi' => 'required|string|max:255',
            'file_image' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            $filename = $request->file('file_image')->hashName(); // hash nama unik
            $imagePath = $request->file('file_image')->storeAs('image_kompensasi', $filename, 'public');

            TugasKompensasi::create([
                'dosen_id' => $request->id_dosen,
                'jumlah_mahasiswa' => $request->jumlah_mahasiswa,
                'deskripsi_kompensasi' => $request->deskripsi_kompensasi,
                'file_path' => $imagePath,
            ]);

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Tugas kompensasi berhasil disimpan.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan saat menyimpan data.',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $data = TugasKompensasi::findOrFail($id);

            return response()->json([
                'status' => true,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Data tidak ditemukan.'
            ], 404);
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
        DB::beginTransaction();

        try {
            $kompensasi = TugasKompensasi::findOrFail($id);

            $validated = $request->validate([
                'id_dosen' => 'required|exists:users,id',
                'jumlah_mahasiswa' => 'required|integer|min:1',
                'deskripsi_kompensasi' => 'required|string',
                'file_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
            ]);

            $oldFilePath = $kompensasi->file_path;

            if ($request->hasFile('file_image')) {
                if ($oldFilePath && Storage::disk('public')->exists($oldFilePath)) {
                    Storage::disk('public')->delete($oldFilePath);
                }

                $path = $request->file('file_image')->store('image_kompensasi', 'public');
                $kompensasi->file_path = $path;
            }

            $kompensasi->dosen_id = $validated['id_dosen'];
            $kompensasi->jumlah_mahasiswa = $validated['jumlah_mahasiswa'];
            $kompensasi->deskripsi_kompensasi = $validated['deskripsi_kompensasi'];
            $kompensasi->save();

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Data berhasil diperbarui.',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Gagal memperbarui data: ' . $e->getMessage(),
            ], 500);
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        DB::beginTransaction();

        try {
            // Ambil data berdasarkan ID
            $kompensasi = TugasKompensasi::findOrFail($id);

            // Hapus file gambar jika ada (pakai unlink)
            if ($kompensasi->file_path) {
                $fullPath = public_path('storage/' . $kompensasi->file_path);
                if (file_exists($fullPath)) {
                    unlink($fullPath);
                }
            }

            // Hapus data dari database
            $kompensasi->delete();

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Data berhasil dihapus.'
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
        $data = TugasKompensasi::with('dosen.detailDosen')->get();

        return DataTables::of($data)
            ->addColumn('nama_dosen', function ($row) {
                $detail = $row->dosen->detailDosen ?? null;
                return $detail ? $detail->first_name . ' ' . $detail->last_name : '-';
            })
            ->make(true);
    }

    public function detail($id)
    {
        $data = MahasiswaKompensasi::where('penawaran_kompensasi_id', $id)
            ->with(['mahasiswa.detailMahasiswa'])
            ->get()
            ->map(function ($item) {
                $detail = $item->mahasiswa->detailMahasiswa;

                return [
                    'id' => $item->id,
                    'nim' => $item->mahasiswa->nim,
                    'nama_mahasiswa' => $detail ? "{$detail->first_name} {$detail->last_name}" : '-',
                    'kelas' => $detail->kelas ?? '-',
                ];
            });

        return DataTables::of($data)->make(true);
    }

    public function hapusMahasiswa($id)
    {
        try {
            $mahasiswa = MahasiswaKompensasi::findOrFail($id);
            $mahasiswa->delete();

            return response()->json([
                'status' => true,
                'message' => 'Mahasiswa berhasil dihapus dari kompensasi.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Gagal menghapus mahasiswa: ' . $e->getMessage()
            ], 500);
        }
    }

    public function storeMahasiswaKompensasi(Request $request)
    {
        $request->validate([
            'kompensasi_id' => 'required|exists:penawaran_kompensasis,id'
        ]);

        try {
            $userId = Auth::id();

            $exists = MahasiswaKompensasi::where('penawaran_kompensasi_id', $request->kompensasi_id)
                        ->where('user_id', $userId)
                        ->exists();

            if ($exists) {
                return response()->json([
                    'status' => false,
                    'message' => 'Anda sudah mendaftar dalam kompensasi ini.'
                ], 400);
            }

            MahasiswaKompensasi::create([
                'penawaran_kompensasi_id' => $request->kompensasi_id,
                'user_id' => $userId
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Berhasil mendaftar ke kompensasi.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Gagal menyimpan data: ' . $e->getMessage()
            ], 500);
        }
    }


}
