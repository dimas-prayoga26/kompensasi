@extends('admin.layout.main')

@section('title', 'Index')

@section('css')

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">

<style>
    div.dataTables_filter {
        margin-bottom: 2rem;
    }
</style>

@section('content')

<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Dashboard</span></h4>

    <div class="row">
        {{-- Untuk superAdmin --}}
        @role('superAdmin')
            <!-- Mahasiswa Aktif -->
            <div class="col-lg-2 col-md-6 col-12 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-start justify-content-between mb-2">
                            <div class="avatar flex-shrink-0">
                                <i class="fi fi-rr-user-check fs-3 text-success"></i>
                            </div>
                        </div>
                        <span class="fw-semibold d-block mb-1">Mahasiswa Aktif</span>
                        <h3 class="card-title mb-2">{{ number_format($mahasiswaAktif) }}</h3>
                    </div>
                </div>
            </div>

            <!-- Mahasiswa Tidak Aktif -->
            <div class="col-lg-2 col-md-6 col-12 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-start justify-content-between mb-2">
                            <div class="avatar flex-shrink-0">
                                <i class="fi fi-rr-delete-user fs-3 text-danger"></i>
                            </div>
                        </div>
                        <span class="fw-semibold d-block mb-1">Mahasiswa Tidak Aktif</span>
                        <h3 class="card-title mb-2">{{ number_format($mahasiswaTidakAktif) }}</h3>
                    </div>
                </div>
            </div>
        @endrole


        {{-- Untuk Mahasiswa --}}
        @php
            $isMahasiswa = auth()->user()->hasRole('Mahasiswa');
            $itemsPerRow = 4;
            $colClass = 'col-lg-3';
        @endphp

        @if($isMahasiswa && $lamaStudi && count($kompensasiPerSemester) > 0)
            @foreach($kompensasiPerSemester as $semester => $menit)
                @if(($loop->index % $itemsPerRow) == 0)
                    <div class="row">
                @endif

                @php
                    $marginTopClass = $semester > 4 ? 'mt-4' : '';
                @endphp

                <div class="{{ $colClass }} col-md-6 col-12 mb-4 {{ $marginTopClass }}">
                    <div class="card h-100">
                        <div class="card-body">
                            <span class="fw-semibold d-block mb-1">Jumlah Kompensasi Semester {{ $semester }}</span>
                            <h3 class="card-title mb-2">{{ $menit }} Menit</h3>
                        </div>
                    </div>
                </div>

                @if(($loop->iteration % $itemsPerRow == 0) || $loop->last)
                    </div>
                @endif
            @endforeach
        @endif

        {{-- Untuk Dosen --}}
        @role('Dosen')
        <!-- Jumlah Dosen -->
        <div class="col-lg-2 col-md-6 col-12 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between mb-2">
                        <div class="avatar flex-shrink-0">
                            <i class="fi fi-rr-user"></i>
                        </div>
                    </div>
                    <span class="fw-semibold d-block mb-1">Jumlah Dosen</span>
                    <h3 class="card-title mb-2">47</h3>
                </div>
            </div>
        </div>
        @endrole
    </div>

    @role('Mahasiswa')
        <div class="row mt-5">
            <div class="col-lg-12 mb-4 order-0">
                <div class="card">
                    <div class="d-flex justify-content-between align-items-center p-3">
                        <div class="d-flex align-items-center">
                            <h5 class="mb-0 me-3">Detail kompensasi</h5>
                        </div>
                    </div>

                    <div class="d-flex align-items-end row">
                        <div class="col-sm-12">
                            <div class="card-body">
                                <table id="datatable" class="table table-bordered text-nowrap w-100">
                                    <thead>
                                        <tr>
                                            <th>No.</th>
                                            <th>Nama Matkul</th>
                                            <th>Jumlah Kompen</th>
                                            <th>Semester</th>
                                            <th>Keterangan</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
     @endrole

     @role('superAdmin')
        <div class="row mt-5">
            <div class="col-lg-12 mb-4 order-0">
                <div class="card">
                    <div class="d-flex justify-content-between align-items-center p-3">
                        <div class="d-flex align-items-center">
                            <h5 class="mb-0 me-3">Detail kompen mahasiswa akhir semester, kelas</h5>
                            <select id="filterKelas" class="form-select me-2" style="width: 200px;">
                                <option selected disabled>Pilih Kelas</option>
                                @foreach ($kelasAkhir as $kelas)
                                    <option value="{{ $kelas->id }}">
                                        {{ strtoupper(preg_replace('/(\D+)\d+([A-Z])/', '$1 $2', $kelas->nama)) }}
                                    </option>
                                @endforeach
                            </select>
                            <button type="button" id="exportBtn" class="btn btn-success">
                                <i class="fa fa-file-excel"></i> Export
                            </button>
                        </div>
                    </div>

                    <div class="d-flex align-items-end row">
                        <div class="col-sm-12">
                            <div class="card-body">
                                <table id="datatable" class="table table-bordered text-nowrap w-100">
                                    <thead>
                                        <tr>
                                            <th>No.</th>
                                            <th>Nama</th>
                                            <th>Angkatan</th>
                                            <th>Jumlah Kompen</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
     @endrole
</div>




<!-- / Content -->
@endsection

@section('js')

<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>

<script>
    const userRole = "{{ auth()->user()->getRoleNames()->first() }}";
</script>

<script>
    var table;

    $(document).ready(function () {

        // === Jika Mahasiswa ===
        if (userRole === 'Mahasiswa') {
            table = $("#datatable").DataTable({
                responsive: false,
                processing: true,
                serverSide: true,
                autoWidth: false,
                scrollX: true,
                ajax: {
                    url: "{{ route('mahasiswa.dashboard.datatable') }}",
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'nama_matakuliah' },
                    { data: 'menit_kompensasi' },
                    { data: 'semester' },
                    { data: 'keterangan' },
                ],
                columnDefs: [
                    {
                    targets: 0,
                        render: function (data, type, full, meta) {
                            return meta.row + 1;
                        }
                        
                    },
                    {
                        targets: 4,
                        render: function(data, type, full, meta) {
                            if (!data) return 'No description';

                            let htmlDecoded = data
                                .replace(/&lt;/g, '<')
                                .replace(/&gt;/g, '>')
                                .replace(/&amp;/g, '&')
                                .replace(/&quot;/g, '"')
                                .replace(/&#039;/g, "'");

                            let textOnly = htmlDecoded.replace(/<\/?[^>]+(>|$)/g, "");
                            return textOnly ? textOnly : 'No description';
                        }
                    },
                ],
                language: {
                    searchPlaceholder: 'Search...',
                    sSearch: ''
                }
            });

            $('#filterSemester').change(function () {
                table.ajax.reload();
            });
        }

        // === Jika Super Admin ===
        if (userRole === 'superAdmin') {
            table = $("#datatable").DataTable({
                responsive: false,
                processing: true,
                serverSide: true,
                autoWidth: false,
                scrollX: true,
                ajax: {
                    url: "{{ route('admin.dashboard.datatable') }}",
                    data: function (d) {
                        d.kelas_id = $('#filterKelas').val();
                    }
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'mahasiswa' },
                    { data: 'angkatan' },
                    { data: 'jumlah' },
                ],
                columnDefs: [{
                    targets: 0,
                    render: function (data, type, full, meta) {
                        return meta.row + 1;
                    }
                }],
                language: {
                    searchPlaceholder: 'Search...',
                    sSearch: ''
                }
            });

            // Reload jika kelas atau semester berubah
            $('#filterKelas, #filterSemester').change(function () {
                table.ajax.reload();
            });

            $("#exportBtn").on("click", function () {
                let kelasId = $("#filterKelas").val();

                console.log(kelasId);
                

                if (!kelasId) {
                    alert("Silakan pilih kelas terlebih dahulu.");
                    return;
                }

                $.ajax({
                    url: "{{ route('admin.dashboard.export-kompen') }}",
                    type: 'GET',
                    data: { kelas_id: kelasId },
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Export Berhasil!',
                            text: 'Data telah berhasil diexport.',
                        });

                        var link = document.createElement('a');
                        link.href = response.fileUrl;
                        link.download = response.fileName;
                        link.click();
                    },
                    error: function(xhr, status, error) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: 'Terjadi kesalahan saat mengekspor data.',
                        });
                    }
                });
            });
        }

    });
</script>



@endsection