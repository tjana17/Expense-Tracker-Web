<?php
$path = "../";
include "../config/db.php"; // Include DB directly first to handle logic

$id = $_GET['id'];
$result = mysqli_query($conn, "SELECT * FROM expenses WHERE id=$id");
$expense = mysqli_fetch_assoc($result);

// Fetch categories for dropdown
$cats = mysqli_query($conn, "SELECT * FROM categories");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $amount = $_POST['amount'];
    $category = $_POST['category'];
    $note = mysqli_real_escape_string($conn, $_POST['note']);
    $date = $_POST['date'];

    mysqli_query($conn,
        "UPDATE expenses SET
         amount='$amount',
         category_id='$category',
         note='$note',
         expense_date='$date'
         WHERE id=$id");

    header("Location: index.php");
    exit;
}

include "../header.php"; // Include header (HTML output) AFTER logic
?>

<div class="pagetitle">
  <h1>Edit Expense</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="../dashboard.php">Home</a></li>
      <li class="breadcrumb-item"><a href="index.php">Expenses</a></li>
      <li class="breadcrumb-item active">Edit Expense</li>
    </ol>
  </nav>
</div><!-- End Page Title -->

<section class="section dashboard">
  <div class="row">
    <div class="col-lg-12">
      <div class="card">
        <div class="card-body">
          <h5 class="card-title">Edit Expense</h5>

          <form method="POST" class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Amount</label>
              <input type="number" step="0.01" class="form-control"
                     name="amount" value="<?= $expense['amount'] ?>" required>
            </div>

            <div class="col-md-6">
              <label class="form-label">Category</label>
              <select class="form-control" name="category" required>
                <option value="">Select</option>
                <?php while ($c = mysqli_fetch_assoc($cats)): ?>
                  <option value="<?= $c['id'] ?>" <?= $expense['category_id'] == $c['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($c['name']) ?>
                  </option>
                <?php endwhile; ?>
              </select>
            </div>

            <div class="col-md-6">
              <label class="form-label">Date</label>
              <input type="date" class="form-control"
                     name="date" value="<?= $expense['expense_date'] ?>" required>
            </div>

            <div class="col-md-6">
              <label class="form-label">Note</label>
              <input class="form-control" name="note" value="<?= htmlspecialchars($expense['note']) ?>">
            </div>

            <div class="text-center">
              <button type="submit" class="btn btn-primary">Update</button>
              <button type="reset" class="btn btn-secondary">Reset</button>
            </div>
          </form>

        </div>
      </div>
    </div>
  </div>
</section>

<?php include "../footer.php"; ?>
