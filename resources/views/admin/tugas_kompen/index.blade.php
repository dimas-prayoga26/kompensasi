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
                            <input type="file" class="form-control" id="tambah_file_image" name="file_image" 
                                accept="image/*,.pdf,.doc,.docx,.xls,.xlsx">
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
                            <input type="file" class="form-control" id="edit_file_image" name="file_image" 
                                accept="image/*,.pdf,.doc,.docx,.xls,.xlsx">
                            <small class="text-muted">Kosongkan jika tidak ingin mengubah gambar.</small>
                        </div>

                        <!-- Preview Gambar -->
                        <img id="preview_edit_image" src="#" class="img-fluid my-2 d-none" style="max-height: 150px;">

                        <!-- Preview Dokumen (PDF, Word, Excel) -->
                        <div id="preview_edit_file" class="d-none my-2">
                            <!-- Konten akan dimuat secara dinamis berdasarkan jenis file -->
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
            // Menentukan role pengguna menggunakan Spatie
            const currentUserRole = "{{ auth()->user()->getRoleNames()->first() }}"; // Mengambil role pertama dari koleksi role yang dimiliki pengguna
            console.log(currentUserRole);

            let columnsConfig = [
                { data: null }, // No
                { data: 'nama_dosen' },
                { data: 'file_image' },
                { data: 'jumlah_mahasiswa' },
                { data: 'deskripsi_kompensasi' },
            ];

            if (currentUserRole != 'Mahasiswa') {
                columnsConfig.push({ data: 'id' });
            }

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
                            const filePath = full.file_path;
                            const fileExtension = filePath.split('.').pop().toLowerCase();
                            let filePreview = '';

                            if (['jpeg', 'png', 'jpg', 'webp'].includes(fileExtension)) {
                                const imageUrl = "{{ asset('storage') }}/" + filePath;
                                filePreview = `
                                    <a href="${imageUrl}" download>
                                        <img src="${imageUrl}" width="50" height="50" style="object-fit: cover; cursor: pointer;" />
                                    </a>
                                `;
                            } else if (['pdf'].includes(fileExtension)) {
                                const pdfUrl = "{{ asset('storage') }}/" + filePath;
                                filePreview = `<a href="${pdfUrl}" target="_blank" class="btn btn-sm btn-secondary">PDF</a>`;
                            } else if (['doc', 'docx'].includes(fileExtension)) {
                                const wordUrl = "{{ asset('storage') }}/" + filePath;
                                filePreview = `<a href="${wordUrl}" target="_blank" class="btn btn-sm btn-primary">Word</a>`;
                            } else if (['xls', 'xlsx'].includes(fileExtension)) {
                                const excelUrl = "{{ asset('storage') }}/" + filePath;
                                filePreview = `<a href="${excelUrl}" target="_blank" class="btn btn-sm btn-success">Excel</a>`;
                            } else {
                                filePreview = '-';
                            }

                            return filePreview;
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
                            if (currentUserRole !== 'Mahasiswa') {
                                return `
                                    <button type="button" class="btn btn-warning btn-sm" onclick="editData(${full.id})">
                                        <i class="fe fe-edit"></i> Edit
                                    </button>
                                    <button type="button" class="btn btn-danger btn-sm" onclick="hapusData(${full.id})">
                                        <i class="fe fe-trash"></i> Hapus
                                    </button>
                                `;
                            }
                            return '';
                        }
                    }
                ],
                columns: columnsConfig,
                language: {
                    searchPlaceholder: 'Search...',
                    sSearch: ''
                }
            });

            if (currentUserRole === 'Mahasiswa') {
                table.column(5).visible(false);
            }
        });




        function showImagePreview(url) {
            $("#previewImage").attr("src", url);
            $("#modalImagePreview").modal("show");
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

                        $('#edit_id_kompensasi').val(tugasKompensasi.id);
                        $('#edit_id_dosen').val(tugasKompensasi.dosen_id).trigger('change');
                        $('#edit_jumlah_mahasiswa').val(tugasKompensasi.jumlah_mahasiswa);
                        $('#edit_deskripsi').val(tugasKompensasi.deskripsi_kompensasi);

                        // Cek apakah ada file yang diupload
                        if (tugasKompensasi.file_path) {
                            const fileExtension = tugasKompensasi.file_path.split('.').pop().toLowerCase();
                            const fileUrl = `{{ asset('storage') }}/${tugasKompensasi.file_path}`;
                            
                            // Menyesuaikan tampilan berdasarkan jenis file
                            if (['jpeg', 'png', 'jpg', 'webp'].includes(fileExtension)) {
                                // Jika gambar, tampilkan gambar
                                $("#preview_edit_image").attr("src", fileUrl).removeClass("d-none");
                                $("#preview_edit_image").show();
                                $("#preview_edit_file").hide(); // Sembunyikan preview file lainnya
                            } else if (['pdf'].includes(fileExtension)) {
                                // Jika PDF, tampilkan tombol untuk membuka PDF
                                $("#preview_edit_image").hide();
                                $("#preview_edit_file").html(`<a href="${fileUrl}" target="_blank" class="btn btn-sm btn-secondary">Buka PDF</a>`).removeClass("d-none");
                            } else if (['doc', 'docx'].includes(fileExtension)) {
                                // Jika Word, tampilkan tombol untuk membuka Word
                                $("#preview_edit_image").hide();
                                $("#preview_edit_file").html(`<a href="${fileUrl}" target="_blank" class="btn btn-sm btn-primary">Buka Word</a>`).removeClass("d-none");
                            } else if (['xls', 'xlsx'].includes(fileExtension)) {
                                // Jika Excel, tampilkan tombol untuk membuka Excel
                                $("#preview_edit_image").hide();
                                $("#preview_edit_file").html(`<a href="${fileUrl}" target="_blank" class="btn btn-sm btn-success">Buka Excel</a>`).removeClass("d-none");
                            } else {
                                $("#preview_edit_image").hide();
                                $("#preview_edit_file").hide();
                            }
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