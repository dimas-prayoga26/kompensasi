<?php

namespace App\Exports;

use App\Models\Kompensasi;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class RekapKompensasiAllSemester implements FromCollection, WithHeadings, WithMapping
{
    protected $mahasiswa;
    protected $semesterBerjalan;

    public function __construct($mahasiswa, $semesterBerjalan)
    {
        $this->mahasiswa = $mahasiswa;
        $this->semesterBerjalan = $semesterBerjalan;
    }

    public function collection()
    {
        $data = $this->mahasiswa->map(function ($user, $index) {
            $detail = $user->detailMahasiswa;

            if (!$detail || !$detail->prodi || !$detail->tahun_masuk) {
                return null;
            }

            $prodi = $detail->prodi->nama;
            $tahunMasuk = $detail->tahun_masuk;

            $semesterMax = ($prodi === 'Teknik Informatika') ? 6 : 8;

            $semesterData = [];
            $totalKompensasi = 0;

            for ($semester = 1; $semester <= $semesterMax; $semester++) {
                $semesterKompensasi = Kompensasi::where('user_id', $user->id)
                    ->where('semester_lokal', $semester)
                    ->whereHas('user.detailMahasiswa', function ($query) use ($tahunMasuk) {
                        $query->where('tahun_masuk', $tahunMasuk);
                    })
                    ->sum('menit_kompensasi');

                $semesterData[] = $semesterKompensasi ? $semesterKompensasi . ' menit' : '0 menit';
                $totalKompensasi += $semesterKompensasi;
            }

            return [
                'no' => $index + 1,
                'nim' => $user->nim,
                'nama' => "{$detail->first_name} {$detail->last_name}",
                'semester_data' => $semesterData,
                'total' => $totalKompensasi . ' menit',
            ];
        })->filter()->values();

        return collect($data);
    }

    public function headings(): array
    {
        $headers = [
            'No.',
            'NIM',
            'Nama',
        ];

        $prodi = $this->mahasiswa->first()->detailMahasiswa->prodi->nama ?? '';

        $semesterCount = ($prodi === 'Teknik Informatika') ? 6 : 8;
        for ($i = 1; $i <= $semesterCount; $i++) {
            $headers[] = "Semester $i";
        }

        $headers[] = 'Total Kompensasi';

        return $headers;
    }

    public function map($row): array
    {
        $semesterData = $row['semester_data'];

        return [
            $row['no'],
            $row['nim'],
            $row['nama'],
            ...$semesterData,
            $row['total'],
        ];
    }
}

