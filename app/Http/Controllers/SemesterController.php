<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\Semester;
use App\Models\Kompensasi;
use Illuminate\Http\Request;
use App\Models\DetailMahasiswa;
use Illuminate\Support\Facades\DB;
use App\Models\KelasSemesterMahasiswa;

class SemesterController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.metadata.semester.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'tahun_ajaran' => 'required|string',
            'semester' => 'required|in:Ganjil,Genap',
        ]);

        DB::beginTransaction();

        try {
            Semester::where('aktif', true)->update(['aktif' => false]);

            $semesterBaru = Semester::create([
                'tahun_ajaran' => $request->tahun_ajaran,
                'semester' => $request->semester,
                'no_semester' => Semester::max('no_semester') + 1,
                'aktif' => true,
            ]);

            Semester::where('current_aktif', true)->update(['current_aktif' => false]);

            $tahunBaru = $request->tahun_ajaran;
            $jenisSemester = $request->semester;
            $tahunPecah = explode('/', $tahunBaru);
            $tahunSebelum = ((int)$tahunPecah[0] - 1) . '/' . ((int)$tahunPecah[1] - 1);

            if ($jenisSemester === 'Ganjil') {
                Semester::where('tahun_ajaran', $tahunBaru)
                    ->where('semester', 'Genap')
                    ->update(['current_aktif' => true]);
            } else {
                Semester::where('tahun_ajaran', $tahunSebelum)
                    ->where('semester', 'Genap')
                    ->update(['current_aktif' => true]);
            }

            $semesterIdBaru = $semesterBaru->id;

            $mahasiswaList = KelasSemesterMahasiswa::whereHas('semester', function ($query) {
                $query->where('aktif', false);
            })->get();

            foreach ($mahasiswaList as $item) {
                $semesterLokalBaru = $item->semester_lokal + 1;

                $detail = DetailMahasiswa::where('user_id', $item->user_id)->first();
                $prodi = $detail?->prodi;

                if (!$prodi) continue;

                $maxSemester = $prodi->lama_studi ?? 8;

                if ($semesterLokalBaru > $maxSemester) {
                    $item->update(['is_active' => false]);
                    continue;
                }

                Kompensasi::where('user_id', $item->user_id)
                ->where('is_active', true)
                ->update(['is_active' => false]);

                $kelasLama = Kelas::find($item->kelas_id);
                $kelasBaruId = $item->kelas_id;

                if ($semesterBaru->semester === 'Ganjil' && $kelasLama && preg_match('/^(TI|RPL)(\d)([A-Z])$/', $kelasLama->nama, $matches)) {
                    $prefix = $matches[1];
                    $tingkat = (int)$matches[2] + 1;
                    $huruf = $matches[3];
                    $kelasBaruNama = $prefix . $tingkat . $huruf;

                    $kelasBaru = Kelas::where('nama', $kelasBaruNama)->first();
                    if ($kelasBaru) {
                        $kelasBaruId = $kelasBaru->id;
                        DetailMahasiswa::where('user_id', $item->user_id)
                            ->update(['kelas' => $kelasBaru->nama]);
                    }
                }

                $item->update([
                    'semester_id' => $semesterIdBaru,
                    'semester_lokal' => $semesterLokalBaru,
                    'kelas_id' => $kelasBaruId,
                    'is_active' => true,
                ]);
            }

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Semester baru berhasil ditambahkan dan data mahasiswa diperbarui.',
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => 'Gagal menambahkan semester baru.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function datatable(Request $request)
    {
        $data = Semester::orderByDesc('no_semester')->get();

        return datatables()->of($data)->make(true);
    }


}
