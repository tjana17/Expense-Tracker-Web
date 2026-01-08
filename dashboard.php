<?php include "header.php";

$user_id = $_SESSION['user_id'];

$result = mysqli_query($conn,
  "SELECT SUM(amount) AS total FROM expenses WHERE user_id=$user_id");
$data = mysqli_fetch_assoc($result);

$result = mysqli_query($conn,
  "SELECT e.*, c.name FROM expenses e
   JOIN categories c ON e.category_id=c.id
   WHERE user_id=$user_id");
?>


<div class="pagetitle">
  <h1>Dashboard</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
      <li class="breadcrumb-item active">Dashboard</li>
    </ol>
  </nav>
</div><!-- End Page Title -->

<section class="section dashboard">
<div class="row">
  <div class="col-md-4">
    <div class="card p-3">
      <h5>Total Expenses</h5>
      <h3>₹<?php echo $data['total'] ?? 0; ?></h3>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-12">
    <a href="expenses/add.php" class="btn btn-primary mt-4">+ Add Expense</a>
  </div>
</div>
</section>

<?php include "footer.php"; ?>