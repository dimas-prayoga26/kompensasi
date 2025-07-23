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
            <div class="d-flex justify-content-between align-items-center p-3 flex-wrap gap-2">
                <h5 class="mb-0">Daftar Data Tugas Kompensasi</h5>

                <div class="d-flex gap-2">
                    @role('superAdmin|Dosen')
                        <a href="{{ asset('template_kompen/BUKTI_KOMPENSASI.docx') }}" class="btn btn-outline-secondary" download>
                            <i class="bx bx-download"></i> Download Template
                        </a>

                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahData">
                            + Tambah Data
                        </button>

                    @endrole
                </div>
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
                                      <th>Jumlah menit kompensasi</th>
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
                                                {{ $dosen->detailDosen->first_name }} {{ $dosen->detailDosen->last_name }} - {{ $dosen->nip }}
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
                            <label for="tambah_jumlah_menit" class="form-label">Jumlah Menit Kompensasi</label>
                            <input type="number" class="form-control" id="tambah_jumlah_menit" name="jumlah_menit_kompensasi" required>
                        </div>

                        <div class="mb-3">
                            <label for="tambah_deskripsi" class="form-label">Deskripsi Tugas Kompensasi</label>
                            <textarea class="form-control" id="tambah_deskripsi" name="deskripsi_kompensasi" rows="3" required></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="tambah_file_image" class="form-label">Upload File Pendukung (Opsional)</label>
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
                                                {{ $dosen->detailDosen->first_name }} {{ $dosen->detailDosen->last_name }} - {{ $dosen->nip }}
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
                            <label for="edit_jumlah_menit" class="form-label">Jumlah Menit Kompensasi</label>
                            <input type="number" class="form-control" id="edit_jumlah_menit" name="jumlah_menit_kompensasi" required>
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

    <!-- Modal Upload Bukti -->
    <div class="modal fade" id="modalUploadBukti" tabindex="-1" aria-labelledby="modalUploadBuktiLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="formUploadBukti" enctype="multipart/form-data">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalUploadBuktiLabel">Upload Bukti Tugas Kompensasi</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="id" id="uploadBuktiId">

                        <div class="mb-3">
                            <label for="file_bukti" class="form-label">Pilih File Bukti</label>
                            <input type="file" class="form-control" id="file_bukti" name="file_bukti" 
                                accept="image/*,.pdf,.doc,.docx,.xls,.xlsx" required>
                        </div>

                        <div class="mb-3">
                            <label for="keterangan" class="form-label">Keterangan (Opsional)</label>
                            <textarea name="keterangan" id="keterangan" class="form-control" rows="3"
                                    placeholder="Tulis keterangan singkat tentang file yang diunggah..."></textarea>
                        </div>

                        <!-- Preview Gambar -->
                        <img id="preview_upload_image" src="#" class="img-fluid my-2 d-none" style="max-height: 150px;">

                        <!-- Preview Dokumen (PDF, Word, Excel) -->
                        <div id="preview_upload_file" class="d-none my-2">
                            <!-- Preview file akan dimunculkan via JS -->
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Upload</button>
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
    const currentUserID = {{ auth()->user()->id }};
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
            const currentUserRole = "{{ auth()->user()->getRoleNames()->first() }}";
            console.log(currentUserRole);

            let columnsConfig = [
                { data: null }, // No
                { data: 'nama_dosen' },
                { data: 'file_image' },
                { data: 'jumlah_mahasiswa' },
                { data: 'jumlah_menit_kompensasi' },
                { data: 'deskripsi_kompensasi' },
            ];

            if (currentUserRole != 'Mahasiswa') {
                columnsConfig.push({ data: 'id' });
            }

            table = $("#datatable").DataTable({
                responsive: false,
                processing: true,
                serverSide: true,
                autoWidth: false,
                scrollX: true,

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
                            return full.jumlah_menit_kompensasi ? full.jumlah_menit_kompensasi + ' menit' : '-';
                        }
                    },
                    {
                        targets: 5,
                        render: function (data, type, full, meta) {
                            return full.deskripsi_kompensasi || '-';
                        }
                    },
                    {
                        targets: 6,
                        render: function (data, type, full, meta) {
                            const currentUserId = parseInt(currentUserID);
                            const userSudahTerdaftar = full.penawaran_users?.some(pu => parseInt(pu.user_id) === currentUserId);

                            if (currentUserRole === 'Mahasiswa') {
                                let html = '';

                                if (!userSudahTerdaftar) {
                                    html += `
                                        <button type="button" class="btn btn-success btn-sm" onclick="pilihData(${full.id})">
                                            <i class="fe fe-check"></i> Pilih
                                        </button>
                                    `;
                                }

                                if (userSudahTerdaftar) {
                                    html += `
                                        <button type="button" class="btn btn-primary btn-sm" onclick="downloadBukti(${full.id})">
                                            <i class="fe fe-download"></i> Download Bukti
                                        </button>
                                    `;
                                }

                                return html;
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
                columns: columnsConfig,
                language: {
                    searchPlaceholder: 'Search...',
                    sSearch: ''
                }
            });
        });

        function downloadBukti(id) {
            const url = "{{ route('tugas-kompensasi.download.bukti', ['id' => ':id']) }}".replace(':id', id);

            $.ajax({
                url: url,
                method: 'GET',
                success: function(response) {
                    if (response.status && response.file_url) {
                        const link = document.createElement('a');
                        link.href = response.file_url;
                        link.setAttribute('download', ''); // Memaksa browser untuk download
                        document.body.appendChild(link);
                        link.click();
                        document.body.removeChild(link);
                    } else {
                        Swal.fire('Gagal', response.message || 'File tidak ditemukan.', 'error');
                    }
                },
                error: function(xhr) {
                    Swal.fire('Error', xhr.responseJSON?.message || 'Terjadi kesalahan saat mengambil bukti.', 'error');
                }
            });
        }

        function showImagePreview(url) {
            $("#previewImage").attr("src", url);
            $("#modalImagePreview").modal("show");
        }

        function detailData(id) {
            $('#modalDetailData').modal('show');

            if ($.fn.DataTable.isDataTable('#detailDatatable')) {
                $('#detailDatatable').DataTable().destroy();
            }

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
                        render: function (data, type, row, full) {
                            console.log(data);
                            
                            return `
                                <div class="d-flex gap-1">
                                    <button class="btn btn-danger btn-sm" onclick="hapusMahasiswa(${data})">
                                        <i class="fe fe-trash"></i> Hapus
                                    </button>
                                    <button class="btn btn-success btn-sm" onclick="uploadBukti(${data})">
                                        <i class="fe fe-upload"></i> Upload Bukti
                                    </button>
                                </div>
                            `;
                        }
                    }
                ]
            });
        }


        function uploadBukti(id) {
            $('#uploadBuktiId').val(id);

            // Reset form
            $('#file_bukti').val('');
            $('#keterangan').val('');
            $('#preview_upload_image').addClass('d-none').attr('src', '#');
            $('#preview_upload_file').addClass('d-none').html('');

            $.ajax({
                url: `/portal/tugas-kompensasi/${id}/get-upload`,
                type: 'GET',
                success: function (res) {
                    if (res.success) {
                        const fileUrl = res.data.file_url;
                        const fileExt = fileUrl.split('.').pop().toLowerCase();
                        const keterangan = res.data.keterangan || '';

                        $('#keterangan').val(keterangan);

                        if (['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(fileExt)) {
                            $('#preview_upload_image').attr('src', fileUrl).removeClass('d-none');
                            $('#preview_upload_file').addClass('d-none').html('');
                        } else {
                            $('#preview_upload_file').removeClass('d-none').html(
                                `<a href="${fileUrl}" target="_blank" class="text-primary">Lihat dokumen sebelumnya</a>`
                            );
                            $('#preview_upload_image').addClass('d-none').attr('src', '#');
                        }
                    }
                },
                error: function () {
                    console.warn('Tidak ada data upload sebelumnya atau gagal mengambil data.');
                }
            });

            $('#modalUploadBukti').modal('show');
        }


        $('#file_bukti').on('change', function() {
            const file = this.files[0];
            const imgPreview = $('#preview_upload_image');
            const filePreview = $('#preview_upload_file');

            if (!file) return;

            const fileType = file.type;
            const reader = new FileReader();

            if (fileType.startsWith('image/')) {
                reader.onload = function(e) {
                    imgPreview.attr('src', e.target.result).removeClass('d-none');
                    filePreview.addClass('d-none').html('');
                };
                reader.readAsDataURL(file);
            } else {
                imgPreview.addClass('d-none');
                filePreview.removeClass('d-none').html(`<p class="text-muted">${file.name}</p>`);
            }
        });

        $('#formUploadBukti').on('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const modalEl = document.getElementById('modalUploadBukti');
            const modalInstance = bootstrap.Modal.getInstance(modalEl);

            // Tutup modal Upload Bukti
            modalInstance.hide();

            // Jalankan setelah modal benar-benar tertutup
            $(modalEl).one('hidden.bs.modal', function () {
                // Tutup semua modal aktif untuk mencegah tumpang tindih
                $('.modal.show').modal('hide');

                // Tampilkan loading
                Swal.fire({
                    title: 'Mengunggah...',
                    text: 'Mohon tunggu sebentar.',
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });

                // Kirim data via AJAX
                $.ajax({
                    url: "{{ route('tugas-kompensasi.upload.bukti') }}",
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(res) {
                        $('#preview_upload_image').addClass('d-none').attr('src', '#');
                        $('#preview_upload_file').addClass('d-none').html('');

                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: res.message || 'Bukti berhasil diunggah.',
                            timer: 2000,
                            showConfirmButton: false
                        });

                        $("#formUploadBukti")[0].reset();
                        $('#detailDatatable').DataTable().ajax.reload();
                    },
                    error: function(xhr) {
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
        });


        function hapusMahasiswa(id) {
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
                        $('#modalDetailData').modal('show');
                    }
                });
            }, 300);
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
                    const modalEl = document.getElementById('modalTambahData');
                    const modal = bootstrap.Modal.getInstance(modalEl);

                    modal.hide();

                    modalEl.addEventListener('hidden.bs.modal', function handler() {
                        modalEl.removeEventListener('hidden.bs.modal', handler);

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
                        $('#edit_jumlah_menit').val(tugasKompensasi.jumlah_menit_kompensasi);
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