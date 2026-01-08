<?php
$path = "../";
include "../config/db.php";

$id = $_GET['id'];
$result = mysqli_query($conn, "SELECT * FROM categories WHERE id=$id");
$category = mysqli_fetch_assoc($result);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = mysqli_real_escape_string($conn, $_POST['name']);

    mysqli_query($conn,
        "UPDATE categories SET name='$name' WHERE id=$id");

    header("Location: index.php");
    exit;
}

include "../header.php";
?>

<div class="pagetitle">
  <h1>Edit Category</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="../dashboard.php">Home</a></li>
      <li class="breadcrumb-item"><a href="index.php">Categories</a></li>
      <li class="breadcrumb-item active">Edit Category</li>
    </ol>
  </nav>
</div><!-- End Page Title -->

<section class="section dashboard">
  <div class="row">
    <div class="col-lg-6">
      <div class="card">
        <div class="card-body">
          <h5 class="card-title">Edit Category</h5>

          <form method="POST">
            <div class="mb-3">
              <label for="categoryName" class="form-label">Category Name</label>
              <input type="text" class="form-control" id="categoryName" name="name" value="<?= htmlspecialchars($category['name']) ?>" required>
            </div>

            <button type="submit" class="btn btn-primary">Update</button>
            <a href="index.php" class="btn btn-secondary ms-2">Cancel</a>
          </form>

        </div>
      </div>
    </div>
  </div>
</section>

<?php include "../footer.php"; ?>
