<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\JabatanFungsional;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class JabatanFungsionalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::beginTransaction();

        try {
            JabatanFungsional::create([
                'nama_jabatan' => 'Ketua',
            ]);

            JabatanFungsional::create([
                'nama_jabatan' => 'Anggota',
            ]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error("Error seeding Jabatan Fungsional: " . $e->getMessage());
        }
    }
}