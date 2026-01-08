<?php include "header.php";

$user_id = $_SESSION['user_id'];

$current_month = date('m');
$current_year = date('Y');

// Total Expenses (This Month)
$expense_query = mysqli_query($conn, 
  "SELECT SUM(amount) AS total FROM expenses 
   WHERE user_id='$user_id' 
   AND MONTH(expense_date) = '$current_month' 
   AND YEAR(expense_date) = '$current_year'");
$expense_data = mysqli_fetch_assoc($expense_query);
$total_expense = $expense_data['total'] ?? 0;

// Total Income (This Month)
$income_query = mysqli_query($conn, 
  "SELECT SUM(amount) AS total FROM incomes 
   WHERE user_id='$user_id' 
   AND MONTH(income_date) = '$current_month' 
   AND YEAR(income_date) = '$current_year'");
$income_data = mysqli_fetch_assoc($income_query);
$total_income = $income_data['total'] ?? 0;

// Total Savings (This Month)
$total_savings = $total_income - $total_expense;
?>


<div class="pagetitle d-flex justify-content-between align-items-center">
  <div>
    <h1>Dashboard</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
        <li class="breadcrumb-item active">Dashboard</li>
      </ol>
    </nav>
  </div>
  <div>
    <a href="expenses/add.php" class="btn btn-primary">+ Add Expense</a>
    <a href="income/add.php" class="btn btn-success ms-2">+ Add Income</a>
  </div>
</div><!-- End Page Title -->

<section class="section dashboard">
<div class="row">

  <!-- Total Expenses Card -->
  <div class="col-xxl-4 col-md-4">
    <div class="card info-card sales-card">
      
      <div class="card-body">
        <h5 class="card-title">Expenses <span>| This Month</span></h5>

        <div class="d-flex align-items-center">
          <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
            <i class="bi bi-currency-exchange"></i>
          </div>
          <div class="ps-3">
            <h6>₹<?= number_format($total_expense, 2) ?></h6>
            <!-- <span class="text-success small pt-1 fw-bold">12%</span> <span class="text-muted small pt-2 ps-1">increase</span> -->
          </div>
        </div>
      </div>

    </div>
  </div><!-- End Expenses Card -->

  <!-- Total Income Card -->
  <div class="col-xxl-4 col-md-4">
    <div class="card info-card revenue-card">

      <div class="card-body">
        <h5 class="card-title">Income <span>| This Month</span></h5>

        <div class="d-flex align-items-center">
          <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
            <i class="bi bi-currency-dollar"></i>
          </div>
          <div class="ps-3">
            <h6>₹<?= number_format($total_income, 2) ?></h6>
            <!-- <span class="text-success small pt-1 fw-bold">8%</span> <span class="text-muted small pt-2 ps-1">increase</span> -->
          </div>
        </div>
      </div>

    </div>
  </div><!-- End Income Card -->

  <!-- Savings Card -->
  <div class="col-xxl-4 col-md-4">
    <div class="card info-card customers-card">

      <div class="card-body">
        <h5 class="card-title">Savings <span>| This Month</span></h5>

        <div class="d-flex align-items-center">
          <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
            <i class="bi bi-piggy-bank"></i>
          </div>
          <div class="ps-3">
            <h6>₹<?= number_format($total_savings, 2) ?></h6>
          </div>
        </div>
      </div>

    </div>
  </div><!-- End Savings Card -->

</div>
</section>

<?php include "footer.php"; ?>