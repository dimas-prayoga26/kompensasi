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
                            <i class="fi fi-rr-user-check"></i>
                        </div>
                    </div>
                    <span class="fw-semibold d-block mb-1">Mahasiswa Aktif</span>
                    <h3 class="card-title mb-2">1,234</h3>
                </div>
            </div>
        </div>

        <!-- Mahasiswa Inaktif -->
        <div class="col-lg-2 col-md-6 col-12 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between mb-2">
                        <div class="avatar flex-shrink-0">
                            <i class="fi fi-rr-delete-user"></i>
                        </div>
                    </div>
                    <span class="fw-semibold d-block mb-1">Mahasiswa Inaktif</span>
                    <h3 class="card-title mb-2">245</h3>
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

                <div class="{{ $colClass }} col-md-6 col-12 mb-4">
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

    <div class="row mt-5">
      <div class="col-lg-12 mb-4 order-0">
          <div class="card">
            <div class="d-flex justify-content-between align-items-center p-3">
                <div class="d-flex align-items-center">
                    <h5 class="mb-0 me-3">Detail kompensasi</h5>
                     <select id="filterSemester" class="form-select" style="width: 200px;">
                        <option selected disabled>Pilih Semester</option>
                        @for ($i = 1; $i <= $lamaStudi; $i++)
                            <option value="{{ $i }}">Semester {{ $i }}</option>
                        @endfor
                    </select>
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
</div>




<!-- / Content -->
@endsection

@section('js')

<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>

<script>
    var table;

    $(document).ready(function () {
        table = $("#datatable").DataTable({
            responsive: true,
            processing: true,
            serverSide: true,
            autoWidth: false,
            ajax: {
                url: "{{ route('dashboard.datatable') }}",
                data: function (d) {
                    d.semester = $('#filterSemester').val(); // Tambahkan filter semester
                }
            },
            columnDefs: [
                {
                    targets: 0,
                    render: function (data, type, full, meta) {
                        return meta.row + 1;
                    }
                },
            ],
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'nama_matakuliah' },
                { data: 'menit_kompensasi' },
                { data: 'keterangan' },
            ],
            language: {
                searchPlaceholder: 'Search...',
                sSearch: ''
            }
        });

        // Trigger reload ketika semester berubah
        $('#filterSemester').change(function () {
            table.ajax.reload();
        });
    });

</script>


@endsection