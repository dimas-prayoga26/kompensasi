@extends('admin.layout.main')

@section('title', 'Kelas')

@section('css')

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<style>
    div.dataTables_filter {
        margin-bottom: 2rem;
    }

</style>


@section('content')

<div class="container-xxl flex-grow-1 container-p-y">

    <h4 class="fw-bold py-3 mb-4">
    <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Kompensasi</span></h4>
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
        <div class="col-lg-12 mb-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Detail Kompensasi - {{ $dosenMatakuliah->matakuliah->nama }} / {{ $dosenMatakuliah->kelas->nama }}</h5>
                    
                    <div class="d-flex">
                        <a href="{{ route('matakuliah-diampu.kompensasi.exportExcel', $dosenMatakuliah->id) }}" class="btn btn-sm btn-success"  style="margin-right: 10px;">
                            <i class="bx bx-download"></i> Download Excel
                        </a>
                        <a href="{{ route('matakuliah-diampu.index') }}" class="btn btn-sm btn-secondary">← Kembali</a>
                    </div>
                </div>

                <div class="card-body table-responsive">
                    <table id="datatable" class="table table-bordered text-nowrap w-100">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>Nama Mahasiswa</th>
                                <th>Menit Kompensasi</th>
                                <th>Keterangan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        {{-- tbody akan diisi otomatis oleh DataTables --}}
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalEditData" tabindex="-1" aria-labelledby="modalEditDataLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="editData" enctype="multipart/form-data">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalEditDataLabel">Edit Data Kompensasi</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="edit_id_kompensasi" name="id">

                        <div class="mb-3">
                            <label for="edit_menit_kompensasi" class="form-label">Menit Kompensasi</label>
                            <input type="number" class="form-control" id="edit_menit_kompensasi" name="menit_kompensasi" required min="0">
                        </div>

                        <div class="mb-3">
                            <label for="edit_keterangan" class="form-label">Keterangan</label>
                            <textarea class="form-control" id="edit_keterangan" name="keterangan" rows="3" placeholder="Opsional..."></textarea>
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
<script src="https://cdn.ckeditor.com/ckeditor5/41.2.0/classic/ckeditor.js"></script>
<script src="https://cdn.jsdelivr.net/npm/datatables.net/js/jquery.dataTables.min.js"></script>

<script>

    let table;
    let editorKeterangan;

    $('#modalEditData').on('shown.bs.modal', function () {
        if (!editorKeterangan) {
            ClassicEditor
                .create(document.querySelector('#edit_keterangan'))
                .then(editor => {
                    editorKeterangan = editor;
                })
                .catch(error => {
                    console.error(error);
                });
        }
    });

    $(document).ready(function () {
        table = $("#datatable").DataTable({
            responsive: true,
            processing: true,
            serverSide: true,
            autoWidth: false,
            ajax: {
                url: "{{ route('matakuliah-diampu.kompensasi.datatableKompensasi', $dosenMatakuliah->id) }}",
            },
            columns: [
                { data: null},
                { data: 'nama_mahasiswa', name: 'nama_mahasiswa' },
                { data: 'menit_kompensasi', name: 'menit_kompensasi' },
                { data: 'keterangan', name: 'keterangan' },
                { data: 'aksi', name: 'aksi', orderable: false, searchable: false }
            ],
            columnDefs: [
                {
                    targets: 0,
                    render: function (data, type, full, meta) {
                        return meta.row + 1;
                    }
                },
                {
                    targets: 3,
                    render: function(data, type, full, meta) {
                        if (!data) return 'No description'; // <- Cegah error jika null atau kosong

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
                {
                    targets: 4,
                    render: function (data, type, full, meta) {
                        console.log(full.id);
                        
                        return `
                            <button type="button" class="btn btn-warning btn-sm" onclick="editData(${full.id})">
                                <i class="fe fe-edit"></i> Update
                            </button>
                        `;
                    }
                }
            ],
            language: {
                searchPlaceholder: 'Cari...',
                sSearch: ''
            }
        });
    });

    const urlEditKompensasi = "{{ route('matakuliah-diampu.kompensasi.detail', ':id') }}";

    function editData(id) {
        const url = urlEditKompensasi.replace(':id', id);

        $.ajax({
            url: url,
            type: "GET",
            success: function(response) {
                if (response.status === true) {
                    const kompensasi = response.data;

                    $('#edit_id_kompensasi').val(kompensasi.id || '');

                    $('#edit_menit_kompensasi').val(kompensasi.menit_kompensasi || '');

                    if (typeof window.editorKeterangan !== 'undefined') {
                        window.editorKeterangan.setData(kompensasi.keterangan || '');
                    } else {
                        $('#edit_keterangan').val(kompensasi.keterangan || '');
                    }
                    
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

     $("#updateData").on("click", function(e) {
            e.preventDefault();

            let id = $("#editData").data('id');
            
            let formData = new FormData($("#editData")[0]);
            formData.append('keterangan', editorKeterangan.getData());

            if (typeof editorKeterangan !== 'undefined') {
                formData.set('keterangan', editorKeterangan.getData());
            }

            let url = "{{ route('matakuliah-diampu.kompensasi.update', ':id') }}".replace(':id', id);

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
                success: function (response) {
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
                            table.ajax.reload(null, false);
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

    


</script>



@endsection