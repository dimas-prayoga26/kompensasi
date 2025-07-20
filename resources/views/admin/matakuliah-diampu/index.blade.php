@extends('admin.layout.main')

@section('title', 'Kelas')

@section('css')

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<style>
    div.dataTables_filter {
        margin-bottom: 2rem;
    }

    
    .select2-container--open {
        z-index: 9999 !important; 
    }

    
    .select2-container {
        width: 100% !important;
    }

    .modal .select2-container {
        width: 100% !important;  
        margin-bottom: 10px; 
    }

    .select2-container .select2-selection--single {
        height: 38px;  
        padding: 6px;
        font-size: 14px;  
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 36px;
        position: absolute;
        top: 1px;
        right: 1px;
        width: 32px;
    }

    .swal-custom-z {
        z-index: 2000 !important;
    }

</style>


@section('content')

<div class="container-xxl flex-grow-1 container-p-y">

    <h4 class="fw-bold py-3 mb-4">
    <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Daftar matakuliah yang diampu</span></h4>
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
                <h5 class="mb-0">Daftar matakuliah yang diampu</h5>

                <div class="d-flex align-items-center">
                    <!-- Tampilkan filter hanya untuk superAdmin -->
                    @if(auth()->user()->hasRole('superAdmin'))
                        <select class="form-select w-auto me-2" aria-label="Pilih Dosen" id="dosen-filter">
                            <option selected>Pilih Dosen</option>
                            @foreach($dosen as $d)
                                <option value="{{ $d->id }}">
                                    @if($d->detailDosen)
                                        {{ $d->detailDosen->first_name }} {{ $d->detailDosen->last_name }} - {{ $d->nip }}
                                    @else
                                        {{ $d->nip }} - Data Tidak Tersedia
                                    @endif
                                </option>
                            @endforeach
                        </select>
                    @endif

                    <!-- Tombol di sebelah kanan -->
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
                                      <th>Matakuliah</th>
                                      <th>Kelas</th>
                                      <th>Semester</th>
                                      <th>Aksi Akademik</th>
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

                        @if(Auth::user()->hasRole('Dosen'))
                            <input type="hidden" name="dosen_id" value="{{ Auth::user()->id }}">
                        @else
                            <div class="mb-3">
                                <label for="dosen_id" class="form-label">Dosen</label>
                                <select class="select2 form-select" id="dosen_id" name="dosen_id" required>
                                    <option value="" disabled selected>-- Pilih Dosen --</option>

                                </select>
                            </div>
                        @endif
                        
                        <div class="mb-3">
                            <label for="matakuliah_id" class="form-label">Matakuliah</label>
                            <select class="select2 form-select" id="matakuliah_id" name="matakuliah_id" required>
                                <option value="" disabled selected>-- Pilih Matakuliah --</option>

                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="kelas_id" class="form-label">Kelas</label>
                            <select class="select2 form-select" id="kelas_id" name="kelas_id" required>
                                <option value="" disabled selected>-- Pilih Kelas --</option>

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

                        @if(Auth::user()->hasRole('Dosen'))
                            <input type="hidden" name="dosen_id" value="{{ Auth::user()->id }}">
                        @else
                            <div class="mb-3">
                                <label for="edit_dosen_id" class="form-label">Dosen</label>
                                <select class="form-select" id="edit_dosen_id" name="dosen_id" required>
                                    <option></option>
                                </select>
                            </div>
                         @endif


                        <div class="mb-3">
                            <label for="edit_matakuliah_id" class="form-label">Matakuliah</label>
                            <select class="form-select" id="edit_matakuliah_id" name="matakuliah_id" required>
                                <option></option>
                            </select>
                        </div>


                        <div class="mb-3">
                            <label for="edit_kelas_id" class="form-label">Kelas</label>
                            <select class="form-select" id="edit_kelas_id" name="kelas_id" required>
                                <option></option>
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





</div>

@endsection

@section('js')


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/datatables.net/js/jquery.dataTables.min.js"></script>
<script>
    window.semesterAktif = @json($semesterAktif);
</script>

<script>

    let table;

    $(document).ready(function () {
        if ($.fn.modal) {
            $.fn.modal.Constructor.prototype._enforceFocus = function () {};
        }

        $('#modalTambahData').on('shown.bs.modal', function () {

            // Dosen
            $('#dosen_id').select2({
                placeholder: 'Cari Dosen...',
                allowClear: true,
                width: '100%',
                dropdownParent: $('#modalTambahData'),
                ajax: {
                    url: "{{ route('matakuliah-diampu.dosen.select2') }}",
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return {
                            q: params.term
                        };
                    },
                    processResults: function (data) {
                        return {
                            results: data.results
                        };
                    },
                    cache: true
                }
            });

            // Matakuliah
            $('#matakuliah_id').select2({
                placeholder: 'Cari Matakuliah...',
                allowClear: true,
                width: '100%',
                dropdownParent: $('#modalTambahData'),
                ajax: {
                    url: "{{ route('matakuliah-diampu.matakuliah.select2') }}",
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return {
                            q: params.term
                        };
                    },
                    processResults: function (data) {
                        return {
                            results: data.results
                        };
                    },
                    cache: true
                }
            });

            // Kelas
            $('#kelas_id').select2({
                placeholder: 'Cari Kelas...',
                allowClear: true,
                width: '100%',
                dropdownParent: $('#modalTambahData'),
                ajax: {
                    url: "{{ route('matakuliah-diampu.kelas.select2') }}",
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return {
                            q: params.term
                        };
                    },
                    processResults: function (data) {
                        return {
                            results: data.results
                        };
                    },
                    cache: true
                }
            });

        });

        var table = $("#datatable").DataTable({
            responsive: false,
            processing: true,
            serverSide: true,
            autoWidth: false,
            scrollX: true,
            ajax: {
                url: "{{ route('matakuliah-diampu.datatable') }}",
                data: function(d) {
                    var dosenFilter = $('#dosen-filter').val();
                    if (dosenFilter && dosenFilter !== "Pilih Dosen") {
                        d.dosen_id = dosenFilter;
                    } else {
                        d.dosen_id = '';
                    }
                }
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
                        return `
                            <div class="d-flex flex-column gap-1">
                                <button type="button" class="btn btn-warning btn-sm" onclick="editData(${full.id})">
                                    <i class="fe fe-edit"></i> Perbarui Data Kompen Tahun Ajaran Baru
                                </button>
                                <button type="button" class="btn btn-secondary btn-sm" onclick="editDataMahasiswaAktif(${full.id})">
                                    <i class="fe fe-edit"></i> Perbarui Data Kompen Mahasiswa Aktif
                                </button>
                            </div>
                        `;
                    }
                },
                {
                targets: 6,
                    render: function (data, type, full, meta) {
                        let isSemesterValid = false;

                        const semesterSekarang = window.semesterAktif?.semester || ''; // 'Ganjil' / 'Genap'
                        const semesterLokal = parseInt(full.semester_lokal);

                        if (semesterSekarang === 'Ganjil') {
                            if ([1, 3, 5, 7].includes(semesterLokal)) {
                                isSemesterValid = true;
                            }
                        } else if (semesterSekarang === 'Genap') {
                            if ([2, 4, 6, 8].includes(semesterLokal)) {
                                isSemesterValid = true;
                            }
                        }

                        const disableClass = isSemesterValid ? '' : 'disabled';

                        return `
                            <div class="d-flex flex-column gap-1">
                                <a href="/portal/matakuliah-diampu/kompensasi/${full.id}" class="btn btn-info btn-sm ${disableClass}">
                                    <i class="fe fe-eye"></i> Detail
                                </a>
                                <button type="button" class="btn btn-danger btn-sm ${disableClass}" onclick="hapusData(${full.id})">
                                    <i class="fe fe-trash"></i> Hapus
                                </button>
                            </div>
                        `;
                    }

                }

            ],
            columns: [
                { data: null },
                { data: 'dosen_name' },
                { data: 'matakuliah_name' },
                { data: 'kelas_name' },
                { data: 'semester_lokal' },
                { data: null },
                { data: 'id' },
            ],
            language: {
                searchPlaceholder: 'Search...',
                sSearch: ''
            }
        });


        $('#dosen-filter').on('change', function() {
            table.ajax.reload();
        });

    });

    $("#simpanData").on("click", function (e) {
        e.preventDefault();

        let formData = new FormData($("#tambahData")[0]);

        $.ajax({
            url: "{{ route('matakuliah-diampu.store') }}",
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (response) {
                const modalEl = document.getElementById('modalTambahData');
                const modal = bootstrap.Modal.getInstance(modalEl);
                modal.hide();

                modalEl.addEventListener('hidden.bs.modal', function handler() {
                    modalEl.removeEventListener('hidden.bs.modal', handler);

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
                        // $("#tambahData")[0].reset();
                        $('#datatable').DataTable().ajax.reload();
                        // table.ajax.reload(null, false);
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: response.message || 'Data tidak berhasil diproses.',
                            showConfirmButton: true
                        });
                    }
                });
            },
            error: function (xhr) {
                let message = 'Terjadi kesalahan saat menyimpan data.';
                if (xhr.responseJSON?.errors) {
                    const errors = Object.values(xhr.responseJSON.errors).flat().join('\n');
                    message = errors;
                } else if (xhr.responseJSON?.message) {
                    message = xhr.responseJSON.message;
                }

                const modalEl = document.getElementById('modalTambahData');
                const modal = bootstrap.Modal.getInstance(modalEl);
                modal.hide();

                modalEl.addEventListener('hidden.bs.modal', function handler() {
                    modalEl.removeEventListener('hidden.bs.modal', handler);

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


    function editData(id) {
        const urlTahunAjaran = `/portal/matakuliah-diampu/${id}/tahun-ajaran`;
        const urlRefresh = `/portal/matakuliah-diampu/${id}/refresh`;

        $.get(urlTahunAjaran, function(response) {
            console.log(response);

            if (response.status) {
                const tahunLama = response.tahun_ajaran_lama || '-';
                const tahunBaru = response.tahun_ajaran_baru;

                if (!tahunBaru && response.kompensasi_aktif_ada) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Tidak Bisa Diperbarui',
                        text: response.message || 'Tahun ajaran baru belum tersedia atau belum aktif.'
                    });
                    return;
                }

                Swal.fire({
                    title: 'Perbarui Data Kompensasi?',
                    text: tahunBaru
                        ? `Ini akan memperbarui mahasiswa tahun ajaran ${tahunLama} dan mengganti dengan mahasiswa tahun ajaran ${tahunBaru} baru.`
                        : `Belum ada data kompensasi aktif. Apakah ingin membuat data kompensasi untuk mahasiswa tahun ajaran ${tahunLama}?`,
                    icon: 'info',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Perbarui',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: urlRefresh,
                            type: 'PUT',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function (res) {
                                if (res.status) {
                                    Swal.fire('Berhasil', res.message, 'success');
                                    table.ajax.reload(null, false);
                                } else {
                                    Swal.fire({
                                        icon: 'warning',
                                        title: 'Tidak Bisa Memperbarui',
                                        text: res.message || 'Beberapa mahasiswa mungkin tidak sesuai atau data sudah ada.'
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
                    }
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Memuat Data',
                    text: response.message || 'Data tahun ajaran tidak tersedia atau tidak valid.'
                    });
            }
        }).fail(function(xhr) {
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: 'Gagal memuat data tahun ajaran.'
            });
        });
    }

    function editDataMahasiswaAktif(id) {
        const urlDataMahasiswaAktif = `/portal/matakuliah-diampu/mahasiswa-aktif/${id}`;

        $.get(urlDataMahasiswaAktif, function(response) {
            if (response.status) {

                Swal.fire({
                    title: 'Sinkronisasi Data Kompensasi?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Tambahkan',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: urlDataMahasiswaAktif,
                            type: 'GET',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(res) {
                                Swal.fire('Berhasil', res.message, 'success');
                                table.ajax.reload(null, false);
                            },
                            error: function(xhr) {
                                Swal.fire('Gagal', xhr.responseJSON?.message || 'Terjadi kesalahan.', 'error');
                            }
                        });
                    }
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: response.message || 'Data tidak ditemukan.'
                });
            }
        }).fail(function() {
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: 'Gagal memuat data mahasiswa aktif yang belum punya kompensasi.'
            });
        });
    }



    const urlMatakuliahDiampu = "{{ route('matakuliah-diampu.destroy', ':id') }}";

    function hapusData(id) {

        const url = urlMatakuliahDiampu.replace(':id', id);

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