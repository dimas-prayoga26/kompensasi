<?php

namespace Database\Seeders;

use App\Models\Prodi;
use Illuminate\Database\Seeder;

class ProdiSeeder extends Seeder
{
    public function run()
    {
        $prodis = [
            ['kode_prodi' => '03', 'nama' => 'Informatika', 'lama_studi' => 6], // 3 tahun = 6 semester
            ['kode_prodi' => '04', 'nama' => 'Rekayasa Perangkat Lunak', 'lama_studi' => 8], // 4 tahun = 8 semester
        ];

        foreach ($prodis as $prodi) {
            Prodi::firstOrCreate(['kode_prodi' => $prodi['kode_prodi']], $prodi);
        }
    }
}
