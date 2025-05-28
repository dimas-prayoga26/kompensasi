@extends('admin.layout.main')

@section('title', 'User')

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
    <span class="text-muted fw-light">Settings /</span> User
    </h4>

    <div class="row">
      <div class="col-lg-12 mb-4 order-0">
          <div class="card">
            <div class="row px-3 pt-3">
                <div class="col-md-3">
                    <label for="filter-role" class="form-label">Filter Role</label>
                    <select id="filter-role" class="form-select">
                        <option value="">Semua</option>
                        <option value="Mahasiswa">Mahasiswa</option>
                        <option value="Dosen" selected>Dosen</option>
                    </select>
                </div>
              </div>
              <div class="d-flex justify-content-between align-items-center p-3">
                    <h5 class="mb-0">Daftar Data User</h5>
                    <div>
                        <button type="button" class="btn btn-success me-2" data-bs-toggle="modal" data-bs-target="#modalImportData">
                            <i class="bi bi-upload"></i> Import Data
                        </button>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahData">
                            + Tambah Data
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
                                      <th>Nim</th>
                                      <th>Tahun Ajaran</th>
                                      <th>JK</th>
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
        <h5 class="modal-title" id="modalLabel">Tambah Data Dosen</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <form id="tambahData" enctype="multipart/form-data">
        <div class="modal-body">


          <div class="d-flex gap-4 mt-3">
            <div class="form-check">
              <input
                name="default-radio-1"
                class="form-check-input"
                type="radio"
                value="mahasiswa"
                id="defaultRadio1"
              />
              <label class="form-check-label" for="defaultRadio1">Mahasiswa</label>
            </div>

            <div class="form-check">
              <input
                name="default-radio-1"
                class="form-check-input"
                type="radio"
                value="dosen"
                id="defaultRadio2"
                checked
              />
              <label class="form-check-label" for="defaultRadio2">Dosen</label>
            </div>
          </div>


          <div class="mb-3 mt-3">
            <label for="nim" class="form-label" id="labelNim">NIP</label>
            <input type="text" class="form-control" id="nim" name="nim" placeholder="Masukkan NIP">
            <div id="nimAlert" class="form-text text-danger mt-1"></div>
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

      <div class="modal-header">
        <h6 class="modal-title" id="modalEditDataLabel">Edit Data</h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <form id="editData" enctype="multipart/form-data">
        <div class="modal-body">

        </div>

        <div class="modal-footer">
          <button type="submit" class="btn btn-primary" id="updateData">Simpan</button>
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

        $(document).on('change', 'input[name="default-radio-1"]', function () {
            const selectedValue = $(this).val();

            if (selectedValue === 'mahasiswa') {
                $('#modalLabel').text('Tambah Data Mahasiswa');
                $('#labelNim').text('NIM');
                $('#nim').attr('placeholder', 'Masukkan NIM');
            } else {
                $('#modalLabel').text('Tambah Data Dosen');
                $('#labelNim').text('NIP');
                $('#nim').attr('placeholder', 'Masukkan NIP');
            }
        });


        let table;
        let currentRole = '';

        $(document).ready(function () {
            currentRole = $('#filter-role').val();

            replaceTableHeader(currentRole);
            initDataTable(currentRole);

            $('#filter-role').on('change', function () {
                currentRole = $(this).val();

                if ($.fn.DataTable.isDataTable('#datatable')) {
                    $('#datatable').DataTable().clear().destroy();
                }

                replaceTableHeader(currentRole);
                initDataTable(currentRole);
            });
        });

        function replaceTableHeader(role) {
            const headers = {
                Mahasiswa: ['No.', 'Nama', 'NIM', 'Tahun Ajaran', 'Kelas', 'Prodi', 'Aksi'],
                Dosen: ['No.', 'Nama', 'NIP', 'Jabatan', 'JK', 'Bidang Keahlian', 'Aksi']
            };

            const selectedHeaders = headers[role || 'Mahasiswa'];
            let headerHtml = '<tr>';
            selectedHeaders.forEach(text => {
                headerHtml += `<th>${text}</th>`;
            });
            headerHtml += '</tr>';

            $('#datatable thead').html(headerHtml); // Replace thead
            $('#datatable tbody').html('');         // Optional: bersihkan tbody
        }

        function initDataTable(role = '') {
            const isDosen = role === 'Dosen';

            table = $('#datatable').DataTable({
                responsive: true,
                processing: true,
                serverSide: true,
                scrollX: true,
                ajax: {
                    url: "{{ route('user.datatable') }}?role=" + role,
                },
                columns: [
                    { data: null, orderable: false, searchable: false },
                    { data: "nama_lengkap" },
                    { data: isDosen ? "nip" : "nim" },
                    { data: "kolom4" },
                    { data: "kolom5" },
                    { data: "kolom6" },
                    { data: "id", orderable: false, searchable: false }
                ],
                columnDefs: [
                    {
                        targets: 0,
                        render: function (data, type, full, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        }
                    },
                    {
                        targets: 1,
                        render: function (data, type, full, meta) {
                            const detail = isDosen ? full.detail_dosen : full.detail_mahasiswa;
                            const first = detail?.first_name?.trim() ?? '';
                            const last = detail?.last_name?.trim() ?? '';
                            return (first || last) ? `${first} ${last}` : '<span class="badge bg-danger">Data belum lengkap</span>';
                        }
                    },
                    {
                        targets: 3,
                        render: function (data, type, full, meta) {
                            return isDosen
                                ? (full.detail_dosen?.jabatan ?? '<span class="badge bg-danger">Data belum lengkap</span>')
                                : (full.detail_mahasiswa?.tahun_masuk ?? '<span class="badge bg-danger">Data belum lengkap</span>');
                        }
                    },
                    {
                        targets: 4,
                        render: function (data, type, full, meta) {
                            const detail = isDosen ? full.detail_dosen : full.detail_mahasiswa;
                            return isDosen
                                ? (detail?.jenis_kelamin ?? '<span class="badge bg-danger">Data belum lengkap</span>')
                                : (detail?.kelas ?? '<span class="badge bg-danger">Belum diisi</span>');
                        }
                    },
                    {
                        targets: 5,
                        render: function (data, type, full, meta) {
                            return isDosen
                                ? (full.detail_dosen?.bidang_keahlian ?? '<span class="badge bg-danger">Data belum lengkap</span>')
                                : (full.detail_mahasiswa?.prodi?.nama ?? '<span class="badge bg-danger">Data belum lengkap</span>');
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
                language: {
                    searchPlaceholder: 'Search...',
                    sSearch: ''
                }
            });
        }

        $("#simpanData").on("click", function (e) {
            e.preventDefault();

            const selectedRole = $('input[name="default-radio-1"]:checked').val();
            const nimInput = $("#nim").val().trim();
            $("#nimAlert").text("");

            let valid = true;

            if (selectedRole === 'mahasiswa') {
                const nimRegex = /^[0-9]{1,7}$/;
                if (!nimRegex.test(nimInput)) {
                    $("#tambahData")[0].reset();
                    $('#modalTambahData').modal('hide');

                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'NIM harus berupa angka maksimal 7 digit.',
                        timer: 3000,
                        timerProgressBar: true,
                        showConfirmButton: false,
                    });
                    valid = false;
                }
            } else if (selectedRole === 'dosen') {
                const nipRegex = /^[0-9]{18}$/;
                if (!nipRegex.test(nimInput)) {
                    $("#tambahData")[0].reset();
                    $('#modalTambahData').modal('hide');

                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'NIP harus berupa angka 18 digit.',
                        timer: 3000,
                        timerProgressBar: true,
                        showConfirmButton: false,
                    });
                    valid = false;
                }
            }

            if (!valid) return;

            let formData = new FormData();
            formData.append("role", selectedRole);

            if (selectedRole === 'mahasiswa') {
                formData.append("nim", nimInput);
            } else {
                formData.append("nip", nimInput);
            }

            let fileUpload = $("#file_upload")[0]?.files[0];
            if (fileUpload) {
                formData.append("file_upload", fileUpload);
            }

            $.ajax({
                url: "{{ route('user.store') }}",
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
                            text: response.message || 'Data berhasil diproses.',
                            timer: 2000,
                            timerProgressBar: true,
                            showConfirmButton: false
                        });
                        $("#tambahData")[0].reset();
                        $('#modalTambahData').modal('hide');
                        table.ajax.reload();
                    } else {
                        $("#tambahData")[0].reset();
                        $('#modalTambahData').modal('hide');
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: response.message || 'Data tidak berhasil diproses.',
                            showConfirmButton: true,
                        });
                    }
                },
                error: function (xhr) {
                    const message = xhr.responseJSON?.message || 'Terjadi kesalahan saat menyimpan data.';
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: message,
                        showConfirmButton: true,
                    });
                }
            });
        });

        const urlEditUser = "{{ route('user.show', ':id') }}";

        function editData(id) {
            const url = urlEditUser.replace(':id', id);

            $.ajax({
                url: url,
                type: "GET",
                success: function(response) {
                    if (response.status === true) {
                        const user = response.data;

                        const $modalBody = $('#modalEditData .modal-body');
                        $modalBody.empty();

                        let modalTitle = 'Edit Data';

                        if (user.nim) {

                            modalTitle = 'Edit Data Mahasiswa';

                            $modalBody.append(`
                                <div class="mb-3">
                                    <label for="first_name" class="form-label">First Name</label>
                                    <input type="text" class="form-control" id="first_name" name="first_name" value="${user.detail_mahasiswa?.first_name ?? ''}">
                                </div>
                                <div class="mb-3">
                                    <label for="last_name" class="form-label">Last Name</label>
                                    <input type="text" class="form-control" id="last_name" name="last_name" value="${user.detail_mahasiswa?.last_name ?? ''}">
                                </div>
                                <div class="mb-3">
                                    <label for="kelas" class="form-label">Kelas</label>
                                    <input type="text" class="form-control" id="kelas" name="kelas" value="${user.detail_mahasiswa?.kelas ?? ''}">
                                </div>

                            `);

                        } else if (user.nip) {

                            modalTitle = 'Edit Data Dosen';

                            $modalBody.append(`
                                <div class="mb-3">
                                    <label for="first_name" class="form-label">First Name</label>
                                    <input type="text" class="form-control" id="first_name" name="first_name" value="${user.detail_dosen?.first_name ?? ''}">
                                </div>
                                <div class="mb-3">
                                    <label for="last_name" class="form-label">Last Name</label>
                                    <input type="text" class="form-control" id="last_name" name="last_name" value="${user.detail_dosen?.last_name ?? ''}">
                                </div>
                                <div class="mb-3">
                                    <label for="jenis_kelamin" class="form-label">Jenis Kelamin</label>
                                    <select class="form-select" id="jenis_kelamin" name="jenis_kelamin">
                                        <option value="Laki-laki" ${user.detail_dosen?.jenis_kelamin === 'Laki-laki' ? 'selected' : ''}>Laki-laki</option>
                                        <option value="Perempuan" ${user.detail_dosen?.jenis_kelamin === 'Perempuan' ? 'selected' : ''}>Perempuan</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="jabatan_fungsional" class="form-label">Jabatan Fungsional</label>
                                    <input type="text" class="form-control" id="jabatan_fungsional" name="jabatan_fungsional" value="${user.detail_dosen?.jabatan_fungsional ?? ''}">
                                </div>
                                <div class="mb-3">
                                    <label for="bidang_keahlian" class="form-label">Bidang Keahlian</label>
                                    <input type="text" class="form-control" id="bidang_keahlian" name="bidang_keahlian" value="${user.detail_dosen?.bidang_keahlian ?? ''}">
                                </div>
                            `);
                        } else {
                            $modalBody.append(`<p>Data tidak lengkap atau role tidak dikenali.</p>`);
                        }

                        $('#modalEditDataLabel').text(modalTitle);

                        $('#editData').data('id', id);

                        $('#modalEditData').modal('show');
                    } else {
                        alert('Data tidak ditemukan.');
                    }
                },
                error: function(xhr) {
                    toastr.error('Terjadi kesalahan: ' + xhr.responseText);
                }
            });
        }

        $("#updateData").on("click", function(e) {
            e.preventDefault();

            let id = $("#editData").data('id');
            let formData = new FormData($("#editData")[0]);

            let url = "{{ route('user.update', ':id') }}".replace(':id', id);

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


        const urlDeleteUser = "{{ route('user.destroy', ':id') }}";

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