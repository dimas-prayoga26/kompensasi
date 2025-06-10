<?php

namespace Database\Seeders;

use App\Models\Prodi;
use App\Models\Semester;
use App\Models\DetailMahasiswa;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class SemesterSeeder extends Seeder
{
    public function run(): void
    {
        $noSemester = 1;

        for ($tahun = 2020; $tahun <= 2025; $tahun++) {
            $tahunAjaran = "{$tahun}/" . ($tahun + 1);

            // Ganjil
            Semester::firstOrCreate([
                'tahun_ajaran' => $tahunAjaran,
                'semester'     => 'Ganjil',
            ], [
                'no_semester'  => $noSemester++,
                'aktif'        => ($tahun == 2025), // misal semester terakhir dijadikan aktif
            ]);

            // Genap
            Semester::firstOrCreate([
                'tahun_ajaran' => $tahunAjaran,
                'semester'     => 'Genap',
            ], [
                'no_semester'  => $noSemester++,
                'aktif'        => false,
            ]);
        }
    }
}
