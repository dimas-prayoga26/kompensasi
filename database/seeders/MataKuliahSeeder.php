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
        // Data Mata Kuliah untuk setiap semester (Prodi Teknik Informatika dan Rekayasa Perangkat Lunak)
        $mataKuliahDataInformatika = [
            // Semester 1 Ganjil
            'SEMESTER 1 GANJIL' => [
                ['kode' => 'TIP2101', 'nama' => 'Bahasa Inggris 1', 'sks' => 2],
                ['kode' => 'TIP2102', 'nama' => 'Pengantar Teknologi Informasi Dan Komunikasi', 'sks' => 2],
                ['kode' => 'TIP4101', 'nama' => 'Arsitektur Dan Organisasi Komputer', 'sks' => 3],
                ['kode' => 'TIP4102', 'nama' => 'Desain Grafis', 'sks' => 2],
                ['kode' => 'TIP4103', 'nama' => 'Matematika Teknik', 'sks' => 2],
                ['kode' => 'TIP4104', 'nama' => 'Sistem Operasi', 'sks' => 2],
                ['kode' => 'TIU3101', 'nama' => 'Jaringan Komputer', 'sks' => 3],
                ['kode' => 'TIU4101', 'nama' => 'Algoritma Dan Pemrograman', 'sks' => 4],
            ],
            // Semester 2 Genap
            'SEMESTER 2 GENAP' => [
                ['kode' => 'TIP4201', 'nama' => 'Interaksi Manusia dan Komputer', 'sks' => 3],
                ['kode' => 'TIP4202', 'nama' => 'Matematika Diskrit', 'sks' => 2],
                ['kode' => 'TIU2201', 'nama' => 'Proyek 1', 'sks' => 3],
                ['kode' => 'TIU3201', 'nama' => 'Basis Data', 'sks' => 3],
                ['kode' => 'TIU3202', 'nama' => 'Jaringan Komputer Lanjut', 'sks' => 3],
                ['kode' => 'TIU3203', 'nama' => 'Pemrograman Web', 'sks' => 3],
                ['kode' => 'TIU4201', 'nama' => 'Struktur Data', 'sks' => 3],
            ],
            // Semester 3 Ganjil
            'SEMESTER 3 GANJIL' => [
                ['kode' => 'TIP2301', 'nama' => 'Bahasa Inggris 2', 'sks' => 2],
                ['kode' => 'TIP4301', 'nama' => 'Internet Of Things', 'sks' => 3],
                ['kode' => 'TIU4302', 'nama' => 'Metode Numerik', 'sks' => 3],
                ['kode' => 'TIU2301', 'nama' => 'Proyek 2', 'sks' => 3],
                ['kode' => 'TIU3301', 'nama' => 'Administrasi Sistem dan Jaringan Komputer', 'sks' => 3],
                ['kode' => 'TIU3302', 'nama' => 'Pemrograman Berorientasi Objek', 'sks' => 3],
                ['kode' => 'TIU3303', 'nama' => 'Pemrograman Web Lanjut', 'sks' => 3],
            ],
            // Semester 4 Genap
            'SEMESTER 4 GENAP' => [
                ['kode' => 'TIP2401', 'nama' => 'Bahasa Indonesia', 'sks' => 2],
                ['kode' => 'TIP3401', 'nama' => 'Kecerdasan Buatan', 'sks' => 2],
                ['kode' => 'TIU4401', 'nama' => 'Pengolahan Citra Digital', 'sks' => 2],
                ['kode' => 'TIU2401', 'nama' => 'Proyek 3', 'sks' => 3],
                ['kode' => 'TIU3401', 'nama' => 'Basis Data Lanjut', 'sks' => 3],
                ['kode' => 'TIU3402', 'nama' => 'Keamanan Komputer', 'sks' => 2],
                ['kode' => 'TIU3304', 'nama' => 'Pemrograman Perangkat Bergerak', 'sks' => 3],
                ['kode' => 'TIU3404', 'nama' => 'Rekayasa Perangkat Lunak', 'sks' => 3],
            ],
            // Semester 5 Ganjil
            'SEMESTER 5 GANJIL' => [
                ['kode' => 'TIU2501', 'nama' => 'PROGRAM PILIHAN MBKM', 'sks' => 20],
            ],
            // Semester 6 Genap
            'SEMESTER 6 GENAP' => [
                ['kode' => 'TIP1601', 'nama' => 'Agama', 'sks' => 2],
                ['kode' => 'TIP1602', 'nama' => 'Etika Profesi', 'sks' => 2],
                ['kode' => 'TIP1603', 'nama' => 'Kewarganegaraan', 'sks' => 2],
                ['kode' => 'TIP1604', 'nama' => 'Kewirausahaan', 'sks' => 2],
                ['kode' => 'TIP1605', 'nama' => 'Pancasila', 'sks' => 2],
                ['kode' => 'TIU2601', 'nama' => 'Tugas Akhir', 'sks' => 6],
            ],
        ];

        // Data Mata Kuliah untuk Rekayasa Perangkat Lunak
        $mataKuliahDataRpl = [
            // Semester 1 Ganjil
            'SEMESTER 1 GANJIL' => [
                ['kode' => 'RPLHR1022', 'nama' => 'Bahasa Inggris 1', 'sks' => 1],
                ['kode' => 'RPLKU1013', 'nama' => 'Pengantar Rekayasa Perangkat Lunak', 'sks' => 1],
                ['kode' => 'RPLKU1024', 'nama' => 'Algoritma dan Pemrograman', 'sks' => 2],
                ['kode' => 'RPLKU1033', 'nama' => 'Arsitektur Komputer', 'sks' => 1],
                ['kode' => 'RPLKU1042', 'nama' => 'Sistem Operasi', 'sks' => 1],
                ['kode' => 'RPLPU1023', 'nama' => 'Matematika Diskrit', 'sks' => 1],
                ['kode' => 'RPLPU1033', 'nama' => 'Matematika Terapan', 'sks' => 1],
            ],
            // Semester 2 Genap
            'SEMESTER 2 GENAP' => [
                ['kode' => 'RPLKK2013', 'nama' => 'Desain Perangkat Lunak 1', 'sks' => 3],
                ['kode' => 'RPLKK2023', 'nama' => 'Rekayasa Kebutuhan Perangkat Lunak', 'sks' => 3],
                ['kode' => 'RPLKU2053', 'nama' => 'Pemrograman Web 1', 'sks' => 3],
                ['kode' => 'RPLKU2063', 'nama' => 'Sistem Basis Data 1', 'sks' => 3],
                ['kode' => 'RPLKU2073', 'nama' => 'Struktur Data', 'sks' => 3],
                ['kode' => 'RPLKU2083', 'nama' => 'Jaringan Komputer', 'sks' => 3],
                ['kode' => 'RPLPU2012', 'nama' => 'Aljabar Linear', 'sks' => 2],
            ],
            // Semester 3 Ganjil
            'SEMESTER 3 GANJIL' => [
                ['kode' => 'RPLKK4093', 'nama' => 'Proyek 1', 'sks' => 3],
                ['kode' => 'RPLKU3093', 'nama' => 'Pemrograman Web 2', 'sks' => 3],
                ['kode' => 'RPLKU3103', 'nama' => 'Pemrograman Mobile 1', 'sks' => 3],
                ['kode' => 'RPLKU3112', 'nama' => 'Interaksi Manusia & Komputer', 'sks' => 2],
                ['kode' => 'RPLKU3123', 'nama' => 'Sistem Basis Data 2', 'sks' => 3],
                ['kode' => 'RPLKU3133', 'nama' => 'Pemrograman Berorientasi Objek', 'sks' => 3],
                ['kode' => 'RPLKU3162', 'nama' => 'Probabilitas & Statistika', 'sks' => 3],
            ],
            // Semester 4 Genap
            'SEMESTER 4 GENAP' => [
                ['kode' => 'RPLHR5032', 'nama' => 'Bahasa Inggris 2', 'sks' => 2],
                ['kode' => 'RPLKK3052', 'nama' => 'Desain Perangkat Lunak 2', 'sks' => 3],
                ['kode' => 'RPLKK4034', 'nama' => 'Pengujian dan Penjaminan Kualitas Perangkat Lunak', 'sks' => 4],
                ['kode' => 'RPLKK4083', 'nama' => 'Keamanan Perangkat Lunak', 'sks' => 3],
                ['kode' => 'RPLKK5062', 'nama' => 'Integrasi Berkelanjutan (CI/CD)', 'sks' => 2],
                ['kode' => 'RPLKK5103', 'nama' => 'Proyek 2', 'sks' => 3],
                ['kode' => 'RPLKU4153', 'nama' => 'Administrasi Sistem', 'sks' => 3],
            ],
            // Semester 5 Ganjil
            'SEMESTER 5 GANJIL' => [],
            // Semester 6 Genap
            'SEMESTER 6 GENAP' => [
                ['kode' => 'RPLKK6073', 'nama' => 'Komputasi Awan', 'sks' => 3],
                ['kode' => 'RPLKU4142', 'nama' => 'Grafika Komputer', 'sks' => 2],
                ['kode' => 'RPLKU5163', 'nama' => 'Sistem Terdistribusi', 'sks' => 3],
                ['kode' => 'RPLKU5173', 'nama' => 'Pemrograman Mobile 2', 'sks' => 3],
                ['kode' => 'RPLKU6193', 'nama' => 'Manajemen Proyek Perangkat Lunak', 'sks' => 3],
                ['kode' => 'RPLKU6203', 'nama' => 'Kecerdasan Buatan', 'sks' => 3],
                ['kode' => 'RPLKU6212', 'nama' => 'Visi Komputer', 'sks' => 3],
            ],

            'SEMESTER 7 GANJIL' => [],

            'SEMESTER 8' => [
                ['kode' => null, 'nama' => 'Skripsi', 'sks' => 6],
                ['kode' => null, 'nama' => 'Kewirausahaan', 'sks' => 2],
                ['kode' => null, 'nama' => 'Etika Profesi', 'sks' => 2],
            ],
        ];

        $semesterGlobal = Semester::where('aktif', true)->first();

        $prodiIds = [1, 2];

        foreach ($prodiIds as $prodiId) {
            $mataKuliahData = $prodiId == 1 ? $mataKuliahDataInformatika : $mataKuliahDataRpl;

            foreach ($mataKuliahData as $semesterLokal => $mataKuliah) {
                foreach ($mataKuliah as $mk) {
                    $matakuliah = Matakuliah::firstOrCreate([
                        'kode' => $mk['kode'],
                        'nama' => $mk['nama'],
                        'sks' => $mk['sks'],
                        'prodi_id' => $prodiId,
                    ]);

                    $semesterLokalAngka = $this->getSemesterLokal($semesterLokal, $prodiId);

                    MatakuliahSemester::firstOrCreate([
                        'matakuliah_id' => $matakuliah->id,
                        'semester_id' => $semesterGlobal->id,
                    ], [
                        'semester_lokal' => $semesterLokalAngka,
                    ]);
                }
            }
        }

    }


    /**
 * Fungsi untuk mengonversi nama semester menjadi angka
 * 
    * @param string $semesterLokal
    * @param int $prodiId
    * @return int
    */
    private function getSemesterLokal($semesterLokal, $prodiId)
    {
        $semesterMapTI = [
            'SEMESTER 1 GANJIL' => 1,
            'SEMESTER 2 GENAP' => 2,
            'SEMESTER 3 GANJIL' => 3,
            'SEMESTER 4 GENAP' => 4,
            'SEMESTER 5 GANJIL' => 5,
            'SEMESTER 6 GENAP' => 6,
        ];

        $semesterMapRPL = [
            'SEMESTER 1 GANJIL' => 1,
            'SEMESTER 2 GENAP' => 2,
            'SEMESTER 3 GANJIL' => 3,
            'SEMESTER 4 GENAP' => 4,
            'SEMESTER 5 GANJIL' => 5,
            'SEMESTER 6 GENAP' => 6,
            'SEMESTER 7 GANJIL' => 7,
            'SEMESTER 8 GENAP' => 8,
        ];

        $semesterMap = ($prodiId == 1) ? $semesterMapTI : $semesterMapRPL;

        return $semesterMap[$semesterLokal] ?? 0; 
    }

}
