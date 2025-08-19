<!-- Navbar -->
<nav class="main-header navbar navbar-expand navbar-white navbar-light justify-content-between">
  <!-- Left navbar links -->
  <ul class="navbar-nav">
    <li class="nav-item">
      <a class="nav-link" data-widget="pushmenu" href="#" role="button">
        <i class="fas fa-bars"></i>
      </a>
    </li>
  </ul>

  <!-- Right navbar links -->
  <ul class="navbar-nav ml-auto">
    <!-- Profile Dropdown -->
    <li class="nav-item dropdown">
      <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="profileDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <div class="user-panel d-flex align-items-center">
          <div class="image">
            <img src="{{ asset('img/user.png') }}" class="img-circle elevation-2" alt="User Image" style="width: 25px; height: 25px;">
          </div>
          <div class="info ml-2">
            <span class="text-dark">{{ Auth::user()->nama ?? 'User' }}</span>
          </div>
        </div>
      </a>
      <div class="dropdown-menu dropdown-menu-right" aria-labelledby="profileDropdown">
        <a class="dropdown-item" href="#" data-toggle="modal" data-target="#profileModal">
          <i class="fas fa-user mr-2"></i> Profile
        </a>
        <a class="dropdown-item" href="#" data-toggle="modal" data-target="#editProfileModal">
          <i class="fas fa-edit mr-2"></i> Edit Profile
        </a>
        <a class="dropdown-item" href="#" data-toggle="modal" data-target="#changePasswordModal">
          <i class="fas fa-key mr-2"></i> Change Password
        </a>
        <div class="dropdown-divider"></div>
        <a class="dropdown-item text-danger" href="{{ url('/logout') }}" 
           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
          <i class="fas fa-sign-out-alt mr-2"></i> Logout
        </a>
      </div>
    </li>
  </ul>
  
  <!-- Logout Form -->
  <form id="logout-form" action="{{ url('/logout') }}" method="POST" style="display: none;">
    @csrf
  </form>
</nav>

<!-- Profile Modal -->
<div class="modal fade" id="profileModal" tabindex="-1" role="dialog" aria-labelledby="profileModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="profileModalLabel">User Profile</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="text-center mb-3">
          <img src="{{ asset('adminlte/dist/img/user2-160x160.jpg') }}" class="img-circle elevation-2" alt="User Image" style="width: 80px; height: 80px;">
        </div>
        <form>
          <div class="form-group">
            <label for="userName">Name</label>
            <input type="text" class="form-control" id="userName" value="{{ Auth::user()->nama ?? '' }}" readonly>
          </div>
          <div class="form-group">
            <label for="userEmail">Email</label>
            <input type="email" class="form-control" id="userEmail" value="{{ Auth::user()->email ?? '' }}" readonly>
          </div>
          <div class="form-group">
            <label for="userRole">Role</label>
            <input type="text" class="form-control" id="userRole" value="{{ Auth::user()->jenisPengguna->kode_jenis_pengguna ?? 'User' }}" readonly>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<!-- Change Password Modal -->
<div class="modal fade" id="changePasswordModal" tabindex="-1" role="dialog" aria-labelledby="changePasswordModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="changePasswordModalLabel">Change Password</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form id="changePasswordForm">
        <div class="modal-body">
          @csrf
          <div class="form-group">
            <label for="currentPassword">Current Password</label>
            <input type="password" class="form-control" id="currentPassword" name="current_password" required>
          </div>
          <div class="form-group">
            <label for="newPassword">New Password</label>
            <input type="password" class="form-control" id="newPassword" name="new_password" required>
          </div>
          <div class="form-group">
            <label for="confirmPassword">Confirm New Password</label>
            <input type="password" class="form-control" id="confirmPassword" name="new_password_confirmation" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Change Password</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Edit Profile Modal -->
<div class="modal fade" id="editProfileModal" tabindex="-1" role="dialog" aria-labelledby="editProfileModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editProfileModalLabel">Edit Profile</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form id="editProfileForm">
        <div class="modal-body">
          @csrf
          <div class="text-center mb-3">
            <img src="{{ asset('img/user.png') }}" class="img-circle elevation-2" alt="User Image" style="width: 80px; height: 80px;">
          </div>
          <div class="form-group">
            <label for="editUserName">Name</label>
            <input type="text" class="form-control" id="editUserName" name="nama" value="{{ Auth::user()->nama ?? '' }}" required>
          </div>
          <div class="form-group">
            <label for="editUserEmail">Email</label>
            <input type="email" class="form-control" id="editUserEmail" name="email" value="{{ Auth::user()->email ?? '' }}" required>
          </div>
          <div class="form-group">
            <label for="editUserRole">Role</label>
            <input type="text" class="form-control" id="editUserRole" value="{{ Auth::user()->jenisPengguna->kode_jenis_pengguna ?? 'User' }}" readonly>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Update Profile</button>
        </div>
      </form>
    </div>
  </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Edit Profile Form
    $('#editProfileForm').on('submit', function(e) {
        e.preventDefault();
        
        var formData = new FormData(this);
        formData.append('_method', 'PUT');
        
        $.ajax({
            url: '{{ route("profile.update") }}',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    // Show success message
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: response.message,
                        showConfirmButton: false,
                        timer: 1500
                    }).then(function() {
                        // Reload page to update header info
                        location.reload();
                    });
                    
                    $('#editProfileModal').modal('hide');
                }
            },
            error: function(xhr) {
                var errors = xhr.responseJSON.errors;
                var errorMessage = '';
                
                if (errors) {
                    $.each(errors, function(key, value) {
                        errorMessage += value[0] + '\n';
                    });
                } else {
                    errorMessage = 'Terjadi kesalahan. Silakan coba lagi.';
                }
                
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: errorMessage
                });
            }
        });
    });
    
    // Change Password Form
    $('#changePasswordForm').on('submit', function(e) {
        e.preventDefault();
        
        var formData = new FormData(this);
        formData.append('_method', 'PUT');
        
        $.ajax({
            url: '{{ route("profile.changePassword") }}',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: response.message,
                        showConfirmButton: false,
                        timer: 1500
                    });
                    
                    $('#changePasswordModal').modal('hide');
                    $('#changePasswordForm')[0].reset();
                }
            },
            error: function(xhr) {
                var response = xhr.responseJSON;
                var errorMessage = '';
                
                if (response.errors) {
                    $.each(response.errors, function(key, value) {
                        errorMessage += value[0] + '\n';
                    });
                } else if (response.message) {
                    errorMessage = response.message;
                } else {
                    errorMessage = 'Terjadi kesalahan. Silakan coba lagi.';
                }
                
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: errorMessage
                });
            }
        });
    });
});
</script>

<style>
  /* hover  */
  .dropdown-menu .dropdown-item:hover {
    background-color: #f1f1f1 !important;
    olor: #212529 !important;
  }

  /* Active / Focus semua item */
  .dropdown-menu .dropdown-item:active,
  .dropdown-menu .dropdown-item:focus {
    background-color: #d6d6d6 !important;
    color: #212529 !important;
  }

  .dropdown-menu .dropdown-item.text-danger:hover {
    color: #dc3545 !important;   
  }
  
</style>
@endpush
