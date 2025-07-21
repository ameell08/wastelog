 <!-- sidebar -->
 <div class="sidebar">
     <!-- Brand Logo -->
     <a href="/" class="brand-link">
         <img src="../../dist/img/AdminLTELogo.png" alt="WasteLog Logo" class="brand-image"
             style="opacity: .8">
         <span class="brand-text font-weight-light">WasteLog</span>
     </a>

     <!-- Sidebar -->
     <div class="sidebar">
         <!-- Sidebar user (optional) -->
         <div class="user-panel mt-3 pb-3 mb-3 d-flex">
             <div class="image">
                 <img src="../../dist/img/user2-160x160.jpg" class="img-circle elevation-2" alt="User Image">
             </div>
             <div class="info">
                 <a href="#" class="d-block text-left text-wrap">{{ Auth::user()->pengguna->nama }}</a>
             </div>
         </div>

         <!-- SidebarSearch Form -->
         <div class="form-inline">
             <div class="input-group" data-widget="sidebar-search">
                 <input class="form-control form-control-sidebar" type="search" placeholder="Search"
                     aria-label="Search">
                 <div class="input-group-append">
                     <button class="btn btn-sidebar">
                         <i class="fas fa-search fa-fw"></i>
                     </button>
                 </div>
             </div>
         </div>

         <!-- Sidebar Menu -->
         <!-- Sidebar Pimpinan -->
         @if (Auth::user()->getRole() == 'PMP')
             <li class="nav-header">Dashboard </li>
             <li class="nav-item">
                 <a href="{{ url('/dashboard') }}" class="nav-link {{ $activeMenu == 'dahsboard' ? 'active' : '' }} ">
                     <i class="nav-icon far fa-circle nav-icon"></i>
                     <p>Dashboard</p>
                 </a>
             </li>
             </li>
         @endif

         <!-- Sidebar Pimpinan -->
         @if (Auth::user()->getRole() == 'SDM')
             <li class="nav-header">Dashboard </li>
             <li class="nav-item">
                 <a href="{{ url('/dashboard') }}" class="nav-link {{ $activeMenu == 'dahsboard' ? 'active' : '' }} ">
                     <i class="nav-icon far fa-circle nav-icon"></i>
                     <p>Dashboard</p>
                 </a>
             </li>
                <li class="nav-item">
                     <a href="{{ url('/kodelimbah') }}"
                         class="nav-link {{ $activeMenu == 'kodelimbah' ? 'active' : '' }}">
                         <i class="far fa-circle nav-icon"></i>
                         <p>Kode Limbah</p>
                     </a>
                 </li>
                 <li class="nav-item">
                     <a href="{{ url('/datatruk') }}" class="nav-link {{ $activeMenu == 'datatruk' ? 'active' : '' }}">
                         <i class="far fa-circle nav-icon"></i>
                         <p>Data Truk</p>
                     </a>
                 </li>
                 <li class="nav-item">
                     <a href="{{ url('/datamesin') }}"
                         class="nav-link {{ $activeMenu == 'datamesin' ? 'active' : '' }}">
                         <i class="far fa-circle nav-icon"></i>
                         <p>Data Mesin</p>
                     </a>
                 </li>
             </li>
         @endif

         <!-- Sidebar Admin 1 -->
         @if (Auth::user()->getRole() == 'ADM1')
             <li class="nav-header">Input Limbah Masuk </li>
             <li class="nav-item">
                 <a href="{{ url('/inputlimbahmasuk') }}" class="nav-link {{ $activeMenu == 'inputlimbahmasuk' ? 'active' : '' }} ">
                     <i class="nav-icon fas fa-edit"></i>
                     <p>Input Limbah Masuk</p>
                 </a>
             </li>
             <li class="nav-item">
                 <a href="{{ url('/datalimbahmasuk') }}" class="nav-link {{ $activeMenu == 'datalimbahmasuk' ? 'active' : '' }} ">
                     <i class="nav-icon fas fa-copy"></i>
                     <p>Data Limbah Masuk</p>
                 </a>
             </li>
             </li>
         @endif

              <!-- Sidebar Admin 2 -->
         @if (Auth::user()->getRole() == 'ADM2')
             <li class="nav-header">Input Limbah Olah </li>
             <li class="nav-item">
                 <a href="{{ url('/inputlimbaholah') }}" class="nav-link {{ $activeMenu == 'inputlimbaholah' ? 'active' : '' }} ">
                     <i class="nav-icon fas fa-edit"></i>
                     <p>Input Limbah Olah</p>
                 </a>
             </li>
             <li class="nav-item">
                 <a href="{{ url('/datalimbaholah') }}" class="nav-link {{ $activeMenu == 'datalimbaholah' ? 'active' : '' }} ">
                     <i class="nav-icon fas fa-copy"></i>
                     <p>Data Limbah Olah</p>
                 </a>
             </li>
             </li>
         @endif

         <nav class="mt-2">
             <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                 data-accordion="false">
                 <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
                 <li class="nav-item">
                     <a href="{{ url('/dashboard') }}"
                         class="nav-link {{ $activeMenu == 'dashboard' ? 'active' : '' }}">
                         <i class="nav-icon far fa-circle nav-icon"></i>
                         <p>
                             Dashboard
                         </p>
                     </a>
                 </li>
             </ul>
            </nav>
     </div>

     <style>
    .sidebar {
        background-color: #0D313F;
        /* Mengganti warna gradien menjadi warna solid */
        box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
        padding: 8px;
        font-size: 14px;
        letter-spacing: 0.35px;
        height: 100vh;
        /* Pastikan sidebar mengisi tinggi layar */
        position: fixed;
        /* Agar sidebar tetap di kiri saat scroll */
        width: 250px;
        /* Tentukan lebar sidebar */
        overflow-y: auto;
        /* Agar sidebar bisa scroll vertikal jika konten melebihi tinggi */
    }

    .sidebar .nav-link {
        border-radius: 8px;
        transition: all 0.3s ease;
        padding: 8px 16px;
        color: #ffffff;
        flex-grow: 1;
        /* Allow the nav-links to take up the available space */
        overflow-y: auto;
        /* Add vertical scrolling if the nav-links exceed the available space */
    }

    .sidebar .nav-link:hover {
        background-color: rgba(255, 255, 255, 0.1);
        transform: translateX(5px);
    }

    .sidebar .nav-link.active {
        background: rgba(255, 255, 255, 0.2);
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }

    .sidebar .nav-header {
        color: #ffffff;
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        padding: 1rem 1rem 0.5rem;
    }

    .nav-link.text-white.bg-danger {
        color: #fff !important;
        background-color: #e74c3c !important;
    }

    .nav-link.active {
        background-color: teal !important;
        /* Warna latar belakang teal */
        color: white !important;
        /* Warna teks putih agar kontras */
        border-radius: 4px;
        /* Opsional: Tambahkan sedikit radius */
        padding: 8px 16px;
    }

    .brand-text {
        color: #fff;
        letter-spacing: 1px;
    }
</style>


              