<?php
$path = "../";
include "../config/db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $amount = $_POST['amount'];
    $note = mysqli_real_escape_string($conn, $_POST['note']);
    $date = $_POST['date'];
    $user_id = $_SESSION['user_id'];

    mysqli_query($conn,
        "INSERT INTO incomes(user_id,amount,note,income_date)
         VALUES('$user_id','$amount','$note','$date')");

    header("Location: index.php");
    exit;
}

include "../header.php"; 
?>

<div class="pagetitle">
  <h1>Add Income</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="../dashboard.php">Home</a></li>
      <li class="breadcrumb-item"><a href="index.php">Income</a></li>
      <li class="breadcrumb-item active">Add Income</li>
    </ol>
  </nav>
</div><!-- End Page Title -->

<section class="section dashboard">
  <div class="row">
    <div class="col-lg-12">
      <div class="card">
        <div class="card-body">
          <h5 class="card-title">Add Income</h5>

          <form method="POST" class="row g-3">
            <div class="col-md-12">
              <label class="form-label">Amount</label>
              <input type="number" step="0.01" class="form-control" name="amount" required>
            </div>

            <div class="col-md-6">
              <label class="form-label">Date</label>
              <input type="date" class="form-control" name="date" required>
            </div>

            <div class="col-md-6">
              <label class="form-label">Note</label>
              <input class="form-control" name="note" placeholder="Optional note">
            </div>

            <div class="text-center">
              <button type="submit" class="btn btn-primary">Save Income</button>
              <button type="reset" class="btn btn-secondary">Reset</button>
            </div>
          </form>

        </div>
      </div>
    </div>
  </div>
</section>

<?php include "../footer.php"; ?>
