@extends('admin.layout.main')

@section('title', 'Prodi')

@section('css')

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">

<style>
    div.dataTables_filter {
        margin-bottom: 2rem;
    }
</style>


@section('content')

<div class="container-xxl flex-grow-1 container-p-y">

    <h4 class="fw-bold py-3 mb-4">
    <span class="text-muted fw-light">Settings /</span> Prodi
    </h4>

        @if (session('success'))
            <div id="success-alert" class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('error'))
            <div id="error-alert" class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif



    <div class="row">
      <div class="col-lg-12 mb-4 order-0">
          <div class="card">
            <div class="d-flex justify-content-between align-items-center p-3">
                  <h5 class="mb-0">Daftar Data User</h5>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahData">
                    + Tambah Data
                    </button>

              </div>
              <div class="d-flex align-items-end row">
                  <div class="col-sm-12">
                      <div class="card-body">
                          <table id="datatable" class="table table-bordered text-nowrap w-100">
                              <thead>
                                  <tr>
                                      <th>No.</th>
                                      <th>Kode Prodi</th>
                                      <th>Nama</th>
                                      <th>Lama Studi</th>
                                      <th>Aksi</th>
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

    <div class="modal fade" id="modalTambahData" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title" id="modalLabel">Tambah Data Dosen</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <form id="tambahData" enctype="multipart/form-data">
        <div class="modal-body">

          <div class="mb-3 mt-3">
            <label for="nim" class="form-label" id="labelNim">NIP</label>
            <input type="text" class="form-control" id="nim" name="nim" placeholder="Masukkan NIP">
          </div>

          <div class="mb-3 mt-3">
            <label for="name" class="form-label" id="labelNim">Nama</label>
            <input type="text" class="form-control" id="name" name="name" placeholder="Masukkan Nama">
          </div>

        <div class="mb-3 mt-3">
            <label for="lama_studi" class="form-label">Lama Studi</label>
            <input type="number" class="form-control" id="lama_studi" name="lama_studi" placeholder="Masukkan Lama Studi (tahun)">
        </div>


        </div>

        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Simpan</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        </div>
      </form>

    </div>
  </div>
</div>

</div>

@endsection

@section('js')

<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>


    <script>
        window.setTimeout(function() {
            var successAlert = document.getElementById('success-alert');
            if (successAlert) {
                var alert = bootstrap.Alert.getOrCreateInstance(successAlert);
                alert.close();
            }


            var errorAlert = document.getElementById('error-alert');
            if (errorAlert) {
                var alert = bootstrap.Alert.getOrCreateInstance(errorAlert);
                alert.close();
            }
        }, 5000);

        var table;

        $(document).ready(function () {
            table = $("#datatable").DataTable({
                responsive: true,
                processing: true,
                serverSide: true,
                autoWidth: false,
                ajax: {
                    url: "{{ route('prodi.datatable') }}",
                },
                columnDefs: [
                    {
                        targets: 0,
                        render: function (data, type, full, meta) {
                            console.log(full);

                            return meta.row + 1;
                        }
                    },
                    {
                        targets: 4,
                        render: function (data, type, full, meta) {
                            return `
                                <button type="button" class="btn btn-warning btn-sm" onclick="editData(${full.id})">
                                    <i class="fe fe-edit"></i> Edit
                                </button>
                                <button type="button" class="btn btn-danger btn-sm" onclick="hapusData(${full.id})">
                                    <i class="fe fe-trash"></i> Hapus
                                </button>
                            `;
                        }
                    }

                ],
                columns: [
                    { data: null },
                    { data: 'kode_prodi' },
                    { data: 'nama' },
                    { data: 'lama_studi' },
                    { data: 'id' }
                ],
                language: {
                    searchPlaceholder: 'Search...',
                    sSearch: ''
                }
            });
        });


        </script>


@endsection