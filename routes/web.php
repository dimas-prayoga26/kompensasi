<?php

use App\Models\MatakuliahSemester;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\KelasController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ProdiController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SemesterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MataKuliahController;
use App\Http\Controllers\TugasKompensasiController;
use App\Http\Controllers\MatakuliahDiampuController;

Route::redirect('', '/login');

// routes/web.php (tambahkan di luar group)
Route::get('/login', function () {
    return redirect()->route('mahasiswa.login');
})->name('login');


Route::group(["middleware" => ["guest"]], function() {
    // routes/web.php

Route::group(['middleware' => 'guest'], function () {
    Route::prefix('mahasiswa')->group(function () {
        Route::get('/login', [LoginController::class, 'showLoginForm'])->name('mahasiswa.login');
        Route::post('/login', [LoginController::class, 'login'])->name('mahasiswa.login.post');
    });

    Route::prefix('dosen')->group(function () {
        Route::get('/login', [LoginController::class, 'showLoginForm'])->name('dosen.login');
        Route::post('/login', [LoginController::class, 'login'])->name('dosen.login.post');
    });
});

});

Route::middleware(['web', 'auth'])->group(function () {

    Route::prefix("portal")->group(function() {
        Route::post("/logout", [DashboardController::class, "authLogout"])->name("auth.logout");

        Route::get("/mahasiswa/dashboard/datatable", [DashboardController::class, "mahasiswaDashboardDatatable"])->name("mahasiswa.dashboard.datatable");
        Route::get('/admin/dashboard/datatable', [DashboardController::class, 'adminDashboardDatatable'])->name('admin.dashboard.datatable');
        Route::get('/admin/export-kompen', [DashboardController::class, 'exportKompensasi'])->name('admin.dashboard.export-kompen');

        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Route untuk menampilkan profile
        Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index')->middleware('auth');
        Route::post('/profile/update', [ProfileController::class, 'update'])->name('profile.update');

        Route::get("/user/datatable", [UserController::class, "datatable"])->name("user.datatable");
        Route::get('/user/kelas/select2', [UserController::class, 'select2Kelas'])->name('user.kelas.select2');
        Route::get('/user/detailKelas/detailSelect2', [UserController::class, 'detailSelect2Kelas'])->name('user.kelas.detailSelect2');
        Route::post('/user/import', [UserController::class, 'import'])->name('user.import');
        Route::resource('user', UserController::class);

        Route::get("/mataKuliah/datatable", [MataKuliahController::class, "datatable"])->name("mataKuliah.datatable");
        Route::get("/mataKuliah/getProdi", [MataKuliahController::class, "getProdi"])->name("mataKuliah.getProdi");
        Route::resource('mataKuliah', MataKuliahController::class);

        Route::get("/kelas/datatable", [KelasController::class, "datatable"])->name("kelas.datatable");
        Route::resource('kelas', KelasController::class);

        Route::get("/semester/datatable", [SemesterController::class, "datatable"])->name("semester.datatable");
        Route::resource('semester', SemesterController::class);

        Route::get("/prodi/datatable", [ProdiController::class, "datatable"])->name("prodi.datatable");
        Route::resource('prodi', ProdiController::class);

        Route::get("/matakuliah-diampu/datatable", [MatakuliahDiampuController::class, "datatable"])->name("matakuliah-diampu.datatable");
        Route::get('/matakuliah-diampu/dosen/select2', [MatakuliahDiampuController::class, 'select2Dosen'])->name('matakuliah-diampu.dosen.select2');
        Route::get('/matakuliah-diampu/matakuliah/select2', [MatakuliahDiampuController::class, 'select2Matakuliah'])->name('matakuliah-diampu.matakuliah.select2');
        Route::get('/matakuliah-diampu/kelas/select2', [MatakuliahDiampuController::class, 'select2Kelas'])->name('matakuliah-diampu.kelas.select2');
        Route::get('/matakuliah-diampu/mahasiswa-aktif/{id}', [MatakuliahDiampuController::class, 'editDataMahasiswaAktif'])->name('matakuliah-diampu.mahasiswa-aktif.select2');
        Route::get('/matakuliah-diampu/{id}/tahun-ajaran', [MatakuliahDiampuController::class, 'getTahunAjaranLamaBaru'])->name('matakuliah-diampu.tahun-ajaran');
        Route::put('/matakuliah-diampu/{id}/refresh', [MatakuliahDiampuController::class, 'refreshKompensasi'])->name('matakuliah-diampu.refresh');
        Route::get('/matakuliah-diampu/kompensasi/{id}', [MatakuliahDiampuController::class, 'show'])->name('matakuliah-diampu.kompensasi.show');
        Route::get('/matakuliah-diampu/kompensasi/{id}/detail', [MatakuliahDiampuController::class, 'kompensasiDetail'])->name('matakuliah-diampu.kompensasi.detail');
        Route::put('/matakuliah-diampu/kompensasi/{id}/update', [MatakuliahDiampuController::class, 'kompensasiUpdate'])->name('matakuliah-diampu.kompensasi.update');
        Route::get('/matakuliah-diampu/kompensasi/excel/{id}', [MatakuliahDiampuController::class, 'exportExcel'])->name('matakuliah-diampu.kompensasi.exportExcel');
        Route::get("/matakuliah-diampu/kompensasi/{id}/datatable", [MatakuliahDiampuController::class, "datatableKompensasi"])->name("matakuliah-diampu.kompensasi.datatableKompensasi");
        Route::resource('matakuliah-diampu', MatakuliahDiampuController::class);

        Route::get("/tugas-kompensasi/datatable", [TugasKompensasiController::class, "datatable"])->name("tugas-kompensasi.datatable");
        Route::get('/tugas-kompensasi/{id}/detail', [TugasKompensasiController::class, 'detail']);
        Route::delete('/tugas-kompensasi/detail/{id}', [TugasKompensasiController::class, 'hapusMahasiswa'])->name('tugas-kompensasi.detail.destroy');
        Route::post('/tugas-kompensasi/pilih', [TugasKompensasiController::class, 'storeMahasiswaKompensasi'])->middleware('auth');
        Route::post('/tugas-kompensasi/upload-bukti', [TugasKompensasiController::class, 'uploadBukti'])->name('tugas-kompensasi.upload.bukti');
        Route::get('/tugas-kompensasi/bukti/{id}', [TugasKompensasiController::class, 'downloadBukti'])->name('tugas-kompensasi.download.bukti');
        Route::resource('tugas-kompensasi', TugasKompensasiController::class);
    });
});
// Route::get('/', function () {
//     return view('admin.index');
// });
