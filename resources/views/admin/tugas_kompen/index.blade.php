@extends('admin.layout.main')

@section('title', 'Tugas Kompensasi')

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
    <span class="text-muted fw-light">Tugas kompensasi </span>
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
                  <h5 class="mb-0">Daftar Data Tugas Kompensasi</h5>
                    @role('superAdmin|Dosen')
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahData">
                        + Tambah Data
                        </button>
                    @endrole

              </div>
              <div class="d-flex align-items-end row">
                  <div class="col-sm-12">
                      <div class="card-body">
                          <table id="datatable" class="table table-bordered text-nowrap w-100">
                              <thead>
                                  <tr>
                                      <th>No.</th>
                                      <th>Nama Dosen</th>
                                      <th>Tugas Kompen</th>
                                      <th>Jumlah mahasiswa dibutuhkan</th>
                                      <th>Deskripsi Kompen</th>
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
                        <h5 class="modal-title" id="modalTambahDataLabel">Tambah Data Tugas Kompensasi</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>
                    <div class="modal-body">
                        @if(Auth::user()->hasRole('Dosen'))
                            <input type="hidden" name="id_dosen" value="{{ Auth::user()->id }}">
                        @else
                            <div class="mb-3">
                                <label for="tambah_id_dosen" class="form-label">Dosen Penanggung Jawab</label>
                                <select class="form-select" id="tambah_id_dosen" name="id_dosen" required>
                                    <option selected disabled>Pilih Dosen</option>
                                    @foreach ($dosens as $dosen)
                                        @if ($dosen->detailDosen)
                                            <option value="{{ $dosen->id }}">
                                                {{ $dosen->detailDosen->first_name }} {{ $dosen->detailDosen->last_name }}
                                            </option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        @endif

                        <div class="mb-3">
                            <label for="tambah_jumlah_mahasiswa" class="form-label">Jumlah Mahasiswa</label>
                            <input type="number" class="form-control" id="tambah_jumlah_mahasiswa" name="jumlah_mahasiswa" required>
                        </div>

                        <div class="mb-3">
                            <label for="tambah_deskripsi" class="form-label">Deskripsi Tugas Kompensasi</label>
                            <textarea class="form-control" id="tambah_deskripsi" name="deskripsi_kompensasi" rows="3" required></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="tambah_file_image" class="form-label">Upload Gambar Pendukung (Opsional)</label>
                            <input type="file" class="form-control" id="tambah_file_image" name="file_image" accept="image/*">
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
                        <h5 class="modal-title" id="modalEditDataLabel">Edit Data Tugas Kompensasi</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="edit_id_kompensasi" name="id">

                        @if(Auth::user()->hasRole('Dosen'))
                            <input type="hidden" name="id_dosen" id="edit_id_dosen" value="{{ Auth::user()->id }}">
                        @else
                            <div class="mb-3">
                                <label for="edit_id_dosen" class="form-label">Dosen Penanggung Jawab</label>
                                <select class="form-select" id="edit_id_dosen" name="id_dosen" required>
                                    <option selected disabled>Pilih Dosen</option>
                                    @foreach ($dosens as $dosen)
                                        @if ($dosen->detailDosen)
                                            <option value="{{ $dosen->id }}">
                                                {{ $dosen->detailDosen->first_name }} {{ $dosen->detailDosen->last_name }}
                                            </option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        @endif

                        <div class="mb-3">
                            <label for="edit_jumlah_mahasiswa" class="form-label">Jumlah Mahasiswa</label>
                            <input type="number" class="form-control" id="edit_jumlah_mahasiswa" name="jumlah_mahasiswa" required>
                        </div>

                        <div class="mb-3">
                            <label for="edit_deskripsi" class="form-label">Deskripsi Tugas Kompensasi</label>
                            <textarea class="form-control" id="edit_deskripsi" name="deskripsi_kompensasi" rows="3" required></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="edit_file_image" class="form-label">Gambar Pendukung (Opsional)</label>
                            <input type="file" class="form-control" id="edit_file_image" name="file_image" accept="image/*">
                            <small class="text-muted">Kosongkan jika tidak ingin mengubah gambar.</small>
                        </div>

                        <img id="preview_edit_image" src="#" class="img-fluid my-2 d-none" style="max-height: 150px;">
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary" id="updateData">Simpan Perubahan</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalDetailData" tabindex="-1" aria-labelledby="modalDetailDataLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl"> <!-- Besar karena isinya tabel -->
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detail Mahasiswa Kompensasi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <table class="table table-bordered" id="detailDatatable" width="100%">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>NIM</th>
                                <th>Nama Mahasiswa</th>
                                <th>Kelas</th>
                                <th>Aksi</th> 
                            </tr>
                        </thead>
                        <tbody></tbody> <!-- Akan diisi oleh DataTables -->
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Gambar -->
    <div class="modal fade" id="modalImagePreview" tabindex="-1" aria-labelledby="modalImagePreviewLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
            <div class="modal-body text-center">
                <img id="previewImage" src="" class="img-fluid" alt="Preview">
            </div>
            </div>
        </div>
    </div>



</div>

@endsection

@section('js')

<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script>
    const currentUserRole = "{{ Auth::user()->getRoleNames()->first() }}";
</script>



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
                    url: "{{ route('tugas-kompensasi.datatable') }}",
                },
                columnDefs: [
                    {
                        targets: 0,
                        render: function (data, type, full, meta) {
                            return meta.row + 1;
                        }
                    },
                    {
                        targets: 2,
                        render: function (data, type, full, meta) {
                            const imageUrl = `{{ asset('storage') }}/${full.file_path}`;
                            return `<img src="${imageUrl}" width="50" height="50" style="object-fit: cover; cursor: pointer;" onclick="showImagePreview('${imageUrl}')">`;
                        }
                    },
                    {
                        targets: 3,
                        render: function (data, type, full, meta) {
                            return full.jumlah_mahasiswa || '-';
                        }
                    },
                    {
                        targets: 4,
                        render: function (data, type, full, meta) {
                            return full.deskripsi_kompensasi || '-';
                        }
                    },
                    {
                        targets: 5,
                        render: function (data, type, full, meta) {
                            if (currentUserRole === 'Mahasiswa') {
                                return `
                                    <button type="button" class="btn btn-success btn-sm" onclick="pilihData(${full.id})">
                                        <i class="fe fe-check"></i> Pilih
                                    </button>
                                `;
                            } else {
                                return `
                                    <button type="button" class="btn btn-info btn-sm" onclick="detailData(${full.id})">
                                        <i class="fe fe-eye"></i> Detail
                                    </button>
                                    <button type="button" class="btn btn-warning btn-sm" onclick="editData(${full.id})">
                                        <i class="fe fe-edit"></i> Edit
                                    </button>
                                    <button type="button" class="btn btn-danger btn-sm" onclick="hapusData(${full.id})">
                                        <i class="fe fe-trash"></i> Hapus
                                    </button>
                                `;
                            }
                        }
                    }

                ],
                columns: [
                    { data: null }, // No
                    { data: 'nama_dosen' },
                    { data: 'file_image' },
                    { data: 'jumlah_mahasiswa' },
                    { data: 'deskripsi_kompensasi' },
                    { data: 'id' }
                ],
                language: {
                    searchPlaceholder: 'Search...',
                    sSearch: ''
                }
            });
        });

        function showImagePreview(url) {
            $("#previewImage").attr("src", url);
            $("#modalImagePreview").modal("show");
        }

       function detailData(id) {
            $('#modalDetailData').modal('show');

            // Jika datatable sudah pernah dibuat, hancurkan dulu agar tidak duplikat
            if ($.fn.DataTable.isDataTable('#detailDatatable')) {
                $('#detailDatatable').DataTable().destroy();
            }

            // Buat datatable detail
            $('#detailDatatable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: `/portal/tugas-kompensasi/${id}/detail`,
                    type: 'GET'
                },
                columns: [
                    { data: null, name: 'no', render: (data, type, row, meta) => meta.row + 1 },
                    { data: 'nim', name: 'nim' },
                    { data: 'nama_mahasiswa', name: 'nama_mahasiswa' },
                    { data: 'kelas', name: 'kelas' },
                    {
                        data: 'id',
                        name: 'id',
                        render: function (data, type, row) {
                            return `
                                <button class="btn btn-danger btn-sm" onclick="hapusMahasiswa(${data})">
                                    <i class="fe fe-trash"></i> Hapus
                                </button>
                            `;
                        }
                    }
                ]
            });
        }

        function hapusMahasiswa(id) {
            // Sembunyikan modal agar Swal muncul di atas
            $('#modalDetailData').modal('hide');

            setTimeout(() => {
                Swal.fire({
                    title: 'Yakin ingin menghapus?',
                    text: 'Data mahasiswa akan dihapus dari program kompensasi ini.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/portal/tugas-kompensasi/detail/${id}`,
                            type: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function (response) {
                                if (response.status) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Berhasil',
                                        text: response.message,
                                        timer: 2000,
                                        showConfirmButton: false
                                    });

                                    $('#detailDatatable').DataTable().ajax.reload(null, false);
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Gagal',
                                        text: response.message
                                    });

                                    $('#modalDetailData').modal('show');
                                }
                            },
                            error: function (xhr) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal',
                                    text: xhr.responseJSON?.message || 'Terjadi kesalahan saat menghapus data.'
                                });

                                $('#modalDetailData').modal('show');
                            }
                        });
                    } else {
                        // Jika batal, tampilkan lagi modal detail
                        $('#modalDetailData').modal('show');
                    }
                });
            }, 300); // Tunggu 300ms agar modal Bootstrap sempat tertutup dulu
        }

        function pilihData(kompensasiId) {
            Swal.fire({
                title: 'Yakin ingin mendaftar?',
                text: 'Anda akan bergabung dalam tugas kompensasi ini.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Pilih',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '/portal/tugas-kompensasi/pilih',
                        type: 'POST',
                        data: {
                            kompensasi_id: kompensasiId
                        },
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function (response) {
                            if (response.status) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil',
                                    text: response.message,
                                    timer: 2000,
                                    showConfirmButton: false
                                });

                                // Reload datatable jika perlu
                                $('#datatable').DataTable().ajax.reload();
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal',
                                    text: response.message
                                });
                            }
                        },
                        error: function (xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: xhr.responseJSON?.message || 'Terjadi kesalahan saat memilih data.'
                            });
                        }
                    });
                }
            });
        }


        $("#simpanData").on("click", function (e) {
            e.preventDefault();

            let formData = new FormData($("#tambahData")[0]);

            $.ajax({
                url: "{{ route('tugas-kompensasi.store') }}",
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    if (response.status === true) {
                        const modalEl = document.getElementById('modalTambahData');
                        const modal = bootstrap.Modal.getInstance(modalEl);

                        modal.hide();

                        modalEl.addEventListener('hidden.bs.modal', function handler() {
                            modalEl.removeEventListener('hidden.bs.modal', handler);

                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: response.message || 'Data berhasil disimpan.',
                                timer: 2000,
                                timerProgressBar: true,
                                showConfirmButton: false
                            });

                            $("#tambahData")[0].reset();
                            $('#datatable').DataTable().ajax.reload();
                        });
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

        const urlEditTugasKompensasi = "{{ route('tugas-kompensasi.show', ':id') }}";

        function editData(id) {
            const url = urlEditTugasKompensasi.replace(':id', id);

            $.ajax({
                url: url,
                type: "GET",
                success: function(response) {
                    if (response.status === true) {
                        const tugasKompensasi = response.data;
                        // console.log(tugasKompensasi.id);
                        
                        $('#edit_id_kompensasi').val(tugasKompensasi.id);
                        $('#edit_id_dosen').val(tugasKompensasi.dosen_id).trigger('change');
                        $('#edit_jumlah_mahasiswa').val(tugasKompensasi.jumlah_mahasiswa);
                        $('#edit_deskripsi').val(tugasKompensasi.deskripsi_kompensasi);

                        if (tugasKompensasi.file_path) {
                            const imageUrl = `{{ asset('storage') }}/${tugasKompensasi.file_path}`;
                            $("#preview_edit_image").attr("src", imageUrl).removeClass("d-none");
                        }


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


        $("#updateData").on("click", function(e) {
            e.preventDefault();

            let id = $("#edit_id_kompensasi").val();
            console.log(id);
            
            let formData = new FormData($("#editData")[0]);

            let url = "{{ route('tugas-kompensasi.update', ':id') }}".replace(':id', id);

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
                        const modalEl = document.getElementById('modalEditData');
                        const modal = bootstrap.Modal.getInstance(modalEl);

                        modal.hide();

                        modalEl.addEventListener('hidden.bs.modal', function handler() {
                            modalEl.removeEventListener('hidden.bs.modal', handler);

                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: response.message || 'Data berhasil disimpan.',
                                timer: 2000,
                                timerProgressBar: true,
                                showConfirmButton: false
                            });

                            $("#editData")[0].reset();
                            $('#datatable').DataTable().ajax.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: response.message || 'Data tidak berhasil diproses.',
                            showConfirmButton: true
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

        const urlDeleteClass = "{{ route('tugas-kompensasi.destroy', ':id') }}";

        function hapusData(id) {

            const url = urlDeleteClass.replace(':id', id);

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
                                $('#datatable').DataTable().ajax.reload();
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