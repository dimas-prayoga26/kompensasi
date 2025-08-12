<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo">
        <a href="index.html" class="app-brand-link">
            <img src="{{ asset('images/polindra.png') }}" alt="Logo" width="50" />
            <span class="app-brand-text demo menu-text fw-bolder ms-2" style="text-transform: uppercase;">E-KOMPEN</span>
        </a>

        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
            <i class="bx bx-chevron-left bx-sm align-middle"></i>
        </a>
    </div>

    <div class="menu-inner-shadow"></div>

    <ul class="menu-inner py-1">
        <!-- Dashboard -->
        @hasanyrole('Mahasiswa|Dosen|superAdmin')
            <li class="menu-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <a href="{{ route('dashboard') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-home-circle"></i>
                    <div data-i18n="Analytics">Dashboard</div>
                </a>
            </li>
        @endhasanyrole

        @hasanyrole('Dosen|superAdmin')
            <li class="menu-item {{ request()->routeIs('matakuliah-diampu.index') || request()->routeIs('matakuliah-diampu.kompensasi.show') ? 'active' : '' }}">
                <a href="{{ route('matakuliah-diampu.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-book-open"></i>
                    <div data-i18n="Analytics">Mata kuliah diampu</div>
                </a>
            </li>
        @endhasanyrole

        <li class="menu-item {{ request()->routeIs('tugas-kompensasi.index') ? 'active' : '' }}">
            <a href="{{ route('tugas-kompensasi.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-clipboard"></i>
                <div data-i18n="Analytics">Tugas Kompensasi</div>
            </a>
        </li>

        @role('superAdmin')
            <li class="menu-header small text-uppercase">
                <span class="menu-header-text">Meta Data</span>
            </li>

            <!-- Settings -->
            <li class="menu-item
                {{ request()->routeIs('user.*') ||
                request()->routeIs('mataKuliah.*') ||
                request()->routeIs('kelas.*') ||
                request()->routeIs('semester.*') ||
                request()->routeIs('prodi.*') ? 'active open' : '' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons bx bx-cog"></i>
                    <div data-i18n="Form Layouts">Settings</div>
                </a>
                <ul class="menu-sub">
                    <li class="menu-item {{ request()->routeIs('user.*') ? 'active' : '' }}">
                        <a href="{{ route('user.index') }}" class="menu-link">
                            <div data-i18n="Vertical Form">User</div>
                        </a>
                    </li>
                    <li class="menu-item {{ request()->routeIs('mataKuliah.*') ? 'active' : '' }}">
                        <a href="{{ route('mataKuliah.index') }}" class="menu-link">
                            <div data-i18n="Horizontal Form">Matakuliah</div>
                        </a>
                    </li>
                    <li class="menu-item {{ request()->routeIs('kelas.*') ? 'active' : '' }}">
                        <a href="{{ route('kelas.index') }}" class="menu-link">
                            <div data-i18n="Horizontal Form">Kelas</div>
                        </a>
                    </li>
                    <li class="menu-item {{ request()->routeIs('semester.*') ? 'active' : '' }}">
                        <a href="{{ route('semester.index') }}" class="menu-link">
                            <div data-i18n="Horizontal Form">Semester</div>
                        </a>
                    </li>
                    <li class="menu-item {{ request()->routeIs('prodi.*') ? 'active' : '' }}">
                        <a href="{{ route('prodi.index') }}" class="menu-link">
                            <div data-i18n="Horizontal Form">Prodi</div>
                        </a>
                    </li>
                </ul>
            </li>
        @endrole
    </ul>


</aside>
<!-- / Menu -->