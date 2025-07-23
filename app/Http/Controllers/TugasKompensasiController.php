<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Kompensasi;
use Illuminate\Http\Request;
use App\Models\TugasKompensasi;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use App\Models\FileBuktiKompensasi;
use App\Models\MahasiswaKompensasi;
use Illuminate\Support\Facades\Auth;
use App\Models\PenawaranKompensasiUser;
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
            'jumlah_menit_kompensasi' => 'required|integer|min:1',
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
            } elseif ($fileExtension === 'pdf') {
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

            TugasKompensasi::create([
                'dosen_id' => $request->id_dosen,
                'jumlah_mahasiswa' => $request->jumlah_mahasiswa,
                'jumlah_menit_kompensasi' => $request->jumlah_menit_kompensasi,
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
                'jumlah_menit_kompensasi' => 'required|integer|min:1', // ✅ Tambahan validasi
                'deskripsi_kompensasi' => 'required|string',
                'file_image' => 'nullable|mimes:jpeg,png,jpg,webp,pdf,doc,docx,xlsx,xls|max:2048',
            ]);

            $oldFilePath = $kompensasi->file_path;

            // Cek jika ada file baru
            if ($request->hasFile('file_image')) {
                if ($oldFilePath && Storage::disk('public')->exists($oldFilePath)) {
                    Storage::disk('public')->delete($oldFilePath);
                }

                $file = $request->file('file_image');
                $fileExtension = strtolower($file->getClientOriginalExtension());

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
                } elseif ($fileExtension === 'pdf') {
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

            // Simpan data ke DB
            $kompensasi->dosen_id = $validated['id_dosen'];
            $kompensasi->jumlah_mahasiswa = $validated['jumlah_mahasiswa'];
            $kompensasi->jumlah_menit_kompensasi = $validated['jumlah_menit_kompensasi']; // ✅ Tambahan simpan
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
        $role = auth()->user()->getRoleNames()->first();

        if ($role === 'superAdmin' || $role === 'Mahasiswa') {
            $data = TugasKompensasi::with([
                'dosen.detailDosen',
                'penawaranUsers.user'
            ])->get();
        } else {
            $data = TugasKompensasi::with([
                'dosen.detailDosen',
                'penawaranUsers.user'
            ])->where('dosen_id', $userId)->get();
        }

        return DataTables::of($data)
            ->addColumn('nama_dosen', function ($row) {
                $detail = $row->dosen->detailDosen ?? null;
                return $detail ? $detail->first_name . ' ' . $detail->last_name : '-';
            })
            ->make(true);
    }

    public function detail($id)
    {
        $data = PenawaranKompensasiUser::where('penawaran_kompensasi_id', $id)
            ->with(['user.detailMahasiswa'])
            ->get()
            ->map(function ($item) {
                $detail = $item->user->detailMahasiswa;

                return [
                    'id' => $item->id,
                    'nim' => $item->user->nim,
                    'nama_mahasiswa' => $detail ? "{$detail->first_name} {$detail->last_name}" : '-',
                    'kelas' => $detail->kelas ?? '-',
                ];
            });

        return DataTables::of($data)->make(true);
    }

    public function hapusMahasiswa($id)
    {
        try {
            $mahasiswa = PenawaranKompensasiUser::findOrFail($id);
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

            $exists = PenawaranKompensasiUser::where('penawaran_kompensasi_id', $request->kompensasi_id)
                        ->where('user_id', $userId)
                        ->exists();

            if ($exists) {
                return response()->json([
                    'status' => false,
                    'message' => 'Anda sudah mendaftar dalam kompensasi ini.'
                ], 400);
            }

            $penawaran = TugasKompensasi::findOrFail($request->kompensasi_id);

            $totalTerdaftar = PenawaranKompensasiUser::where('penawaran_kompensasi_id', $request->kompensasi_id)->count();

            if ($totalTerdaftar >= $penawaran->jumlah_mahasiswa) {
                return response()->json([
                    'status' => false,
                    'message' => 'Kuota mahasiswa untuk kompensasi ini sudah penuh.'
                ], 400);
            }

            PenawaranKompensasiUser::create([
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


    public function uploadBukti(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:penawaran_kompensasi_users,id', // ✅ BENAR
            'file_bukti' => 'required|file|max:5120|mimes:jpg,jpeg,png,pdf,doc,docx,xls,xlsx',
            'keterangan' => 'nullable|string|max:1000',
        ]);


        try {
            DB::beginTransaction();

            $file = $request->file('file_bukti');
            $extension = strtolower($file->getClientOriginalExtension());
            $folder = 'bukti_penawaran_kompensasi_' . $extension;
            $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $newFileName = $originalName . '_' . time() . '.' . $extension;
            $filePath = $file->storeAs($folder, $newFileName, 'public');

            $penawaranUser = PenawaranKompensasiUser::where('id', $request->id)->first();

            // dd($penawaranUser);

            if (!$penawaranUser) {
                throw new \Exception("User tidak ditemukan untuk penawaran kompensasi ini.");
            }

            $penawaranUser->update([
                'file_path' => $filePath,
                'keterangan' => $request->keterangan,
            ]);

            $userId = $penawaranUser->user_id;
            $penawaran = TugasKompensasi::findOrFail($penawaranUser->penawaran_kompensasi_id);
            $jumlahMenit = $penawaran->jumlah_menit_kompensasi;
            $sisaMenit = $jumlahMenit;

            $kompensasiList = Kompensasi::where('user_id', $userId)
                ->where('menit_kompensasi', '>', 0)
                ->orderBy('id')
                ->get();

            foreach ($kompensasiList as $kompen) {
                if ($sisaMenit <= 0) break;

                $kurangi = min($kompen->menit_kompensasi, $sisaMenit);
                $kompen->menit_kompensasi -= $kurangi;
                $kompen->save();

                $sisaMenit -= $kurangi;
            }

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Bukti berhasil diunggah dan menit kompensasi diperbarui.'
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }


    public function downloadBukti($id)
    {
        $penawaranUser = PenawaranKompensasiUser::where('penawaran_kompensasi_id', $id)
                        ->where('user_id', auth()->id())
                        ->first();

        if (!$penawaranUser || !$penawaranUser->file_path || !Storage::disk('public')->exists($penawaranUser->file_path)) {
            return response()->json([
                'status' => false,
                'message' => 'File bukti tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'status' => true,
            'file_url' => asset('storage/' . $penawaranUser->file_path),
        ]);
    }

    // CodeIgniter
    public function getUploadData($id)
    {
        $data = PenawaranKompensasiUser::find($id);
        // dd($data);

        if ($data && $data->file_path) {
            $relativePath = 'storage/' . $data->file_path;

            if (file_exists(public_path($relativePath))) {
                return response()->json([
                    'success' => true,
                    'data' => [
                        'file_url' => asset($relativePath),
                        'keterangan' => $data->keterangan ?? ''
                    ]
                ]);
            }
        }

        return response()->json([
            'status' => false,
            'message' => 'Data atau file tidak ditemukan'
        ], 404);
    }


}
