<?php
include "header.php";

$user_id = $_SESSION['user_id'];
$message = "";
$message_type = "";

// Handle Profile Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_profile') {
    $name = mysqli_real_escape_string($conn, $_POST['fullName']);
    // $email = mysqli_real_escape_string($conn, $_POST['email']); // Email is read-only
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    
    // Basic validation
    if (!empty($name)) {
        $update_query = "UPDATE users SET name='$name', address='$address', phone='$phone' WHERE id='$user_id'";
        
        // Handle File Upload
        if (isset($_FILES['profileImage']) && $_FILES['profileImage']['error'] == 0) {
            $target_dir = "assets/img/uploads/";
            $file_extension = pathinfo($_FILES["profileImage"]["name"], PATHINFO_EXTENSION);
            $new_filename = "profile_" . $user_id . "_" . time() . "." . $file_extension;
            $target_file = $target_dir . $new_filename;
            
            // Allow certain file formats
            $allowed_types = array('jpg', 'png', 'jpeg', 'gif');
            if (in_array(strtolower($file_extension), $allowed_types)) {
                if (move_uploaded_file($_FILES["profileImage"]["tmp_name"], $target_file)) {
                    $update_query = "UPDATE users SET name='$name', address='$address', phone='$phone', profile_image='$target_file' WHERE id='$user_id'";
                } else {
                    $message = "Sorry, there was an error uploading your file.";
                    $message_type = "danger";
                }
            } else {
                $message = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
                $message_type = "danger";
            }
        }

        if (mysqli_query($conn, $update_query)) {
            $message = "Profile updated successfully!";
            $message_type = "success";
            // Update session name if changed
            $_SESSION['name'] = $name; 
        } else {
            $message = "Error updating profile: " . mysqli_error($conn);
            $message_type = "danger";
        }
    } else {
        $message = "Name is required.";
        $message_type = "danger";
    }
}

// Handle Password Change
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'change_password') {
    $current_password = $_POST['password'];
    $new_password = $_POST['newpassword'];
    $renew_password = $_POST['renewpassword'];

    // Verify current password
    $user_query = mysqli_query($conn, "SELECT password FROM users WHERE id='$user_id'");
    $user_data = mysqli_fetch_assoc($user_query);

    if (password_verify($current_password, $user_data['password'])) {
        if ($new_password === $renew_password) {
            $new_password_hash = password_hash($new_password, PASSWORD_DEFAULT);
            $update_pass_query = "UPDATE users SET password='$new_password_hash' WHERE id='$user_id'";
            if (mysqli_query($conn, $update_pass_query)) {
                $message = "Password changed successfully!";
                $message_type = "success";
            } else {
                $message = "Error changing password: " . mysqli_error($conn);
                $message_type = "danger";
            }
        } else {
            $message = "New passwords do not match.";
            $message_type = "danger";
        }
    } else {
        $message = "Current password is incorrect.";
        $message_type = "danger";
    }
}

// Fetch User Data
$result = mysqli_query($conn, "SELECT * FROM users WHERE id='$user_id'");
$user = mysqli_fetch_assoc($result);
?>

<div class="pagetitle">
  <h1>Profile</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
      <li class="breadcrumb-item active">Profile</li>
    </ol>
  </nav>
</div><!-- End Page Title -->

<section class="section profile">
  <div class="row">
    <div class="col-xl-4">

      <div class="card">
        <div class="card-body profile-card pt-4 d-flex flex-column align-items-center">

          <img src="<?= !empty($user['profile_image']) ? $user['profile_image'] : 'assets/img/profile-img.jpg' ?>" alt="Profile" class="rounded-circle" style="width: 180px; height: 180px; object-fit: cover;">
          <h2><?= htmlspecialchars($user['name']) ?></h2>
          <h3>User</h3>
        </div>
      </div>

    </div>

    <div class="col-xl-8">

      <div class="card">
        <div class="card-body pt-3">
          
          <?php if ($message): ?>
            <div class="alert alert-<?= $message_type ?> alert-dismissible fade show" role="alert">
              <?= $message ?>
              <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
          <?php endif; ?>

          <!-- Bordered Tabs -->
          <ul class="nav nav-tabs nav-tabs-bordered">

            <li class="nav-item">
              <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#profile-overview">Overview</button>
            </li>

            <li class="nav-item">
              <button class="nav-link" data-bs-toggle="tab" data-bs-target="#profile-edit">Edit Profile</button>
            </li>

            <li class="nav-item">
              <button class="nav-link" data-bs-toggle="tab" data-bs-target="#profile-change-password">Change Password</button>
            </li>

          </ul>
          <div class="tab-content pt-2">

            <div class="tab-pane fade show active profile-overview" id="profile-overview">
              <h5 class="card-title">Profile Details</h5>

              <div class="row">
                <div class="col-lg-3 col-md-4 label ">Full Name</div>
                <div class="col-lg-9 col-md-8"><?= htmlspecialchars($user['name']) ?></div>
              </div>

              <div class="row">
                <div class="col-lg-3 col-md-4 label">Email</div>
                <div class="col-lg-9 col-md-8"><?= htmlspecialchars($user['email']) ?></div>
              </div>

              <div class="row">
                <div class="col-lg-3 col-md-4 label">Address</div>
                <div class="col-lg-9 col-md-8"><?= htmlspecialchars($user['address'] ?? '') ?></div>
              </div>

              <div class="row">
                <div class="col-lg-3 col-md-4 label">Phone</div>
                <div class="col-lg-9 col-md-8"><?= htmlspecialchars($user['phone'] ?? '') ?></div>
              </div>

            </div>

            <div class="tab-pane fade profile-edit pt-3" id="profile-edit">

              <!-- Profile Edit Form -->
              <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="update_profile">
                <div class="row mb-3">
                  <label for="profileImage" class="col-md-4 col-lg-3 col-form-label">Profile Image</label>
                  <div class="col-md-8 col-lg-9">
                    <img src="<?= !empty($user['profile_image']) ? $user['profile_image'] : 'assets/img/profile-img.jpg' ?>" alt="Profile" style="max-width: 100px;">
                    <div class="pt-2">
                        <input type="file" name="profileImage" class="form-control" accept="image/*">
                    </div>
                  </div>
                </div>

                <div class="row mb-3">
                  <label for="fullName" class="col-md-4 col-lg-3 col-form-label">Full Name</label>
                  <div class="col-md-8 col-lg-9">
                    <input name="fullName" type="text" class="form-control" id="fullName" value="<?= htmlspecialchars($user['name']) ?>" required>
                  </div>
                </div>

                <div class="row mb-3">
                  <label for="Email" class="col-md-4 col-lg-3 col-form-label">Email</label>
                  <div class="col-md-8 col-lg-9">
                    <input name="email" type="email" class="form-control" id="Email" value="<?= htmlspecialchars($user['email']) ?>" readonly disabled>
                  </div>
                </div>

                <div class="row mb-3">
                  <label for="Address" class="col-md-4 col-lg-3 col-form-label">Address</label>
                  <div class="col-md-8 col-lg-9">
                    <input name="address" type="text" class="form-control" id="Address" value="<?= htmlspecialchars($user['address'] ?? '') ?>">
                  </div>
                </div>
                
                <div class="row mb-3">
                  <label for="Phone" class="col-md-4 col-lg-3 col-form-label">Phone</label>
                  <div class="col-md-8 col-lg-9">
                    <input name="phone" type="text" class="form-control" id="Phone" value="<?= htmlspecialchars($user['phone'] ?? '') ?>">
                  </div>
                </div>

                <div class="text-center">
                  <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
              </form><!-- End Profile Edit Form -->

            </div>

            <div class="tab-pane fade pt-3" id="profile-change-password">
              <!-- Change Password Form -->
              <form method="POST">
                <input type="hidden" name="action" value="change_password">
                <div class="row mb-3">
                  <label for="currentPassword" class="col-md-4 col-lg-3 col-form-label">Current Password</label>
                  <div class="col-md-8 col-lg-9">
                    <input name="password" type="password" class="form-control" id="currentPassword" required>
                  </div>
                </div>

                <div class="row mb-3">
                  <label for="newPassword" class="col-md-4 col-lg-3 col-form-label">New Password</label>
                  <div class="col-md-8 col-lg-9">
                    <input name="newpassword" type="password" class="form-control" id="newPassword" required>
                  </div>
                </div>

                <div class="row mb-3">
                  <label for="renewPassword" class="col-md-4 col-lg-3 col-form-label">Re-enter New Password</label>
                  <div class="col-md-8 col-lg-9">
                    <input name="renewpassword" type="password" class="form-control" id="renewPassword" required>
                  </div>
                </div>

                <div class="text-center">
                  <button type="submit" class="btn btn-primary">Change Password</button>
                </div>
              </form><!-- End Change Password Form -->

            </div>

          </div><!-- End Bordered Tabs -->

        </div>
      </div>

    </div>
  </div>
</section>

<?php include "footer.php"; ?>
