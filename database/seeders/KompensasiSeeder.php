<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\KelasSemesterMahasiswa;
use App\Models\Kompensasi;
use App\Models\User;
use App\Models\MatakuliahSemester;

class KompensasiSeeder extends Seeder
{
    public function run(): void
    {
        $entries = KelasSemesterMahasiswa::all();
        $dosenList = User::role('Dosen')->get();

        if ($dosenList->isEmpty()) {
            $this->command->error('Tidak ada dosen ditemukan.');
            return;
        }

        foreach ($entries as $entry) {
            $mahasiswa = $entry->user;
            $semester = $entry->semester;
            $kelas = $entry->kelas;
            $semesterLokal = $entry->semester_lokal;

            if (!$mahasiswa || !$semester || !$kelas || !$semesterLokal) continue;

            $detail = $mahasiswa->detailMahasiswa;
            if (!$detail) continue;

            $prodiId = $detail->prodi_id ?? null;
            if (!$prodiId) continue;

            $matkulSemesterList = MatakuliahSemester::where('semester_lokal', $semesterLokal)
                ->whereHas('matakuliah', function ($q) use ($prodiId) {
                    $q->where('prodi_id', $prodiId);
                })
                ->with('matakuliah')
                ->get();

            foreach ($matkulSemesterList as $matkulSemester) {
                $matkul = $matkulSemester->matakuliah;
                if (!$matkul) continue;

                Kompensasi::create([
                    'kelas_semester_mahasiswa_id' => $entry->id,
                    'dosen_id' => $dosenList->random()->id,
                    'matakuliah_id' => $matkul->id,
                    'menit_kompensasi' => rand(30, 180),
                    'keterangan' => 'Bolos mata kuliah ' . $matkul->nama,
                ]);
            }
        }
    }
}
