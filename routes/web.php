<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KelasController;
use App\Http\Controllers\MataKuliahController;
use App\Http\Controllers\ProdiController;
use App\Http\Controllers\SemesterController;
use App\Http\Controllers\UserController;
use App\Models\MatakuliahSemester;

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

        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        Route::get("/user/datatable", [UserController::class, "datatable"])->name("user.datatable");
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
    });
});
// Route::get('/', function () {
//     return view('admin.index');
// });
