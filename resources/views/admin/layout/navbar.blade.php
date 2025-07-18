<nav
    class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme"
    id="layout-navbar"
    >
    <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
        <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
        <i class="bx bx-menu bx-sm"></i>
        </a>
    </div>

    <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
            
        @role('Mahasiswa')
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    @if(isset($semesterMax))
                        Jumlah Keseluruhan Kompensasi semester 1~{{ $semesterMax }} : {{ $totalKompensasi }} Menit
                    @else
                        Jumlah Keseluruhan Kompensasi : {{ $totalKompensasi }} Menit
                    @endif
                </div>
            </div>
        @endrole

        @hasanyrole('superAdmin|Dosen')
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <strong>Tahun Ajaran:</strong> {{ $tahunAjaran }}
                </div>
                <div style="margin-left: 20px;">
                    <strong>Semester :</strong> {{ $semesterAktif }}
                </div>
            </div>


        @endhasanyrole




        <ul class="navbar-nav flex-row align-items-center ms-auto">

        <!-- User -->
        <li class="nav-item navbar-dropdown dropdown-user dropdown">
            <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
            <div class="avatar avatar-online">
                <img src="assets/img/avatars/1.png" alt class="w-px-40 h-auto rounded-circle" />
            </div>
            </a>
            <ul class="dropdown-menu dropdown-menu-end">
            <li>
                <a class="dropdown-item" href="#">
                <div class="d-flex">
                    <div class="flex-shrink-0 me-3">
                    <div class="avatar avatar-online">
                        <img src="assets/img/avatars/1.png" alt class="w-px-40 h-auto rounded-circle" />
                    </div>
                    </div>
                    <div class="flex-grow-1">
                    <span class="fw-semibold d-block">John Doe</span>
                    <small class="text-muted">Admin</small>
                    </div>
                </div>
                </a>
            </li>
            <li>
                <div class="dropdown-divider"></div>
            </li>
            <li>
                <a class="dropdown-item" href="#">
                <i class="bx bx-user me-2"></i>
                <span class="align-middle">My Profile</span>
                </a>
            </li>
            {{-- <li>
                <a class="dropdown-item" href="#">
                <i class="bx bx-cog me-2"></i>
                <span class="align-middle">Settings</span>
                </a>
            </li> --}}
            <li>
                <div class="dropdown-divider"></div>
            </li>
            <li>
                <button id="btn-logout" class="dropdown-item" type="button">
                    <i class="bx bx-power-off me-2"></i>
                    <span class="align-middle">Log Out</span>
                </button>
            </li>

            </ul>
        </li>
        <!--/ User -->
        </ul>
    </div>
    </nav>

    <!-- / Navbar -->