<?php

namespace Database\Seeders;

use App\Models\Kelas;
use App\Models\Semester;
use App\Models\DetailMahasiswa;
use Illuminate\Database\Seeder;
use App\Models\KelasSemesterMahasiswa;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class KelasSemesterMahasiswaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
     public function run(): void
    {
        $mahasiswas = DetailMahasiswa::with('user')->get();
        $kelasList = Kelas::all();

        foreach ($mahasiswas as $mahasiswa) {
            $tahunMasuk = (int) $mahasiswa->tahun_masuk;
            $tahunAjaran = "{$tahunMasuk}/" . ($tahunMasuk + 1);

            $semester = Semester::where('tahun_ajaran', $tahunAjaran)
                ->where('semester', 'Ganjil')
                ->first();

            $kelasRandom = $kelasList->random(); // pilih kelas secara acak

            if ($semester && $kelasRandom) {
                KelasSemesterMahasiswa::firstOrCreate([
                    'user_id' => $mahasiswa->user_id,
                    'no_semester' => $semester->no_semester, // ✅ pakai no_semester, bukan id
                    'kelas_id' => $kelasRandom->id,
                ]);
            }
        }
    }

}
