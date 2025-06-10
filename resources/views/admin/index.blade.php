@extends('admin.layout.main')

@section('title', 'Index')

@section('css')

@section('content')

<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Dashboard</span></h4>

    <div class="row">
        <!-- Mahasiswa Aktif -->
        <div class="col-lg-3 col-md-6 col-12 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between mb-2">
                        <div class="avatar flex-shrink-0">
                            <i class="flaticon-student" style="font-size: 30px;"></i>
                        </div>
                    </div>
                    <span class="fw-semibold d-block mb-1">Mahasiswa Aktif</span>
                    <h3 class="card-title mb-2">1,234</h3>
                </div>
            </div>
        </div>

        <!-- Mahasiswa Inaktif -->
        <div class="col-lg-3 col-md-6 col-12 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between mb-2">
                        <div class="avatar flex-shrink-0">
                            <i class="flaticon-user-block" style="font-size: 30px;"></i>
                        </div>
                    </div>
                    <span class="fw-semibold d-block mb-1">Mahasiswa Inaktif</span>
                    <h3 class="card-title mb-2">245</h3>
                </div>
            </div>
        </div>

        <!-- Jumlah Kompensasi -->
        <div class="col-lg-3 col-md-6 col-12 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between mb-2">
                        <div class="avatar flex-shrink-0">
                            <i class="flaticon-clock" style="font-size: 30px;"></i>
                        </div>
                    </div>
                    <span class="fw-semibold d-block mb-1">Jumlah Kompensasi</span>
                    <h3 class="card-title mb-2">320 Menit</h3>
                </div>
            </div>
        </div>

        <!-- Jumlah Dosen -->
        <div class="col-lg-3 col-md-6 col-12 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between mb-2">
                        <div class="avatar flex-shrink-0">
                            <i class="flaticon-teacher" style="font-size: 30px;"></i>
                        </div>
                    </div>
                    <span class="fw-semibold d-block mb-1">Jumlah Dosen</span>
                    <h3 class="card-title mb-2">47</h3>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- / Content -->
@endsection

@section('js')


@endsection