<?php

namespace App\Providers;

use App\Models\Semester;
use App\Models\Kompensasi;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('admin.layout.navbar', function ($view) {
            $totalKompensasi = 0;

            if (Auth::check()) {
                $user = Auth::user();
                $detail = $user->detailMahasiswa;
                
                $semesterAktif = Semester::where('aktif', true)->first();

                if ($detail && $detail->prodi) {
                    $tahunMasuk = $detail->tahun_masuk;
                    $lamaStudi = $detail->prodi->lama_studi;

                    if ($semesterAktif) {
                        $tahunSekarang = intval(substr($semesterAktif->tahun_ajaran, 0, 4));
                        $tipeSemester = strtolower($semesterAktif->semester);

                        $selisihTahun = $tahunSekarang - $tahunMasuk;
                        $semesterBerjalan = $selisihTahun * 2 + ($tipeSemester == 'genap' ? 2 : 1);

                        $semesterMax = min($semesterBerjalan, $lamaStudi);

                        $totalKompensasi = Kompensasi::where('user_id', $user->id)
                            ->whereBetween('semester_lokal', [1, $semesterMax])
                            ->sum('menit_kompensasi');

                        $tahunAjaran = $semesterAktif->tahun_ajaran;
                        $semester = $semesterAktif->semester;
                    }

                }

                $tahunAjaran = null;
                $semester = null;

                if ($semesterAktif) {
                    $tahunAjaran = $semesterAktif->tahun_ajaran;
                    $semester = $semesterAktif->semester;
                } else {
                    $tahunAjaran = "Tahun ajaran tidak tersedia"; 
                    $semester = "Semester tidak tersedia";
                }


            }

            $view->with([
                'totalKompensasi' => $totalKompensasi,
                'semesterMax' => $semesterMax ?? null,
                'tahunAjaran' => $tahunAjaran ?? null,
                'semesterAktif' => $semester ?? null,
            ]);
        });
    }

}
