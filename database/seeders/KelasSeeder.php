<?php

namespace Database\Seeders;

use App\Models\Kelas;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class KelasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $kelas = [];

        // Kelas untuk TI: dari TI1A sampai TI3C
        for ($tingkat = 1; $tingkat <= 3; $tingkat++) {
            foreach (['A', 'B', 'C'] as $huruf) {
                $kelas[] = "TI{$tingkat}{$huruf}";
            }
        }

        // Kelas untuk RPL: dari RPL1A sampai RPL4C
        for ($tingkat = 1; $tingkat <= 4; $tingkat++) {
            foreach (['A', 'B', 'C'] as $huruf) {
                $kelas[] = "RPL{$tingkat}{$huruf}";
            }
        }

        // Simpan ke database
        foreach ($kelas as $nama) {
            \App\Models\Kelas::firstOrCreate(['nama' => $nama]);
        }
    }

}
