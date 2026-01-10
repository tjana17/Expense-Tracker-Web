<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Path helper
$path = isset($path) ? $path : "./";

include_once __DIR__ . "/config/db.php";

$user_name = "Guest";
$user_role = "User"; // Default or fetch if available

// Greeting Logic
$page_name = basename($_SERVER['PHP_SELF']);
$current_uri = $_SERVER['PHP_SELF'];

// Determine active section
$is_dashboard = strpos($current_uri, 'dashboard.php') !== false;
$is_categories = strpos($current_uri, '/categories/') !== false;
$is_expenses = strpos($current_uri, '/expenses/') !== false;
$is_income = strpos($current_uri, '/income/') !== false;
$is_reports = strpos($current_uri, 'reports.php') !== false;
$is_analytics = strpos($current_uri, 'analytics.php') !== false;

$hour = date('H');
if ($hour < 12) {
    $greeting = "Good Morning";
} elseif ($hour < 17) {
    $greeting = "Good Afternoon";
} else {
    $greeting = "Good Evening";
}

if (isset($_SESSION['user_id'])) {
    $id = $_SESSION['user_id'];
    $query = mysqli_query($conn, "SELECT name, profile_image FROM users WHERE id='$id'");
    if ($query && mysqli_num_rows($query) > 0) {
        $row = mysqli_fetch_assoc($query);
        $user_name = $row['name'];
        $user_profile_img = !empty($row['profile_image']) ? $path . $row['profile_image'] : $path . "assets/img/profile-img.jpg";
    }
}
if (!isset($user_profile_img)) {
    $user_profile_img = $path . "assets/img/profile-img.jpg";
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Expense Tracker</title>
  <meta content="" name="description">
  <meta content="" name="keywords">

  <!-- Favicons -->
  <link href="<?php echo $path; ?>assets/img/favicon.png" rel="icon">
  <link href="<?php echo $path; ?>assets/img/apple-touch-icon.png" rel="apple-touch-icon">

  <!-- Google Fonts -->
  <link href="https://fonts.gstatic.com" rel="preconnect">
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="<?php echo $path; ?>assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="<?php echo $path; ?>assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="<?php echo $path; ?>assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
  <link href="<?php echo $path; ?>assets/vendor/quill/quill.snow.css" rel="stylesheet">
  <link href="<?php echo $path; ?>assets/vendor/quill/quill.bubble.css" rel="stylesheet">
  <link href="<?php echo $path; ?>assets/vendor/remixicon/remixicon.css" rel="stylesheet">
  <link href="<?php echo $path; ?>assets/vendor/simple-datatables/style.css" rel="stylesheet">

  <!-- Template Main CSS File -->
  <link href="<?php echo $path; ?>assets/css/style.css" rel="stylesheet">
</head>

<body>

  <!-- ======= Header ======= -->
  <header id="header" class="header fixed-top d-flex align-items-center">

    <div class="d-flex align-items-center justify-content-between">
      <a href="<?php echo $path; ?>dashboard.php" class="logo d-flex align-items-center">
        <img src="<?php echo $path; ?>assets/img/logo.png" alt="">
        <span class="d-none d-lg-block">Expense Tracker</span>
      </a>
      <i class="bi bi-list toggle-sidebar-btn"></i>
    </div><!-- End Logo -->

    <div class="search-bar logo">
      <?php echo $greeting; ?>,
      <span><?php echo htmlspecialchars($user_name); ?></span><br />
      <p>Your performance summary this month</p>
    </div><!-- End Search Bar -->

    <nav class="header-nav ms-auto">
      <ul class="d-flex align-items-center">

        <li class="nav-item dropdown pe-3">

          <a class="nav-link nav-profile d-flex align-items-center pe-0" href="#" data-bs-toggle="dropdown">
            <img src="<?php echo $user_profile_img; ?>" alt="Profile" class="rounded-circle" style="width: 36px; height: 36px; object-fit: cover;">
            <span class="d-none d-md-block dropdown-toggle ps-2"><?php echo htmlspecialchars($user_name); ?></span>
          </a><!-- End Profile Iamge Icon -->

          <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
            <!-- <li class="dropdown-header">
              <h6><?php echo htmlspecialchars($user_name); ?></h6>
               <span><?php echo htmlspecialchars($user_role); ?></span> 
            </li> 
            <li>
              <hr class="dropdown-divider">
            </li>-->

            <li>
              <a class="dropdown-item d-flex align-items-center" href="<?php echo $path; ?>profile.php">
                <i class="bi bi-person"></i>
                <span>My Profile</span>
              </a>
            </li>
            <li>
              <hr class="dropdown-divider">
            </li> 

            <li>
              <a class="dropdown-item d-flex align-items-center" href="<?php echo $path; ?>auth/logout.php">
                <i class="bi bi-box-arrow-right"></i>
                <span>Sign Out</span>
              </a>
            </li>

          </ul><!-- End Profile Dropdown Items -->
        </li><!-- End Profile Nav -->

      </ul>
    </nav><!-- End Icons Navigation -->

  </header><!-- End Header -->

  <!-- ======= Sidebar ======= -->
  <aside id="sidebar" class="sidebar">

    <ul class="sidebar-nav" id="sidebar-nav">

      <li class="nav-item">
        <a class="nav-link <?= $is_dashboard ? '' : 'collapsed' ?>" href="<?php echo $path; ?>dashboard.php">
          <i class="bi bi-grid"></i>
          <span>Dashboard</span>
        </a>
      </li><!-- End Dashboard Nav -->
      
      <li class="nav-item">
        <a class="nav-link <?= $is_categories ? '' : 'collapsed' ?>" href="<?php echo $path; ?>categories/index.php">
            <i class="bi bi-menu-button-wide"></i>
            <span>Categories</span>
        </a>
      </li>

      <li class="nav-item">
        <a class="nav-link <?= $is_expenses ? '' : 'collapsed' ?>" href="<?php echo $path; ?>expenses/index.php">
            <i class="bi bi-cash-coin"></i>
            <span>Expenses</span>
        </a>
      </li>

      <li class="nav-item">
        <a class="nav-link <?= $is_income ? '' : 'collapsed' ?>" href="<?php echo $path; ?>income/index.php">
            <i class="bi bi-wallet2"></i>
            <span>Income</span>
        </a>
      </li>

      <li class="nav-item">
        <a class="nav-link <?= $is_analytics ? '' : 'collapsed' ?>" href="<?php echo $path; ?>analytics.php">
            <i class="bi bi-bar-chart-line"></i>
            <span>Analytics</span>
        </a>
      </li>

      <li class="nav-item">
        <a class="nav-link <?= $is_reports ? '' : 'collapsed' ?>" href="<?php echo $path; ?>reports.php">
            <i class="bi bi-file-earmark-bar-graph"></i>
            <span>Reports</span>
        </a>
      </li>

      <!-- Add other nav items here as needed -->

    </ul>

  </aside><!-- End Sidebar-->

  <main id="main" class="main">
