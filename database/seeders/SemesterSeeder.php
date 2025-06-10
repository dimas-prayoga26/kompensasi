<?php

namespace Database\Seeders;

use App\Models\Semester;
use Illuminate\Database\Seeder;

class SemesterSeeder extends Seeder
{
    public function run(): void
    {
        $startTahun = 2020;
        $noSemester = 1;
        $tahunGanjil = $startTahun;

        while ($noSemester <= 11) {
            $tahunAjaranGanjil = "{$tahunGanjil}/" . ($tahunGanjil + 1);

            // Tambah semester Ganjil
            Semester::firstOrCreate([
                'tahun_ajaran' => $tahunAjaranGanjil,
                'semester'     => 'Ganjil',
            ], [
                'no_semester'  => $noSemester,
                'aktif'        => ($noSemester === 11),
            ]);
            $noSemester++;

            if ($noSemester > 11) break;

            // Tahun ajaran Genap lompat ke tahun berikutnya
            $tahunGenap = $tahunGanjil + 1;
            $tahunAjaranGenap = "{$tahunGenap}/" . ($tahunGenap + 1);

            // Tambah semester Genap
            Semester::firstOrCreate([
                'tahun_ajaran' => $tahunAjaranGenap,
                'semester'     => 'Genap',
            ], [
                'no_semester'  => $noSemester,
                'aktif'        => false,
            ]);
            $noSemester++;

            // Update tahun Ganjil berikutnya
            $tahunGanjil++;
        }
    }
}
