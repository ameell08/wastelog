<!-- Navbar -->
<nav class="main-header navbar navbar-expand navbar-white navbar-light justify-content-between">
  <!-- Left navbar links -->
  <ul class="navbar-nav">
    <li class="nav-item">
      <a class="nav-link" data-widget="pushmenu" href="#" role="button">
        <i class="fas fa-bars"></i>
      </a>
    </li>
    {{-- <li class="nav-item d-none d-sm-inline-block">
      <a href="../../index3.html" class="nav-link">Home</a>
    </li> --}}
  </ul>

  <!-- Right navbar links (Logout) -->
  <ul class="navbar-nav ml-auto">
    <li class="nav-item">
      <a class="btn btn-sm btn-outline-danger d-flex align-items-center text-danger px-2 py-1" 
         href="{{ url('/logout') }}" 
         onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
        <i class="fas fa-sign-out-alt me-1"></i> Logout
      </a>
      <form id="logout-form" action="{{ url('/logout') }}" method="POST" style="display: none;">
        @csrf
      </form>
    </li>
  </ul>
</nav>
