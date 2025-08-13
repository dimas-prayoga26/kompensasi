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

        $tugasKompensasi = TugasKompensasi::first(); // Mengambil entri pertama

        // dd($tugasKompensasi);

        return view('admin.tugas_kompen.index', compact('tugasKompensasi', 'dosens'));
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
        $rows = PenawaranKompensasiUser::where('penawaran_kompensasi_id', $id)
            ->with(['user.detailMahasiswa'])
            ->get()
            ->map(function ($item) {
                $detail = $item->user->detailMahasiswa;

                return [
                    'id'    => $item->id,
                    'nim'   => $item->user->nim,
                    'nama_mahasiswa' => $detail ? "{$detail->first_name} {$detail->last_name}" : '-',
                    'kelas' => $detail->kelas ?? '-',

                    'status' => $item->status ?? 'pending',

                    'bukti_konfirmasi_url' => $item->file_path
                        ? Storage::disk('public')->url($item->file_path)
                        : null,
                ];
            });

        return DataTables::of($rows)->make(true);
    }

    public function rejectMahasiswa($id)
    {
        try {
            DB::beginTransaction();

            $mahasiswa = PenawaranKompensasiUser::findOrFail($id);

            if ($mahasiswa->status === 'reject') {
                return response()->json([
                    'status' => false,
                    'message' => 'Mahasiswa sudah berstatus reject.'
                ], 400);
            }

            $mahasiswa->status = 'reject';
            $mahasiswa->save();

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Status mahasiswa berhasil diubah menjadi reject.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Gagal mengubah status mahasiswa: ' . $e->getMessage()
            ], 500);
        }
    }

    public function acceptMahasiswa($id)
    {
        try {
            DB::beginTransaction();

            $penawaranUser = PenawaranKompensasiUser::lockForUpdate()->findOrFail($id);

            $penawaran = TugasKompensasi::lockForUpdate()->findOrFail($penawaranUser->penawaran_kompensasi_id);

            $penawaran->decrement('jumlah_mahasiswa', 1);
            $penawaranUser->status = 'accept';
            $penawaranUser->save();

            DB::commit();
            return response()->json(['status' => true, 'message' => 'Mahasiswa berhasil diterima.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => false, 'message' => 'Gagal memproses: ' . $e->getMessage()], 500);
        }
    }


    public function storeMahasiswaKompensasi(Request $request)
    {
        $request->validate([
            'kompensasi_id' => 'required|exists:penawaran_kompensasis,id',
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

            DB::beginTransaction();

            PenawaranKompensasiUser::create([
                'penawaran_kompensasi_id' => $request->kompensasi_id,
                'user_id' => $userId,
                // 'status' => 'pending' // kalau perlu default status
            ]);

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Berhasil mendaftar ke kompensasi.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Gagal menyimpan data: ' . $e->getMessage()
            ], 500);
        }
    }



    public function uploadBukti(Request $request)
    {
        
        $request->validate([
            'id' => 'required|exists:penawaran_kompensasi_users,id',
            'file_bukti' => 'required|file|max:5120|mimes:jpg,jpeg,png,pdf,doc,docx,xls,xlsx',
            'keterangan' => 'nullable|string|max:1000',
        ]);

        try {
            DB::beginTransaction();

            $target      = $request->input('target');
            $user        = Auth::user();

            $file        = $request->file('file_bukti');
            $ext         = strtolower($file->getClientOriginalExtension());
            $folder      = 'bukti_penawaran_kompensasi_' . $ext;
            $original    = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $newFileName = $original . '_' . time() . '.' . $ext;
            $storedPath  = $file->storeAs($folder, $newFileName, 'public');

            if ($target === 'buktiKompensasi') {

                $penawaranUser = PenawaranKompensasiUser::lockForUpdate()->findOrFail($request->id);

                $user = Auth::user();
                $update = [
                    'file_path'  => $storedPath,
                    'keterangan' => $request->keterangan,
                ];

                if ($user->hasAnyRole(['superAdmin', 'Dosen'])) {
                    $update['file_status'] = 'edited';
                } elseif ($user->hasRole('Mahasiswa')) {
                    if (is_null($penawaranUser->file_path)) {
                        $update['file_status'] = 'created';
                    }
                }

                if (!empty($penawaranUser->file_path) && Storage::disk('public')->exists($penawaranUser->file_path)) {
                    Storage::disk('public')->delete($penawaranUser->file_path);
                }

                $penawaranUser->update($update);

            }   elseif ($target === 'buktiPengerjaanKompen') {

                $penawaranUser = PenawaranKompensasiUser::findOrFail($request->id);

                $penawaranKompenId = $penawaranUser->penawaran_kompensasi_id;

                $history = FileBuktiKompensasi::create([
                    'penawaran_kompensasi_id' => $penawaranKompenId,
                    'user_id'      => $user?->id,
                    'file_path'    => $storedPath,
                    'keterangan'   => $request->input('keterangan'),
                ]);
            }


            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Bukti berhasil diunggah.'
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }


    public function download($id)
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
    public function downloadBukti($id)
    {
        try {
            return DB::transaction(function () use ($id) {

                $data = PenawaranKompensasiUser::find($id);

                if (!$data) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Data tidak ditemukan',
                    ], 404);
                }

                $payload = [
                    'keterangan'  => $data->keterangan ?? '',
                    'file_status' => $data->file_status ?? null,
                ];

                if ($data->file_path && Storage::disk('public')->exists($data->file_path)) {
                    $payload['file_url']  = Storage::url($data->file_path);
                    $payload['filename']  = basename($data->file_path);
                    $payload['extension'] = strtolower(pathinfo($data->file_path, PATHINFO_EXTENSION));

                    // dd($payload);
                    return response()->json([
                        'status' => true,
                        'data'    => $payload,
                    ], 200);
                }


                return response()->json([
                    'status' => false,
                    'data'    => $payload,
                    'message' => 'File belum diunggah',
                ], 404);
            });
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function getFiles($penawaranId)
    {
        try {
            $penawaranUser = PenawaranKompensasiUser::where('id', $penawaranId)->first();

            if (!$penawaranUser) {
                return response()->json([
                    'success' => false,
                    'message' => 'Penawaran Kompensasi tidak ditemukan.',
                ], 404);
            }
            
            $penawaranKompenId = $penawaranUser->penawaran_kompensasi_id;

            $files = FileBuktiKompensasi::where('penawaran_kompensasi_id', $penawaranKompenId)
                ->get();

            if ($files->isEmpty()) {
                return response()->json([
                    'success' => true,
                    'data'    => [],
                    'message' => 'Tidak ada file bukti yang diunggah.',
                ]);
            }

            $filesData = $files->map(function ($file) {
                $user = $file->user;

                $firstName = '';
                $lastName = '';

                if ($user->detailMahasiswa) {
                    $firstName = $user->detailMahasiswa->first_name;
                    $lastName  = $user->detailMahasiswa->last_name;
                } elseif ($user->detailDosen) {
                    $firstName = $user->detailDosen->first_name;
                    $lastName  = $user->detailDosen->last_name;
                }

                $nama = trim($firstName . ' ' . $lastName) ?: 'Tanpa Nama';

                return [
                    'id'              => $file->id,
                    'first_name'      => $firstName,
                    'last_name'       => $lastName,
                    'file_url'        => $file->file_path ? Storage::url($file->file_path) : null,
                    'extension'       => pathinfo($file->file_path, PATHINFO_EXTENSION),
                    'keterangan'      => $file->keterangan ?? '',
                    'created_at'      => $file->created_at->toDateTimeString(),
                    'nama'            => $nama,
                ];
            });

            return response()->json([
                'success' => true,
                'data'    => $filesData,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data bukti: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function konfirmasiKompensasi(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:penawaran_kompensasis,id', // id = ID penawaran/tugas kompensasi
        ]);

        DB::beginTransaction();

        try {
            $penawaran = TugasKompensasi::lockForUpdate()->findOrFail($request->id);

            if ($penawaran->status === 'closed') {
                DB::rollBack();
                return response()->json([
                    'status' => false,
                    'message' => 'Tugas kompensasi sudah berstatus closed.',
                ], 422);
            }
            $penawaranUser = PenawaranKompensasiUser::where('penawaran_kompensasi_id', $penawaran->id)->first();

            if (!$penawaranUser) {
                DB::rollBack();
                return response()->json([
                    'status' => false,
                    'message' => 'Peserta untuk penawaran kompensasi ini tidak ditemukan.',
                ], 404);
            }

            $userId     = $penawaranUser->user_id;
            $sisaMenit  = (int) ($penawaran->jumlah_menit_kompensasi ?? 0);

            if ($sisaMenit > 0) {
                $kompensasiList = Kompensasi::where('user_id', $userId)
                    ->where('menit_kompensasi', '>', 0)
                    ->orderBy('id')
                    ->lockForUpdate()
                    ->get();

                foreach ($kompensasiList as $kompen) {
                    if ($sisaMenit <= 0) break;

                    $kurangi = min($kompen->menit_kompensasi, $sisaMenit);
                    $kompen->menit_kompensasi -= $kurangi;
                    $kompen->save();

                    $sisaMenit -= $kurangi;
                }
            }

            $penawaran->status     = 'closed';
            $penawaran->save();

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Tugas kompensasi diselesaikan dan status diubah menjadi closed.'
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }


}
