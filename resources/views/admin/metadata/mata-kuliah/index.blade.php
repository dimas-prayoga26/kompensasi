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

    <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Dashboard</h4>

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
                                      <th>Kode</th>
                                      <th>Nama</th>
                                      <th>Sks</th>
                                      <th>Deskripsi</th>
                                      <th>Prodi</th>
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

</div>

<div class="modal fade" id="modalTambahData" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title" id="modalTambahMatakuliahLabel">Tambah Data Matakuliah</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <form id="tambahData" enctype="multipart/form-data">
        <div class="modal-body">

          <div class="mb-3">
            <label for="kode" class="form-label">Kode</label>
            <input type="text" class="form-control" id="kode" name="kode" placeholder="Contoh: BIO101" required>
          </div>

          <div class="mb-3">
            <label for="nama" class="form-label">Nama Matakuliah</label>
            <input type="text" class="form-control" id="nama" name="nama" placeholder="Nama matakuliah" required>
          </div>

          <div class="mb-3">
            <label for="sks" class="form-label">SKS</label>
            <input type="number" class="form-control" id="sks" name="sks" min="1" max="6" placeholder="Jumlah SKS" required>
          </div>

          <div class="mb-3">
            <label for="deskripsi" class="form-label">Deskripsi</label>
            <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3" placeholder="Deskripsi matakuliah (opsional)"></textarea>
          </div>

          <div class="mb-3">
            <label for="prodi_id" class="form-label">Program Studi</label>
            <select class="form-select" id="prodi_id" name="prodi_id" required>
              <option value="" disabled selected>-- Pilih Prodi --</option>
              <!-- Data prodi akan diisi lewat JS -->
            </select>
          </div>

          <div class="mb-3">
            <label for="semester" class="form-label">Semester yang Diajarkan</label>
            <select class="form-select" id="semester" name="semester" required>
                <!-- Diisi melalui JS -->
            </select>
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

<div class="modal fade" id="modalEditData" tabindex="-1" aria-labelledby="modalEditMatakuliahLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title" id="modalEditMatakuliahLabel">Edit Data Matakuliah</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <form id="editData" enctype="multipart/form-data">
        <div class="modal-body">

          <input type="hidden" id="edit_id" name="id">

          <div class="mb-3">
            <label for="edit_kode" class="form-label">Kode</label>
            <input type="text" class="form-control" id="edit_kode" name="kode" required>
          </div>

          <div class="mb-3">
            <label for="edit_nama" class="form-label">Nama Matakuliah</label>
            <input type="text" class="form-control" id="edit_nama" name="nama" required>
          </div>

          <div class="mb-3">
            <label for="edit_sks" class="form-label">SKS</label>
            <input type="number" class="form-control" id="edit_sks" name="sks" min="1" max="6" required>
          </div>

          <div class="mb-3">
            <label for="edit_deskripsi" class="form-label">Deskripsi</label>
            <textarea class="form-control" id="edit_deskripsi" name="deskripsi" rows="3"></textarea>
          </div>

          <div class="mb-3">
            <label for="edit_prodi_id" class="form-label">Program Studi</label>
            <select class="form-select" id="edit_prodi_id" name="prodi_id" required>
              <option value="" disabled selected>-- Pilih Prodi --</option>
              <!-- Diisi lewat JS -->
            </select>
          </div>

          <div class="mb-3">
            <label for="edit_semester" class="form-label">Semester yang Diajarkan</label>
            <select class="form-select" id="edit_semester" name="semester" required>
              <!-- Diisi lewat JS -->
            </select>
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
        
            $.ajax({
                url: "{{ route('mataKuliah.getProdi') }}",
                type: "GET",
                success: function (response) {
                    const listProdi = response.data;

                    $("#prodi_id").empty().append('<option value="" disabled selected>-- Pilih Prodi --</option>');

                    listProdi.forEach(function (prodi) {
                        let maxSemester = 6;
                        if (prodi.nama.toLowerCase().includes("rekayasa perangkat lunak")) {
                            maxSemester = 8;
                        }

                        $("#prodi_id").append(`<option value="${prodi.id}" data-max-semester="${maxSemester}">${prodi.nama}</option>`);
                    });
                }
            });

            $("#prodi_id").on("change", function () {
                const selectedOption = $(this).find(":selected");
                const maxSemester = parseInt(selectedOption.data("max-semester"));

                $("#semester").empty().append('<option value="" disabled selected>-- Pilih Semester --</option>');
                for (let i = 1; i <= maxSemester; i++) {
                    $("#semester").append(`<option value="${i}">Semester ${i}</option>`);
                }
            });

            table = $("#datatable").DataTable({
                responsive: true,
                processing: true,
                serverSide: true,
                autoWidth: false,
                ajax: {
                    url: "{{ route('mataKuliah.datatable') }}",
                },
                columnDefs: [
                    {
                        targets: 0,
                        render: function (data, type, full, meta) {
                            return meta.row + 1;
                        }
                    },
                    {
                        targets: 5,
                        render: function (data, type, full, meta) {
                            return full.prodi.nama 
                                ?? '<span class="badge bg-danger">Data belum lengkap</span>';
                        }
                    },
                    {
                        targets: 6,
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
                    { data: 'kode' },
                    { data: 'nama' },
                    { data: 'sks' },
                    { data: 'deskripsi' },
                    { data: null },
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
                url: "{{ route('mataKuliah.store') }}",
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

        const urlEditUser = "{{ route('mataKuliah.show', ':id') }}";

        function editData(id) {
            const url = urlEditUser.replace(':id', id);

            $.ajax({
                url: url,
                type: "GET",
                success: function(response) {
                    if (response.status === true) {
                        const mataKuliah = response.data;

                        // Isi form dasar
                        $('#edit_kode').val(mataKuliah.kode);
                        $('#edit_nama').val(mataKuliah.nama);
                        $('#edit_sks').val(mataKuliah.sks);
                        $('#edit_deskripsi').val(mataKuliah.deskripsi);

                        // Ambil prodi dari server
                        $.ajax({
                            url: "{{ route('mataKuliah.getProdi') }}",
                            type: "GET",
                            success: function(res) {
                                const prodiList = res.data;
                                const selectProdi = $('#edit_prodi_id');
                                const semesterSelect = $('#edit_semester');

                                selectProdi.empty().append('<option disabled selected>-- Pilih Prodi --</option>');
                                semesterSelect.empty();

                                // Cari jumlah semester dari prodi yang sesuai dengan mataKuliah.prodi_id
                                let jumlahSemesterAktif = 6; // default

                                prodiList.forEach(p => {
                                    let maxSemester = 6;
                                    if (p.nama.toLowerCase().includes("rekayasa perangkat lunak")) {
                                        maxSemester = 8;
                                    }

                                    const selected = (p.id === mataKuliah.prodi_id) ? 'selected' : '';
                                    selectProdi.append(`<option value="${p.id}" data-max-semester="${maxSemester}" ${selected}>${p.nama}</option>`);

                                    if (p.id === mataKuliah.prodi_id) {
                                        jumlahSemesterAktif = maxSemester;
                                    }
                                });

                                // Set value setelah opsi terisi
                                selectProdi.val(mataKuliah.prodi_id);

                                // Isi semester sesuai jumlah semester prodi aktif
                                for (let i = 1; i <= jumlahSemesterAktif; i++) {
                                    semesterSelect.append(`<option value="${i}">${i}</option>`);
                                }

                                // Ambil dan pilih semester dari data
                                const selectedSemester = mataKuliah.matakuliah_semesters.length > 0
                                    ? mataKuliah.matakuliah_semesters[0].no_semester
                                    : null;

                                if (selectedSemester) {
                                    semesterSelect.val(selectedSemester);
                                }
                            }
                        });

                        $('#editData').data('id', id);
                        $('#modalEditData').modal('show');
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: 'Data tidak ditemukan.'
                        });
                    }
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Terjadi Kesalahan',
                        text: xhr.responseJSON?.message || 'Gagal memuat data.'
                    });
                }
            });
        }

        $('#edit_prodi_id').on('change', function () {
            const selectedOption = $(this).find(':selected');
            const maxSemester = parseInt(selectedOption.data('max-semester'));
            const semesterSelect = $('#edit_semester');

            semesterSelect.empty().append('<option disabled selected>-- Pilih Semester --</option>');
            for (let i = 1; i <= maxSemester; i++) {
                semesterSelect.append(`<option value="${i}">${i}</option>`);
            }
        });

        $("#updateData").on("click", function(e) {
            e.preventDefault();

            let id = $("#editData").data('id');
            let formData = new FormData($("#editData")[0]);

            let url = "{{ route('mataKuliah.update', ':id') }}".replace(':id', id);

            formData.append('_method', 'PUT');

            $.ajax({
                url: url,
                type: "POST",
                data: formData,
                contentType: false,
                processData: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.status === true) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: response.message,
                            timer: 1500,
                            showConfirmButton: false,
                        });

                        $("#editData")[0].reset();
                        $('#modalEditData').modal('hide');
                        table.ajax.reload();
                    } else {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Peringatan',
                            text: response.message || 'Update gagal.',
                        });
                    }
                },
                error: function(xhr) {
                    let message = xhr.responseJSON?.message || xhr.responseText || 'Terjadi kesalahan saat mengupdate data.';
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: message,
                    });
                }
            });
        });

        const urlDeleteUser = "{{ route('mataKuliah.destroy', ':id') }}";

        function hapusData(id) {

            const url = urlDeleteUser.replace(':id', id);

            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Data yang dihapus tidak dapat dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: url,
                        type: "DELETE",
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            if (response.status === true) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil',
                                    text: response.message,
                                    timer: 1500,
                                    showConfirmButton: false,
                                });
                                table.ajax.reload();
                            } else {
                                Swal.fire({
                                    icon: 'warning',
                                    title: 'Gagal',
                                    text: response.message || 'Data tidak ditemukan.',
                                    showConfirmButton: true,
                                });
                            }
                        },
                        error: function(xhr) {
                            const message = xhr.responseJSON?.message || xhr.responseText || 'Terjadi Kesalahan';
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: message,
                                showConfirmButton: true,
                            });
                        }
                    });
                }
            });
        }



        </script>


@endsection