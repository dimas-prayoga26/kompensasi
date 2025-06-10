<?php

namespace Database\Seeders;

use App\Models\Kelas;
use App\Models\Semester;
use App\Models\DetailMahasiswa;
use Illuminate\Database\Seeder;
use App\Models\KelasSemesterMahasiswa;

class KelasSemesterMahasiswaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $mahasiswas = DetailMahasiswa::with('user', 'prodi')->get();
        $kelasList = Kelas::all();

        $semesterAktif = Semester::where('aktif', true)->first();

        if (!$semesterAktif) {
            $this->command->warn('Tidak ada semester aktif!');
            return;
        }

        foreach ($mahasiswas as $mahasiswa) {
            $kelas = $kelasList->firstWhere('nama', $mahasiswa->kelas);

            if (!$kelas || !preg_match('/^(TI|RPL)(\d)[A-Z]$/', $kelas->nama, $matches)) {
                $this->command->warn("Kelas tidak valid untuk mahasiswa ID {$mahasiswa->user_id}");
                continue;
            }

            $tingkat = (int)$matches[2];

            // Hitung semester lokal berdasarkan tingkat kelas dan semester aktif (Ganjil/Genap)
            $semesterLokal = ($tingkat - 1) * 2 + ($semesterAktif->semester === 'Ganjil' ? 1 : 2);

            $maksimalSemester = $mahasiswa->prodi->lama_studi ?? 8;
            if ($semesterLokal > $maksimalSemester) {
                $semesterLokal = $maksimalSemester;
                if (($semesterAktif->semester === 'Ganjil') && ($semesterLokal % 2 == 0)) {
                    $semesterLokal -= 1;
                } elseif (($semesterAktif->semester === 'Genap') && ($semesterLokal % 2 != 0)) {
                    $semesterLokal -= 1;
                }
            }

            KelasSemesterMahasiswa::updateOrCreate(
                [
                    'user_id' => $mahasiswa->user_id,
                    'semester_id' => $semesterAktif->id,
                ],
                [
                    'kelas_id' => $kelas->id,
                    'semester_lokal' => $semesterLokal,
                    'is_active' => true, // Tambahkan flag aktif
                ]
            );
        }
    }
}
