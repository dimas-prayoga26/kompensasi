<?php

namespace Database\Seeders;

use App\Models\Prodi;
use App\Models\Semester;
use App\Models\Matakuliah;
use Illuminate\Database\Seeder;
use App\Models\MatakuliahSemester;

class MataKuliahSeeder extends Seeder
{
    public function run()
    {
        $prodis = Prodi::all();
        $semesters = Semester::orderBy('no_semester')->get();

        if ($prodis->isEmpty()) {
            $this->command->warn('Prodi belum ada. Tambahkan data prodi terlebih dahulu.');
            return;
        }

        if ($semesters->isEmpty()) {
            $this->command->warn('Semester global belum ada. Jalankan SemesterSeeder terlebih dahulu.');
            return;
        }

        $mataKuliahs = [
            ['kode' => 'MAT101', 'nama' => 'Matematika Dasar', 'sks' => 3],
            ['kode' => 'FIS102', 'nama' => 'Fisika Dasar', 'sks' => 3],
            ['kode' => 'BIO103', 'nama' => 'Biologi Umum', 'sks' => 3],
            ['kode' => 'KIM104', 'nama' => 'Kimia Dasar', 'sks' => 3],
            ['kode' => 'ENG105', 'nama' => 'Bahasa Inggris', 'sks' => 2],
            ['kode' => 'IND106', 'nama' => 'Bahasa Indonesia', 'sks' => 2],
            ['kode' => 'TIK107', 'nama' => 'Pengantar Teknologi Informasi', 'sks' => 2],
            ['kode' => 'KOM108', 'nama' => 'Algoritma dan Pemrograman', 'sks' => 3],
            ['kode' => 'KOM109', 'nama' => 'Struktur Data', 'sks' => 3],
            ['kode' => 'KOM110', 'nama' => 'Basis Data', 'sks' => 3],
            ['kode' => 'KOM111', 'nama' => 'Pemrograman Web', 'sks' => 3],
            ['kode' => 'KOM112', 'nama' => 'Jaringan Komputer', 'sks' => 3],
            ['kode' => 'KOM113', 'nama' => 'Sistem Operasi', 'sks' => 3],
            ['kode' => 'KOM114', 'nama' => 'Pemrograman Mobile', 'sks' => 3],
            ['kode' => 'KOM115', 'nama' => 'Kecerdasan Buatan', 'sks' => 3],
            ['kode' => 'KOM116', 'nama' => 'Rekayasa Perangkat Lunak', 'sks' => 3],
            ['kode' => 'KOM117', 'nama' => 'Keamanan Informasi', 'sks' => 2],
            ['kode' => 'KOM118', 'nama' => 'Manajemen Proyek TI', 'sks' => 2],
            ['kode' => 'KOM119', 'nama' => 'Etika Profesi TI', 'sks' => 2],
            ['kode' => 'KOM120', 'nama' => 'Kewirausahaan', 'sks' => 2],
        ];

        foreach ($mataKuliahs as $index => $matkul) {
            foreach ($prodis as $prodi) {
                $kodeUnik = $matkul['kode'] . '_' . $prodi->id;

                $matakuliah = Matakuliah::firstOrCreate(
                    ['kode' => $kodeUnik],
                    [
                        'nama' => $matkul['nama'],
                        'sks' => $matkul['sks'],
                        'deskripsi' => 'Deskripsi untuk ' . $matkul['nama'],
                        'prodi_id' => $prodi->id,
                    ]
                );

                $maxSemester = $prodi->lama_studi ?? 8;
                $semesterLokal = ($index % $maxSemester) + 1;

                $semesterGlobal = $semesters->firstWhere('no_semester', $semesterLokal);
                if (!$semesterGlobal) {
                    $this->command->warn("Semester global dengan no_semester {$semesterLokal} tidak ditemukan.");
                    continue;
                }

                MatakuliahSemester::firstOrCreate([
                    'matakuliah_id'   => $matakuliah->id,
                    'semester_id'     => $semesterGlobal->id,
                ], [
                    'semester_lokal'  => $semesterLokal
                ]);
            }
        }
    }
}
