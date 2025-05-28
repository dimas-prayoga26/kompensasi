<?php

namespace Database\Seeders;

use App\Models\Prodi;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ProdiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
     public function run()
        {
            $prodis = [
                ['kode_prodi' => '03', 'nama' => 'Informatika', 'lama_studi' => 3],
                ['kode_prodi' => '04', 'nama' => 'Rekayasa Perangkat Lunak', 'lama_studi' => 4],
            ];

            foreach ($prodis as $prodi) {
                Prodi::firstOrCreate(['kode_prodi' => $prodi['kode_prodi']], $prodi);
            }
        }
}
