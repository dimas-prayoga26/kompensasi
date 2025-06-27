<?php

namespace App\Imports;

use App\Models\User;
use App\Models\Kelas;
use App\Models\Prodi;
use App\Models\Semester;
use App\Models\DetailMahasiswa;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\KelasSemesterMahasiswa;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class UsersImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        DB::beginTransaction();

        try {
            foreach ($rows as $row) {
                // Lewati baris header jika masih ada kolom string
                if (!is_numeric(trim($row['nim']))) {
                    continue;
                }

                $nim        = trim($row['nim']);
                $nama       = trim($row['nama_mahasiswa']);
                $kelasName  = trim($row['kelas']);

                // Ambil kode prodi dari NIM (digit ke-3 dan ke-4)
                $kodeProdi = substr($nim, 2, 2);

                // Cari prodi berdasarkan kode
                $prodi = Prodi::where('kode_prodi', $kodeProdi)->first();
                if (!$prodi) {
                    \Log::warning("Prodi dengan kode $kodeProdi tidak ditemukan untuk NIM $nim.");
                    continue; // skip baris ini
                }

                // Tentukan tahun masuk dari 2 digit awal NIM
                $angkaTahun = (int) substr($nim, 0, 2);
                $tahunSekarang = (int) date('Y');
                $prefixTahun = (int) substr($tahunSekarang, 0, 2);

                // Penyesuaian jika tahun lebih besar dari 2 digit tahun sekarang + 2
                if ($angkaTahun > ((int) substr($tahunSekarang, 2, 2)) + 2) {
                    $prefixTahun -= 1;
                }

                $tahunMasuk = (int) ($prefixTahun . str_pad($angkaTahun, 2, '0', STR_PAD_LEFT));

                // Pisahkan nama menjadi first_name dan last_name
                $namaSplit = explode(' ', $nama);
                $firstName = $namaSplit[0] ?? '';
                $lastName  = implode(' ', array_slice($namaSplit, 1)) ?? '';

                // Pastikan kelas ditemukan
                $kelas = Kelas::where('nama', $kelasName)->first();
                if (!$kelas) {
                    DB::rollBack();
                    return response()->json(['status' => false, 'message' => 'Kelas tidak ditemukan: ' . $kelasName], 422);
                }

                // Buat user baru
                $user = User::create([
                    'name'     => $firstName . ' ' . $lastName,
                    'email'    => $nim . '@polindra.co.id',
                    'password' => Hash::make($nim),
                    'nim'      => $nim,
                ])->assignRole('Mahasiswa');

                // Tambahkan detail mahasiswa
                DetailMahasiswa::create([
                    'user_id'     => $user->id,
                    'first_name'  => $firstName,
                    'last_name'   => $lastName,
                    'tahun_masuk' => $tahunMasuk,
                    'prodi_id'    => $prodi->id,
                    'kelas'       => $kelasName,
                ]);

                // Ambil semester aktif
                $semesterAktif = Semester::where('aktif', true)
                    ->orderByDesc('tahun_ajaran')
                    ->orderByDesc('id')
                    ->first();

                if (!$semesterAktif) {
                    DB::rollBack();
                    return response()->json([
                        'status' => false,
                        'message' => 'Semester aktif tidak ditemukan.'
                    ], 422);
                }

                // Tambahkan ke tabel pivot kelas-semester
                KelasSemesterMahasiswa::create([
                    'user_id'        => $user->id,
                    'semester_id'    => $semesterAktif->id,
                    'kelas_id'       => $kelas->id,
                    'semester_lokal' => 1,
                    'is_active'      => true,
                ]);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error("Import gagal: " . $e->getMessage());
            throw $e;
        }
    }

}

