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
                    $lamaStudi = $detail->prodi->lama_studi; // Bisa 6 atau 8

                    if ($semesterAktif) {
                        $tahunSekarang = intval(substr($semesterAktif->tahun_ajaran, 0, 4));
                        $tipeSemester = strtolower($semesterAktif->semester); // ganjil/genap

                        $selisihTahun = $tahunSekarang - $tahunMasuk;
                        $semesterBerjalan = $selisihTahun * 2 + ($tipeSemester == 'genap' ? 2 : 1);

                        // Batas maksimal sesuai lama studi
                        $semesterMax = min($semesterBerjalan, $lamaStudi);

                        // Totalkan semua kompensasi dari semester 1 s.d semesterMax
                        $totalKompensasi = Kompensasi::where('user_id', $user->id)
                            ->whereBetween('semester_lokal', [1, $semesterMax])
                            ->sum('menit_kompensasi');

                        // Ambil tahun_ajaran dan semester
                        $tahunAjaran = $semesterAktif->tahun_ajaran;
                        $semester = $semesterAktif->semester;
                    }

                }

                $tahunAjaran = $semesterAktif->tahun_ajaran;
                $semester = $semesterAktif->semester;

            }

            $view->with([
                'totalKompensasi' => $totalKompensasi,
                'semesterMax' => $semesterMax ?? null,
                'tahunAjaran' => $tahunAjaran ?? null, // Mengirimkan tahun ajaran ke view
                'semesterAktif' => $semester ?? null, // Mengirimkan semester aktif ke view dengan nama yang lebih konsisten
            ]);
        });
    }

}
