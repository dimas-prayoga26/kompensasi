<?php

namespace App\Exports;

use App\Models\Kompensasi;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class KompensasiExport implements FromCollection, WithHeadings, WithStyles
{
    protected $dosenMatakuliah;

    public function __construct($dosenMatakuliah)
    {
        $this->dosenMatakuliah = $dosenMatakuliah;
    }

    public function collection()
    {
        $counter = 1;

        return $this->dosenMatakuliah->kompensasis->where('is_active', 1)->map(function ($kompensasi) use (&$counter) {
            $mahasiswa = $kompensasi->user;
            $detail = $mahasiswa->detailMahasiswa;
            $kelas = $detail ? $detail->kelas : '-';

            return [
                'no' => $counter++,
                'nim' => $mahasiswa ? $mahasiswa->nim : '-',
                'kelas' => $kelas,
                'nama_mahasiswa' => $detail ? $detail->first_name . ' ' . $detail->last_name : '-',
                'menit_kompensasi' => strval($kompensasi->menit_kompensasi),
            ];
        });
    }

    public function headings(): array
    {
        return [
            'No',
            'NIM',
            'Kelas',
            'Nama Mahasiswa',
            'Menit Kompensasi',
        ];
    }


    public function styles(Worksheet $sheet)
    {

        $sheet->getColumnDimension('A')->setWidth(5);
        $sheet->getColumnDimension('B')->setWidth(15);
        $sheet->getColumnDimension('C')->setWidth(15);
        $sheet->getColumnDimension('D')->setWidth(25);
        $sheet->getColumnDimension('E')->setWidth(20);

        $sheet->getStyle('C2:C' . $sheet->getHighestRow())->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('D2:D' . $sheet->getHighestRow())->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
    }
}
