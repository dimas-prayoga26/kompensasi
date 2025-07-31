@extends('admin.layout.main')

@section('title', 'Profile')

@section('css')

@section('content')
<div class="content-wrapper">
<!-- Content -->

<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Account Settings /</span> Account</h4>

    <!-- Menampilkan alert jika ada pesan sukses -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Perhatian!</strong>
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    
    <div class="row">
        <div class="col-md-12">
            <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                <div class="card mb-4">
                    <h5 class="card-header">Profile Details</h5>
                    <!-- Account -->
                    <div class="card-body">
                        @php
                            $user = auth()->user();
                            $defaultAvatar = 'assets/img/avatars/profile_default.jpg';

                            if ($user->hasRole('Mahasiswa')) {
                                $avatarPath = optional($user->detailMahasiswa)->file_path 
                                            ? Storage::url($user->detailMahasiswa->file_path) 
                                            : $defaultAvatar;
                            } elseif ($user->hasRole('Dosen')) {
                                $avatarPath = optional($user->detailDosen)->file_path 
                                            ? Storage::url($user->detailDosen->file_path) 
                                            : $defaultAvatar;
                            } else {
                                $avatarPath = $defaultAvatar;
                            }
                        @endphp

                        <div class="d-flex align-items-start align-items-sm-center gap-4 mb-2">
                            <img src="{{ $avatarPath }}" 
                                alt="user-avatar" 
                                class="d-block rounded" 
                                height="100" 
                                width="100" 
                                id="uploadedAvatar">
                        </div>


                        <div class="button-wrapper">
                            <label for="upload" class="btn btn-primary me-2 mb-4" tabindex="0">
                                <span class="d-none d-sm-block">Upload new photo</span>
                                <i class="bx bx-upload d-block d-sm-none"></i>
                                <input type="file" id="upload" name="profile_image" class="account-file-input" hidden accept="image/png, image/jpeg" />
                            </label>
                            <button type="button" class="btn btn-outline-secondary account-image-reset mb-4">
                                <i class="bx bx-reset d-block d-sm-none"></i>
                                <span class="d-none d-sm-block">Reset</span>
                            </button>
                            <p class="text-muted mb-0">Allowed JPG or PNG. Max size of 2MB</p>
                        </div>
                        </div>
                    </div>
                    <hr class="my-0" />
                    <div class="card-body">
                    @csrf
                    <div class="row">
                        <!-- First Name -->
                        <div class="mb-3 col-md-6">
                            <label for="firstName" class="form-label">First Name</label>
                            <input class="form-control" type="text" id="firstName" name="firstName"
                                value="{{ auth()->user()->hasRole('Mahasiswa') ? optional(auth()->user()->detailMahasiswa)->first_name : optional(auth()->user()->detailDosen)->first_name }}" />
                        </div>

                        <!-- Last Name -->
                        <div class="mb-3 col-md-6">
                            <label for="lastName" class="form-label">Last Name</label>
                            <input class="form-control" type="text" id="lastName" name="lastName"
                                value="{{ auth()->user()->hasRole('Mahasiswa') ? optional(auth()->user()->detailMahasiswa)->last_name : optional(auth()->user()->detailDosen)->last_name }}" />
                        </div>

                        <!-- Jenis Kelamin -->
                        <div class="mb-3 col-md-6">
                            <label for="jenis_kelamin" class="form-label">Jenis Kelamin</label>
                            @php
                                $jenisKelamin = auth()->user()->hasRole('Mahasiswa') 
                                    ? optional(auth()->user()->detailMahasiswa)->jenis_kelamin 
                                    : optional(auth()->user()->detailDosen)->jenis_kelamin;
                            @endphp
                            <select class="form-control" name="jenis_kelamin" id="jenis_kelamin">
                                <option value="Laki-laki" {{ $jenisKelamin == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                                <option value="Perempuan" {{ $jenisKelamin == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                            </select>
                        </div>

                        <!-- Email -->
                        <div class="mb-3 col-md-6">
                            <label for="email" class="form-label">E-mail</label>
                            <input class="form-control" type="text" id="email" name="email"
                                value="{{ auth()->user()->email }}" disabled />
                        </div>

                        <!-- Mahasiswa -->
                        @if(auth()->user()->hasRole('Mahasiswa'))
                            <div class="mb-3 col-md-6">
                                <label for="tahun_masuk" class="form-label">Tahun Masuk</label>
                                <input class="form-control" type="text" name="tahun_masuk"
                                    value="{{ optional(auth()->user()->detailMahasiswa)->tahun_masuk }}" disabled />
                            </div>
                            <div class="mb-3 col-md-6">
                                <label for="prodi_id" class="form-label">Program Studi</label>
                                <input class="form-control" type="text" name="prodi_id"
                                    value="{{ optional(optional(auth()->user()->detailMahasiswa)->prodi)->nama }}" disabled />
                            </div>
                            <div class="mb-3 col-md-6">
                                <label for="kelas" class="form-label">Kelas</label>
                                <input class="form-control" type="text" name="kelas"
                                    value="{{ optional(auth()->user()->detailMahasiswa)->kelas }}" disabled />
                            </div>
                        @endif

                        <!-- Dosen -->
                        @if(auth()->user()->hasRole('Dosen'))
                            <div class="mb-3 col-md-6">
                                <label for="jabatan_fungsional" class="form-label">Jabatan Fungsional</label>
                                <input class="form-control" type="text" name="jabatan_fungsional"
                                    value="{{ optional(auth()->user()->detailDosen)->jabatan_fungsional }}" />
                            </div>
                            <div class="mb-3 col-md-6">
                                <label for="bidang_keahlian" class="form-label">Bidang Keahlian</label>
                                <input class="form-control" type="text" name="bidang_keahlian"
                                    value="{{ optional(auth()->user()->detailDosen)->bidang_keahlian }}" />
                            </div>
                        @endif
                    </div>

                    <div class="mt-2 d-flex align-items-center">
                        <div>
                            <button type="submit" class="btn btn-primary me-2">Simpan Perubahan</button>
                            <button type="reset" class="btn btn-outline-secondary me-2">Batal</button>
                        </div>

                        <button type="button" class="btn btn-warning ms-auto" data-bs-toggle="modal" data-bs-target="#modalUbahPassword">
                            Ubah Password
                        </button>
                    </div>
                </div>

                </div>
            </form>
        </div>
    </div>

    <div class="modal fade" id="modalUbahPassword" tabindex="-1" aria-labelledby="modalUbahPasswordLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('ubah-password') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalUbahPasswordLabel">Ubah Password</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="password_lama" class="form-label">Password Lama</label>
                            <input type="password" class="form-control" id="password_lama" name="password_lama" required>
                        </div>
                        <div class="mb-3">
                            <label for="password_baru" class="form-label">Password Baru</label>
                            <input type="password" class="form-control" id="password_baru" name="password_baru" required>
                        </div>
                        <div class="mb-3">
                            <label for="konfirmasi_password" class="form-label">Konfirmasi Password Baru</label>
                            <input type="password" class="form-control" id="konfirmasi_password" name="password_baru_confirmation" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Simpan Password</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>



</div>

@endsection

@section('js')
<script>
document.getElementById('upload').addEventListener('change', function(e) {
    var reader = new FileReader();
    reader.onload = function() {
        document.getElementById('uploadedAvatar').src = reader.result;
    };
    reader.readAsDataURL(this.files[0]);
});
</script>
@endsection
