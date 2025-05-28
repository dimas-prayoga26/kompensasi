<?php

namespace Database\Seeders;

use App\Models\Prodi;
use App\Models\Semester;
use App\Models\DetailMahasiswa;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class SemesterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
{
    $tahunMasuk = 2023;
    $tahunAjaran = "{$tahunMasuk}/" . ($tahunMasuk + 1);

    $sudahAda = \App\Models\Semester::where('tahun_ajaran', $tahunAjaran)
        ->where('semester', 'Ganjil')
        ->exists();

    if (!$sudahAda) {
        \App\Models\Semester::create([
            'tahun_ajaran' => $tahunAjaran,
            'semester' => 'Ganjil',
            'no_semester' => 1
        ]);
    }
}




}
