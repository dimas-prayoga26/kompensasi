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
                        <div class="d-flex align-items-start align-items-sm-center gap-4">
                        <!-- Display current profile image -->
                        <img src="{{ auth()->user()->hasRole('Mahasiswa') 
                                    ? (auth()->user()->detailMahasiswa->file_path 
                                        ? Storage::url(auth()->user()->detailMahasiswa->file_path) 
                                        : 'assets/img/avatars/profile_default.jpg')
                                    : (auth()->user()->hasRole('Dosen') 
                                        ? (auth()->user()->detailDosen->file_path 
                                            ? Storage::url(auth()->user()->detailDosen->file_path) 
                                            : asset('assets/img/avatars/profile_default.jpg')) 
                                        : 'assets/img/avatars/profile_default.jpg') }}" 
                        alt="user-avatar" 
                        class="d-block rounded" 
                        height="100" 
                        width="100" 
                        id="uploadedAvatar">

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
                            <div class="mb-3 col-md-6">
                                <label for="firstName" class="form-label">First Name</label>
                                @if(auth()->user()->hasRole('Mahasiswa'))
                                    <input class="form-control" type="text" id="firstName" name="firstName" value="{{ auth()->user()->detailMahasiswa->first_name }}" />
                                @elseif(auth()->user()->hasRole('Dosen'))
                                    <input class="form-control" type="text" id="firstName" name="firstName" value="{{ auth()->user()->detailDosen->first_name }}" />
                                @endif
                            </div>
                            <div class="mb-3 col-md-6">
                                <label for="lastName" class="form-label">Last Name</label>
                                @if(auth()->user()->hasRole('Mahasiswa'))
                                    <input class="form-control" type="text" name="lastName" id="lastName" value="{{ auth()->user()->detailMahasiswa->last_name }}" />
                                @elseif(auth()->user()->hasRole('Dosen'))
                                    <input class="form-control" type="text" name="lastName" id="lastName" value="{{ auth()->user()->detailDosen->last_name }}" />
                                @endif
                            </div>

                            @if(auth()->user()->hasRole('Mahasiswa'))
                                <div class="mb-3 col-md-6">
                                    <label for="jenis_kelamin" class="form-label">Jenis Kelamin</label>
                                    <select class="form-control" name="jenis_kelamin" id="jenis_kelamin">
                                        <option value="Laki-laki" {{ auth()->user()->detailMahasiswa->jenis_kelamin == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                                        <option value="Perempuan" {{ auth()->user()->detailMahasiswa->jenis_kelamin == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                                    </select>
                                </div>
                            @endif

                            @if(auth()->user()->hasRole('Dosen'))
                                <div class="mb-3 col-md-6">
                                    <label for="jenis_kelamin" class="form-label">Jenis Kelamin</label>
                                    <select class="form-control" name="jenis_kelamin" id="jenis_kelamin">
                                        <option value="Laki-laki" {{ auth()->user()->detailDosen->jenis_kelamin == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                                        <option value="Perempuan" {{ auth()->user()->detailDosen->jenis_kelamin == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                                    </select>
                                </div>
                            @endif

                            <div class="mb-3 col-md-6">
                                <label for="email" class="form-label">E-mail</label>
                                <input class="form-control" type="text" id="email" name="email" value="{{ auth()->user()->email }}" placeholder="john.doe@example.com" disabled/>
                            </div>

                            @if(auth()->user()->hasRole('Mahasiswa'))
                                <div class="mb-3 col-md-6">
                                    <label for="tahun_masuk" class="form-label">Tahun Masuk</label>
                                    <input class="form-control" type="text" name="tahun_masuk" value="{{ auth()->user()->detailMahasiswa->tahun_masuk }}" disabled />
                                </div>
                                <div class="mb-3 col-md-6">
                                    <label for="prodi_id" class="form-label">Program Studi</label>
                                    <input class="form-control" type="text" name="prodi_id" value="{{ auth()->user()->detailMahasiswa->prodi->nama }}" disabled />
                                </div>
                                <div class="mb-3 col-md-6">
                                    <label for="kelas" class="form-label">Kelas</label>
                                    <input class="form-control" type="text" name="kelas" value="{{ auth()->user()->detailMahasiswa->kelas }}" disabled />
                                </div>
                            @elseif(auth()->user()->hasRole('Dosen'))
                                <div class="mb-3 col-md-6">
                                    <label for="jabatan_fungsional" class="form-label">Jabatan Fungsional</label>
                                    <input class="form-control" type="text" name="jabatan_fungsional" value="{{ auth()->user()->detailDosen->jabatan_fungsional }}" disabled/>
                                </div>
                                <div class="mb-3 col-md-6">
                                    <label for="bidang_keahlian" class="form-label">Bidang Keahlian</label>
                                    <input class="form-control" type="text" name="bidang_keahlian" value="{{ auth()->user()->detailDosen->bidang_keahlian }}" disabled/>
                                </div>
                            @endif
                        </div>
                        <div class="mt-2">
                            <button type="submit" class="btn btn-primary me-2">Save changes</button>
                            <button type="reset" class="btn btn-outline-secondary">Cancel</button>
                        </div>
                    </div>
                </div>
            </form>
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
