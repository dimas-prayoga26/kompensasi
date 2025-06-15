<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            ProdiSeeder::class,
            KelasSeeder::class,
            UserSeeder::class,
            SemesterSeeder::class,
            KelasSemesterMahasiswaSeeder::class,
            MatakuliahSeeder::class,
            // KompensasiSeeder::class
        ]);
    }

}
