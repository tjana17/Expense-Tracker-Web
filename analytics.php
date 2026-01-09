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

// Fetch distinct months for dropdown
$months_query = mysqli_query($conn, 
  "SELECT DISTINCT MONTH(expense_date) as month, YEAR(expense_date) as year 
   FROM expenses 
   WHERE user_id='$user_id' 
   UNION 
   SELECT DISTINCT MONTH(income_date) as month, YEAR(income_date) as year 
   FROM incomes 
   WHERE user_id='$user_id'
   ORDER BY year DESC, month DESC");

$available_months = [];
while ($row = mysqli_fetch_assoc($months_query)) {
    $available_months[] = $row;
}
?>


<div class="pagetitle d-flex justify-content-between align-items-center">
  <div>
    <h1>Analytics</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="analytics.php">Home</a></li>
        <li class="breadcrumb-item active">Analytics</li>
      </ol>
    </nav>
  </div>
  <div class="d-flex align-items-center">
    <select id="monthFilter" class="form-select me-2" style="width: auto;">
      <?php foreach ($available_months as $m): ?>
        <?php 
          $val = $m['year'] . '-' . str_pad($m['month'], 2, '0', STR_PAD_LEFT);
          $selected = ($m['month'] == $current_month && $m['year'] == $current_year) ? 'selected' : '';
          $monthName = date("F Y", mktime(0, 0, 0, $m['month'], 1, $m['year']));
        ?>
        <option value="<?= $val ?>" <?= $selected ?>><?= $monthName ?></option>
      <?php endforeach; ?>
      <?php if (empty($available_months)): ?>
        <option value="<?= date('Y-m') ?>"><?= date('F Y') ?></option>
      <?php endif; ?>
    </select>
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
            <h6 id="totalExpense">₹<?= number_format($total_expense, 2) ?></h6>
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
            <h6 id="totalIncome">₹<?= number_format($total_income, 2) ?></h6>
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
        <h5 class="card-title">Savings <span id="savingsPeriod">| This Month</span></h5>

        <div class="d-flex align-items-center">
          <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
            <i class="bi bi-piggy-bank"></i>
          </div>
          <div class="ps-3">
            <h6 id="totalSavings">₹<?= number_format($total_savings, 2) ?></h6>
          </div>
        </div>
      </div>

    </div>
  </div><!-- End Savings Card -->

  <!-- Category Summary Section -->
  <div class="col-12">
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Category Summary <span id="categoryPeriod">| This Month</span></h5>
            <div id="categoryCards" class="row">
                <!-- Data will be loaded here -->
                <div class="text-center p-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
  </div>

  <!-- Expense Report Chart -->
  <div class="col-8">
    <div class="card">

      <div class="card-body">
        <h5 class="card-title">Expense Report <span class="report-period">/This Month</span></h5>

        <!-- Line Chart -->
        <div id="reportsChart"></div>
        <!-- End Line Chart -->
      </div>
    </div>
  </div><!-- End Expense Report Chart -->

  <!-- Financial Overview Pie Chart -->
  <div class="col-4">
    <div class="card">
      <div class="card-body pb-0">
        <h5 class="card-title">Financial Overview <span id="financialPeriod">| This Month</span></h5>
        <div id="trafficChart" style="min-height: 360px;" class="echart"></div>
      </div>
    </div>
  </div><!-- End Financial Overview Pie Chart -->

      </div>

    </div>
  </div><!-- End Expense Report Chart -->

  

</div>
</section>

<script>
let reportsChart, trafficChart;

document.addEventListener("DOMContentLoaded", () => {
    // Initialize Expense Report Chart
    reportsChart = new ApexCharts(document.querySelector("#reportsChart"), {
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
    });
    reportsChart.render();

    // Initialize Pie Chart
    trafficChart = echarts.init(document.querySelector("#trafficChart"));
    trafficChart.setOption({
        tooltip: { trigger: 'item' },
        legend: { top: '5%', left: 'center' },
        series: [{
            name: 'Financial Overview',
            type: 'pie',
            radius: ['40%', '70%'],
            avoidLabelOverlap: false,
            label: { show: false, position: 'center' },
            emphasis: { label: { show: true, fontSize: '18', fontWeight: 'bold' } },
            labelLine: { show: false },
            data: [
                { value: <?= $total_income ?>, name: 'Income', itemStyle: { color: '#2eca6a' } },
                { value: <?= $total_expense ?>, name: 'Expense', itemStyle: { color: '#4154f1' } },
                { value: <?= $total_savings ?>, name: 'Savings', itemStyle: { color: '#ff771d' } }
            ]
        }]
    });

    // Initial Load for Category Cards
    updatePageData("<?= $current_year ?>-<?= $current_month ?>", false);

    // Handle Month Selection
    document.getElementById('monthFilter').addEventListener('change', function() {
        updatePageData(this.value, true);
    });
});

async function updatePageData(dateVal, updateAll = true) {
    const [year, month] = dateVal.split('-');
    const categoryContainer = document.getElementById('categoryCards');
    
    // Show Loading
    categoryContainer.innerHTML = `
        <div class="text-center p-5 w-100">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    `;

    try {
        const response = await fetch(`api/get_category_summary.php?month=${month}&year=${year}`);
        const data = await response.json();

        if (data.error) {
            console.error(data.error);
            return;
        }

        if (updateAll) {
            // Update Overall Cards
            document.getElementById('totalExpense').innerText = '₹' + data.total_expense.toLocaleString('en-IN', {minimumFractionDigits: 2});
            document.getElementById('totalIncome').innerText = '₹' + data.total_income.toLocaleString('en-IN', {minimumFractionDigits: 2});
            document.getElementById('totalSavings').innerText = '₹' + data.total_savings.toLocaleString('en-IN', {minimumFractionDigits: 2});
            
            const periodSpan = `| ${document.querySelector('#monthFilter option:checked').text}`;
            document.querySelector('.sales-card span').innerText = periodSpan;
            document.querySelector('.revenue-card span').innerText = periodSpan;
            document.getElementById('savingsPeriod').innerText = periodSpan;
            document.getElementById('categoryPeriod').innerText = periodSpan;
            const financialPeriod = document.getElementById('financialPeriod');
            if (financialPeriod) financialPeriod.innerText = periodSpan;
            const reportPeriod = document.querySelector('.card-title span[class*="report-period"]');
            if (reportPeriod) reportPeriod.innerText = periodSpan;

            // Update Charts
            updateCharts(data);
        }

        // Update Category Cards (always update)
        categoryContainer.innerHTML = '';
        if (data.categories.length === 0) {
            categoryContainer.innerHTML = '<div class="col-12 text-center p-3">No expenses found for this month.</div>';
        } else {
            data.categories.forEach(cat => {
                const card = `
                    <div class="col-xxl-3 col-md-4">
                        <div class="card info-card sales-card">
                            <div class="card-body">
                                <h5 class="card-title">${cat.name}</h5>
                                <div class="d-flex align-items-center">
                                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                        <i class="bi ${cat.icon || 'bi-tag'}"></i>
                                    </div>
                                    <div class="ps-3">
                                        <h6>₹${cat.total.toLocaleString('en-IN', {minimumFractionDigits: 2})}</h6>
                                        <!-- <p class="text-muted small mb-0">Summary of '${cat.name}' category is ₹${cat.total.toLocaleString('en-IN', {minimumFractionDigits: 2})}</p> -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                categoryContainer.insertAdjacentHTML('beforeend', card);
            });
        }

    } catch (error) {
        console.error('Error fetching data:', error);
        categoryContainer.innerHTML = '<div class="col-12 text-center text-danger p-3">Failed to load data.</div>';
    }
}

function updateCharts(data) {
    if (reportsChart) {
        reportsChart.updateOptions({
            series: [{
                data: data.chart.amounts
            }],
            xaxis: {
                categories: data.chart.dates
            }
        });
    }

    if (trafficChart) {
        trafficChart.setOption({
            series: [{
                data: [
                    { value: data.total_income, name: 'Income', itemStyle: { color: '#2eca6a' } },
                    { value: data.total_expense, name: 'Expense', itemStyle: { color: '#4154f1' } },
                    { value: data.total_savings, name: 'Savings', itemStyle: { color: '#ff771d' } }
                ]
            }]
        });
    }
}
</script>

<?php include "footer.php"; ?>