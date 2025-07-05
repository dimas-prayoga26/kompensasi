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
    
    public function index()
    {
        $dosens = User::role('Dosen')
            ->with('detailDosen:id,user_id,first_name,last_name')
            ->get();

        return view('admin.tugas_kompen.index', compact('dosens'));
    }

    
    public function create()
    {

    }

    
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_dosen' => 'required|exists:users,id',
            'jumlah_mahasiswa' => 'required|integer|min:1',
            'deskripsi_kompensasi' => 'required|string|max:255',
            'file_image' => 'required|mimes:jpeg,png,jpg,webp,xlsx,xls,pdf,doc,docx|max:2048',
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

            $file = $request->file('file_image');
            $fileExtension = $file->getClientOriginalExtension();


            if (in_array($fileExtension, ['jpeg', 'png', 'jpg', 'webp'])) {
                $folder = 'image_kompensasi';
            } elseif (in_array($fileExtension, ['pdf'])) {
                $folder = 'pdf_dokumen_kompensasi';
            } elseif (in_array($fileExtension, ['doc', 'docx'])) {
                $folder = 'word_dokumen_kompensasi';
            } elseif (in_array($fileExtension, ['xls', 'xlsx'])) {
                $folder = 'excel_dokumen_kompensasi';
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Format file tidak diizinkan'
                ], 422);
            }

            $filename = $file->hashName();
            $filePath = $file->storeAs($folder, $filename, 'public');


            TugasKompensasi::create([
                'dosen_id' => $request->id_dosen,
                'jumlah_mahasiswa' => $request->jumlah_mahasiswa,
                'deskripsi_kompensasi' => $request->deskripsi_kompensasi,
                'file_path' => $filePath,
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


    
    public function edit(string $id)
    {

    }

    
    public function update(Request $request, string $id)
    {
        DB::beginTransaction();

        try {
            $kompensasi = TugasKompensasi::findOrFail($id);

            $validated = $request->validate([
                'id_dosen' => 'required|exists:users,id',
                'jumlah_mahasiswa' => 'required|integer|min:1',
                'deskripsi_kompensasi' => 'required|string',
                'file_image' => 'nullable|mimes:jpeg,png,jpg,webp,pdf,doc,docx,xlsx,xls|max:2048'
            ]);

            // Menyimpan path file lama jika ada
            $oldFilePath = $kompensasi->file_path;

            // Cek apakah ada file baru yang diunggah
            if ($request->hasFile('file_image')) {
                // Jika file lama ada, hapus file lama
                if ($oldFilePath && Storage::disk('public')->exists($oldFilePath)) {
                    Storage::disk('public')->delete($oldFilePath);
                }

                // Mendapatkan ekstensi file yang diunggah
                $file = $request->file('file_image');
                $fileExtension = $file->getClientOriginalExtension();
                $folder = '';

                // Tentukan folder penyimpanan berdasarkan ekstensi file
                if (in_array($fileExtension, ['jpeg', 'png', 'jpg', 'webp'])) {
                    $folder = 'image_kompensasi';
                } elseif (in_array($fileExtension, ['pdf'])) {
                    $folder = 'pdf_dokumen_kompensasi';
                } elseif (in_array($fileExtension, ['doc', 'docx'])) {
                    $folder = 'word_dokumen_kompensasi';
                } elseif (in_array($fileExtension, ['xls', 'xlsx'])) {
                    $folder = 'excel_dokumen_kompensasi';
                }

                // Menyimpan file baru dengan nama yang dihash
                $path = $file->store($folder, 'public');
                $kompensasi->file_path = $path;
            }

            // Perbarui data kompensasi
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



    
    public function destroy(string $id)
    {
        DB::beginTransaction();

        try {

            $kompensasi = TugasKompensasi::findOrFail($id);
            if ($kompensasi->file_path) {
                $fullPath = public_path('storage/' . $kompensasi->file_path);
                if (file_exists($fullPath)) {
                    $fileExtension = pathinfo($fullPath, PATHINFO_EXTENSION);

                    if (in_array($fileExtension, ['jpeg', 'png', 'jpg', 'webp'])) {
                        unlink($fullPath);
                    } elseif (in_array($fileExtension, ['pdf'])) {
                        unlink($fullPath);
                    } elseif (in_array($fileExtension, ['doc', 'docx'])) {
                        unlink($fullPath);
                    } elseif (in_array($fileExtension, ['xls', 'xlsx'])) {
                        unlink($fullPath);
                    }
                }
            }

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
