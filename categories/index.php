<?php
$path = "../";
include "../header.php";

// $result = mysqli_query($conn, "SELECT * FROM categories");
?>

<div class="pagetitle">
  <h1>Categories</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="../dashboard.php">Home</a></li>
      <li class="breadcrumb-item active">Categories</li>
    </ol>
  </nav>
</div><!-- End Page Title -->

</section>

<?php
// Pagination Logic
$limit = 10;
$page = isset($_GET['page']) && $_GET['page'] > 0 ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Count total records
$count_result = mysqli_query($conn, "SELECT COUNT(*) as total FROM categories");
$total_row = mysqli_fetch_assoc($count_result);
$total_records = $total_row['total'];
$total_pages = ceil($total_records / $limit);

// Update main query with LIMIT
$result = mysqli_query($conn, "SELECT * FROM categories LIMIT $offset, $limit");
?>
<!-- Re-rendering content with correct query result -->
<section class="section dashboard">
  <div class="row">
    <div class="col-lg-12">
      <div class="card">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center">
            <h5 class="card-title">Manage Categories</h5>
            <a href="add.php" class="btn btn-primary">+ Add Category</a>
          </div>

          <table class="table table-bordered table-striped">
            <thead>
              <tr>
                <th>#</th>
                <th>Name</th>
                <th width="100" class="text-center">Action</th>
              </tr>
            </thead>
            <tbody>
              <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                  <td><?= $row['id'] ?></td>
                  <td><?= htmlspecialchars($row['name']) ?></td>
                  <td class="text-center">
                    <a href="edit.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-warning"><i class="bi bi-pencil-square"></i></a>
                    <a href="delete.php?id=<?= $row['id'] ?>" onclick="return confirm('Delete this category?')" class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></a>
                  </td>
                </tr>
              <?php endwhile; ?>
            </tbody>
          </table>

          <!-- Pagination UI -->
          <?php if($total_pages > 1): ?>
          <nav aria-label="Page navigation" class="mt-4">
            <ul class="pagination justify-content-center">
              <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                <a class="page-link" href="?page=<?= $page - 1 ?>" tabindex="-1">Previous</a>
              </li>
              <?php for($i=1; $i<=$total_pages; $i++): ?>
              <li class="page-item <?= ($page == $i) ? 'active' : '' ?>">
                <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
              </li>
              <?php endfor; ?>
              <li class="page-item <?= ($page >= $total_pages) ? 'disabled' : '' ?>">
                <a class="page-link" href="?page=<?= $page + 1 ?>">Next</a>
              </li>
            </ul>
          </nav>
          <?php endif; ?>

        </div>
      </div>
    </div>
  </div>
</section>

<?php include "../footer.php"; ?>
