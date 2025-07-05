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
            // Ambil file
            $file = $request->file('file_image');
            $fileExtension = $file->getClientOriginalExtension();

            // Ambil nama dosen dari 'dosen_id'
            $dosen = User::find($request->id_dosen)->detailDosen;

            if (!$dosen) {
                return response()->json([
                    'status' => false,
                    'message' => 'Dosen tidak ditemukan.'
                ], 404);
            }

            // Format nama dosen menjadi 'first_name_last_name'
            $dosenName = $dosen->first_name . '_' . $dosen->last_name;

            // Tentukan folder penyimpanan berdasarkan ekstensi file
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

            // Dapatkan nama file asli dan format nama baru dengan format '(NAMAFILE)_(NAMADOSEN)'
            $originalFileName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $newFileName = $originalFileName . '_' . $dosenName . '.' . $fileExtension;

            // Simpan file dengan nama baru
            $filePath = $file->storeAs($folder, $newFileName, 'public');

            // Simpan data tugas kompensasi
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

            $oldFilePath = $kompensasi->file_path;

            if ($request->hasFile('file_image')) {
                if ($oldFilePath && Storage::disk('public')->exists($oldFilePath)) {
                    Storage::disk('public')->delete($oldFilePath);
                }

                $file = $request->file('file_image');
                $fileExtension = $file->getClientOriginalExtension();

                $dosen = User::find($request->id_dosen)->detailDosen;

                if (!$dosen) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Dosen tidak ditemukan.'
                    ], 404);
                }

                $dosenName = $dosen->first_name . '_' . $dosen->last_name;

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

                $originalFileName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $newFileName = $originalFileName . '_' . $dosenName . '.' . $fileExtension;

                $filePath = $file->storeAs($folder, $newFileName, 'public');
                $kompensasi->file_path = $filePath;
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
        $userId = auth()->user()->id;

        $data = TugasKompensasi::with('dosen.detailDosen')
            ->where('dosen_id', $userId)
            ->get();

        return DataTables::of($data)
            ->addColumn('nama_dosen', function ($row) {
                $detail = $row->dosen->detailDosen ?? null;
                return $detail ? $detail->first_name . ' ' . $detail->last_name : '-';
            })
            ->make(true);
    }



}
