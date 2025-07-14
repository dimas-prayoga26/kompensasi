<?php

namespace Database\Seeders;

use App\Models\Semester;
use Illuminate\Database\Seeder;

class SemesterSeeder extends Seeder
{
    public function run(): void
    {
        // $startTahun = 2020;
        // $noSemester = 1;
        // $tahunGanjil = $startTahun;

        // $targetAktifSemester = 11;
        // $targetCurrentAktifSemester = $targetAktifSemester - 2;

        // while ($noSemester <= $targetAktifSemester) {
        //     $tahunAjaran = "{$tahunGanjil}/" . ($tahunGanjil + 1);

        //     $isAktifGanjil = ($noSemester === $targetAktifSemester);
        //     $isCurrentAktifGanjil = ($noSemester === $targetCurrentAktifSemester);

        //     Semester::updateOrCreate([
        //         'tahun_ajaran' => $tahunAjaran,
        //         'semester'     => 'Ganjil',
        //     ], [
        //         'no_semester'   => $noSemester,
        //         'aktif'         => $isAktifGanjil,
        //         'current_aktif' => $isCurrentAktifGanjil,
        //     ]);
        //     $noSemester++;

        //     if ($noSemester > $targetAktifSemester) break;

        //     $isAktifGenap = ($noSemester === $targetAktifSemester);
        //     $isCurrentAktifGenap = ($noSemester === $targetCurrentAktifSemester);

        //     Semester::updateOrCreate([
        //         'tahun_ajaran' => $tahunAjaran,
        //         'semester'     => 'Genap',
        //     ], [
        //         'no_semester'   => $noSemester,
        //         'aktif'         => $isAktifGenap,
        //         'current_aktif' => $isCurrentAktifGenap,
        //     ]);
        //     $noSemester++;

        //     $tahunGanjil++;
        // }
    }

}

