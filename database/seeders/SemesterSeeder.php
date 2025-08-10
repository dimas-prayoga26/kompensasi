<?php

namespace Database\Seeders;

use App\Models\Semester;
use Illuminate\Database\Seeder;

class SemesterSeeder extends Seeder
{
    public function run(): void
    {
        Semester::updateOrCreate(
            [
                'tahun_ajaran' => '2025/2026',
                'semester'     => 'Ganjil',
            ],
            [
                'no_semester'   => 1,
                'aktif'         => true,
            ]
        );
    }


}

