<?php
$path = "../";
include "../config/db.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = mysqli_real_escape_string($conn, $_POST['name']);

    mysqli_query($conn,
        "INSERT INTO categories(name) VALUES('$name')");

    header("Location: index.php");
    exit;
}

include "../header.php";
?>

<div class="pagetitle">
  <h1>Add Category</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="../dashboard.php">Home</a></li>
      <li class="breadcrumb-item"><a href="index.php">Categories</a></li>
      <li class="breadcrumb-item active">Add Category</li>
    </ol>
  </nav>
</div><!-- End Page Title -->

<section class="section dashboard">
  <div class="row">
    <div class="col-lg-6">
      <div class="card">
        <div class="card-body">
          <h5 class="card-title">Add New Category</h5>

          <form method="POST">
            <div class="mb-3">
              <label for="categoryName" class="form-label">Category Name</label>
              <input type="text" class="form-control" id="categoryName" name="name" required>
            </div>

            <button type="submit" class="btn btn-success">Save</button>
            <a href="index.php" class="btn btn-secondary ms-2">Cancel</a>
          </form>

        </div>
      </div>
    </div>
  </div>
</section>

<?php include "../footer.php"; ?>
