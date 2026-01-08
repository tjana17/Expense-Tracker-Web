<?php
$path = "../";
include "../config/db.php";

$id = $_GET['id'];
$result = mysqli_query($conn, "SELECT * FROM incomes WHERE id=$id");
$income = mysqli_fetch_assoc($result);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $amount = $_POST['amount'];
    $note = mysqli_real_escape_string($conn, $_POST['note']);
    $date = $_POST['date'];

    mysqli_query($conn,
        "UPDATE incomes SET
         amount='$amount',
         note='$note',
         income_date='$date'
         WHERE id=$id");

    header("Location: index.php");
    exit;
}

include "../header.php";
?>

<div class="pagetitle">
  <h1>Edit Income</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="../dashboard.php">Home</a></li>
      <li class="breadcrumb-item"><a href="index.php">Income</a></li>
      <li class="breadcrumb-item active">Edit Income</li>
    </ol>
  </nav>
</div><!-- End Page Title -->

<section class="section dashboard">
  <div class="row">
    <div class="col-lg-12">
      <div class="card">
        <div class="card-body">
          <h5 class="card-title">Edit Income</h5>

          <form method="POST" class="row g-3">
            <div class="col-md-12">
              <label class="form-label">Amount</label>
              <input type="number" step="0.01" class="form-control"
                     name="amount" value="<?= $income['amount'] ?>" required>
            </div>

            <div class="col-md-6">
              <label class="form-label">Date</label>
              <input type="date" class="form-control"
                     name="date" value="<?= $income['income_date'] ?>" required>
            </div>

            <div class="col-md-6">
              <label class="form-label">Note</label>
              <input class="form-control" name="note" value="<?= htmlspecialchars($income['note']) ?>">
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
