@extends('admin.layout.main')

@section('title', 'Kelas')

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
    <span class="text-muted fw-light">Settings /</span> Kelas
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
                  <h5 class="mb-0">Daftar Data Kelas</h5>
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
                                      <th>Nama</th>
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

    <div class="modal fade" id="modalTambahData" tabindex="-1" aria-labelledby="modalTambahDataLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
            <form id="tambahData" enctype="multipart/form-data">
                <div class="modal-header">
                <h5 class="modal-title" id="modalTambahDataLabel">Tambah Data Kelas</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                <div class="mb-3">
                    <label for="tambah_nama_kelas" class="form-label">Nama Kelas</label>
                    <input type="text" class="form-control" id="tambah_nama_kelas" name="nama" required>
                </div>
                </div>
                <div class="modal-footer">
                <button type="submit" class="btn btn-primary" id="simpanData">Simpan</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                </div>
            </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalEditData" tabindex="-1" aria-labelledby="modalEditDataLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
            <form id="editData" enctype="multipart/form-data">
                <div class="modal-header">
                <h5 class="modal-title" id="modalEditDataLabel">Edit Data Kelas</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                <input type="hidden" id="edit_id_kelas" name="id">
                <div class="mb-3">
                    <label for="edit_nama_kelas" class="form-label">Nama Kelas</label>
                    <input type="text" class="form-control" id="edit_nama_kelas" name="nama" required>
                </div>
                </div>
                <div class="modal-footer">
                <button type="submit" class="btn btn-primary" id="updateData">Simpan Perubahan</button>
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
                    url: "{{ route('kelas.datatable') }}",
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
                        targets: 2,
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
                    { data: 'nama' },
                    { data: 'id' }
                ],
                language: {
                    searchPlaceholder: 'Search...',
                    sSearch: ''
                }
            });
        });

        $("#simpanData").on("click", function (e) {
            e.preventDefault();

            let formData = new FormData($("#tambahData")[0]);

            $.ajax({
                url: "{{ route('kelas.store') }}",
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    if (response.status === true) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: response.message || 'Data berhasil disimpan.',
                            timer: 2000,
                            timerProgressBar: true,
                            showConfirmButton: false
                        });

                        $("#tambahData")[0].reset();
                        $('#modalTambahData').modal('hide');
                        if (typeof table !== 'undefined') {
                            table.ajax.reload();
                        }
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: response.message || 'Data tidak berhasil diproses.',
                            showConfirmButton: true
                        });
                    }
                },
                error: function (xhr) {
                    let message = 'Terjadi kesalahan saat menyimpan data.';
                    if (xhr.responseJSON?.errors) {
                        const errors = Object.values(xhr.responseJSON.errors).flat().join('\n');
                        message = errors;
                    } else if (xhr.responseJSON?.message) {
                        message = xhr.responseJSON.message;
                    }

                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: message,
                        showConfirmButton: true
                    });
                }
            });
        });


        </script>


@endsection