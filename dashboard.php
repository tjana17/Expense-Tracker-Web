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

// Daily Expenses for Chart
$chart_query = mysqli_query($conn, 
  "SELECT DATE(expense_date) as date, SUM(amount) as total 
   FROM expenses 
   WHERE user_id='$user_id' 
   AND MONTH(expense_date) = '$current_month' 
   AND YEAR(expense_date) = '$current_year' 
   GROUP BY DATE(expense_date) 
   ORDER BY DATE(expense_date)");

$dates = [];
$amounts = [];
while ($row = mysqli_fetch_assoc($chart_query)) {
    $dates[] = $row['date'];
    $amounts[] = $row['total'];
}
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

  <!-- Expense Report Chart -->
  <div class="col-8">
    <div class="card">

      <div class="card-body">
        <h5 class="card-title">Expense Report <span>/This Month</span></h5>

        <!-- Line Chart -->
        <div id="reportsChart"></div>

        <script>
          document.addEventListener("DOMContentLoaded", () => {
            new ApexCharts(document.querySelector("#reportsChart"), {
              series: [{
                name: 'Expenses',
                data: <?php echo json_encode($amounts); ?>,
              }],
              chart: {
                height: 350,
                type: 'area',
                toolbar: {
                  show: false
                },
              },
              markers: {
                size: 4
              },
              colors: ['#4154f1'],
              fill: {
                type: "gradient",
                gradient: {
                  shadeIntensity: 1,
                  opacityFrom: 0.3,
                  opacityTo: 0.4,
                  stops: [0, 90, 100]
                }
              },
              dataLabels: {
                enabled: false
              },
              stroke: {
                curve: 'smooth',
                width: 2
              },
              xaxis: {
                type: 'datetime',
                categories: <?php echo json_encode($dates); ?>
              },
              tooltip: {
                x: {
                  format: 'dd/MM/yy'
                },
              }
            }).render();
          });
        </script>
        <!-- End Line Chart -->

      </div>

    </div>
  </div><!-- End Expense Report Chart -->

  <!-- Financial Overview Pie Chart -->
  <div class="col-4">
    <div class="card">

      <div class="card-body pb-0">
        <h5 class="card-title">Financial Overview <span>| This Month</span></h5>

        <div id="trafficChart" style="min-height: 360px;" class="echart"></div>

        <script>
          document.addEventListener("DOMContentLoaded", () => {
            echarts.init(document.querySelector("#trafficChart")).setOption({
              tooltip: {
                trigger: 'item'
              },
              legend: {
                top: '5%',
                left: 'center'
              },
              series: [{
                name: 'Financial Overview',
                type: 'pie',
                radius: ['40%', '70%'],
                avoidLabelOverlap: false,
                label: {
                  show: false,
                  position: 'center'
                },
                emphasis: {
                  label: {
                    show: true,
                    fontSize: '18',
                    fontWeight: 'bold'
                  }
                },
                labelLine: {
                  show: false
                },
                data: [{
                    value: <?php echo $total_income; ?>,
                    name: 'Income',
                    itemStyle: { color: '#2eca6a' }
                  },
                  {
                    value: <?php echo $total_expense; ?>,
                    name: 'Expense',
                    itemStyle: { color: '#4154f1' }
                  },
                  {
                    value: <?php echo $total_savings; ?>,
                    name: 'Savings',
                    itemStyle: { color: '#ff771d' }
                  }
                ]
              }]
            });
          });
        </script>

      </div>
    </div>
  </div><!-- End Financial Overview Pie Chart -->

</div>
</section>

<?php include "footer.php"; ?>