<!-- Sidebar -->
<div class="sidebar">
    <!-- Brand Logo -->
    <a href="/" class="brand-link text-center">
        <img src="{{ asset('logo2.png') }}" alt="Logo" class="brand-image" style="opacity: .8; ">
        <span class="brand-text font-weight-light">WASTELOG</span>
    </a>

    <!-- Sidebar User Info -->
    <div class="user-panel mt-3 pb-3 mb-3 d-flex align-items-center">
        <div class="image">
            <img src="{{ asset('img/user.png') }}" class="img-circle elevation-2" alt="User Image">
        </div>
        <div class="info text-white">
            <span class="d-block">{{ Auth::user()->nama }}</span>
        </div>
    </div>

    <!-- Sidebar Menu -->
    <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">

            <!-- Sidebar Super Admin -->
            @if (Auth::user()->getRole() == 'SDM')
                <li class="nav-item">
                    <a href="{{ url('/dashboard') }}" class="nav-link {{ $activeMenu == 'dashboard' ? 'active' : '' }}">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Dashboard</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ url('/kodelimbah') }}"
                        class="nav-link {{ $activeMenu == 'kodelimbah' ? 'active' : '' }}">
                        <i class="nav-icon fas fa-tag"></i>
                        <p>Kode Limbah</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ url('/datatruk') }}" class="nav-link {{ $activeMenu == 'datatruk' ? 'active' : '' }}">
                        <i class="nav-icon fas fa-truck"></i>
                        <p>Data Truk</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ url('/datamesin') }}"
                        class="nav-link {{ $activeMenu == 'datamesin' ? 'active' : '' }}">
                        <i class="nav-icon fas fa-cogs"></i>
                        <p>Data Mesin</p>
                    </a>
                </li>
            @endif

            <!-- Sidebar Admin 1 -->
            @if (Auth::user()->getRole() == 'ADM1')
                <li class="nav-item">
                    <a href="{{ url('/inputlimbahmasuk') }}"
                        class="nav-link {{ $activeMenu == 'inputlimbahmasuk' ? 'active' : '' }}">
                        <i class="nav-icon fas fa-edit"></i>
                        <p>Input Limbah Masuk</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ url('/datalimbahmasuk') }}"
                        class="nav-link {{ $activeMenu == 'datalimbahmasuk' ? 'active' : '' }}">
                        <i class="nav-icon fas fa-copy"></i>
                        <p>Data Limbah Masuk</p>
                    </a>
                </li>
            @endif

            <!-- Sidebar Admin 2 -->
            @if (Auth::user()->getRole() == 'ADM2')
                <li class="nav-item">
                    <a href="{{ url('/inputlimbaholah') }}"
                        class="nav-link {{ $activeMenu == 'inputlimbaholah' ? 'active' : '' }}">
                        <i class="nav-icon fas fa-edit"></i>
                        <p>Input Limbah Olah</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ url('/datalimbaholah') }}"
                        class="nav-link {{ $activeMenu == 'datalimbaholah' ? 'active' : '' }}">
                        <i class="nav-icon fas fa-copy"></i>
                        <p>Data Limbah Olah</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ url('/inputpengirimanresidu') }}"
                        class="nav-link {{ $activeMenu == 'inputpengirimanresidu' ? 'active' : '' }}">
                        <i class="nav-icon fas fa-file-import"></i>
                        <p>Input Pengiriman Residu </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ url('/datapengirimanresidu') }}"
                        class="nav-link {{ $activeMenu == 'datapengirimanresidu' ? 'active' : '' }}">
                        <i class="nav-icon fas fa-book"></i>
                        <p>Data Pengiriman Residu</p>
                    </a>
                </li>
            @endif

            <!-- Sidebar Pimpinan -->
            @if (Auth::user()->getRole() == 'PMP')
                <li class="nav-item">
                    <a href="{{ url('/dashboard') }}"
                        class="nav-link {{ $activeMenu == 'dashboard' ? 'active' : '' }}">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Dashboard</p>
                    </a>
                </li>
            @endif


        </ul>
    </nav>
</div>

<!-- Styling khusus -->
<style>
    .sidebar {
        background-color: #0a3926;
        height: 100vh;
        position: fixed;
        width: 250px;
        overflow-y: auto;
        padding: 8px;
        font-size: 14px;
    }

    .sidebar .brand-text {
        color: #fff;
        font-size: 1.2rem;
        font-weight: bold;
        letter-spacing: 1px;
    }

    .sidebar .nav-link {
        color: #ffffff;
        border-radius: 8px;
        transition: all 0.3s ease;
    }

    .sidebar .nav-link:hover {
        background-color: rgba(255, 255, 255, 0.1);
        transform: translateX(5px);
    }

    .sidebar .nav-link.active {
        background-color: #aaaa07 !important;
        color: white !important;
    }

    .user-panel .info span {
        color: white;
        font-weight: 500;
    }
</style>
