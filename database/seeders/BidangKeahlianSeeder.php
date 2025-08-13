<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\BidangKeahlian;

class BidangKeahlianSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::beginTransaction();

        try {
            BidangKeahlian::create([
                'nama_keahlian' => 'KBK Sistem Komputer dan Jaringan',
            ]);

            BidangKeahlian::create([
                'nama_keahlian' => 'KBK Rekayasa Perangkat Lunak dan Pengetahuan',
            ]);

            BidangKeahlian::create([
                'nama_keahlian' => 'KBK Sistem Informasi',
            ]);

            BidangKeahlian::create([
                'nama_keahlian' => 'KBK Sains Data',
            ]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error("Error seeding Bidang Keahlian: " . $e->getMessage());
        }
    }
}
