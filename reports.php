<?php include "header.php"; ?>

<div class="pagetitle d-flex justify-content-between align-items-center">
  <div>
    <h1>Reports</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
        <li class="breadcrumb-item active">Reports</li>
      </ol>
    </nav>
  </div>
  <div>
    <a href="expenses/add.php" class="btn btn-primary">+ Add Expense</a>
    <a href="income/add.php" class="btn btn-success ms-2">+ Add Income</a>
  </div>
</div><!-- End Page Title -->

<section class="section">
  <div class="row">
    <div class="col-lg-12">
      <div class="card">
        <div class="card-body">
          <h5 class="card-title">Generate Reports</h5>

          <!-- Filters Row -->
          <div class="row g-3 mb-4">
            <div class="col-md-4">
              <label class="form-label">Select Type</label>
              <select id="reportType" class="form-select">
                <option value="">Choose...</option>
                <option value="income">Income</option>
                <option value="expense">Expense</option>
              </select>
            </div>
            <div class="col-md-4" id="monthFilterContainer" style="display: none;">
              <label class="form-label">Select Month</label>
              <select id="reportMonth" class="form-select">
                <!-- Populated via AJAX -->
              </select>
            </div>
            <div class="col-md-4 d-flex align-items-end">
              <button id="downloadBtn" class="btn btn-outline-primary" style="display: none;">
                <i class="bi bi-download me-1"></i> Download CSV
              </button>
            </div>
          </div>

          <!-- Table Container -->
          <div id="reportResults" style="display: none;">
            <div class="table-responsive">
              <table class="table table-hover table-striped">
                <thead id="tableHead"></thead>
                <tbody id="tableBody"></tbody>
              </table>
            </div>

            <!-- Pagination -->
            <nav aria-label="Page navigation" class="mt-4">
              <ul class="pagination justify-content-center" id="pagination">
                <!-- Populated via JS -->
              </ul>
            </nav>
            <div class="text-center text-muted small mt-2" id="recordCount"></div>
          </div>

          <!-- Loading Spinner -->
          <div id="loading" class="text-center p-5" style="display: none;">
            <div class="spinner-border text-primary" role="status">
              <span class="visually-hidden">Loading...</span>
            </div>
          </div>

          <!-- Empty State -->
          <div id="noData" class="text-center p-5 text-muted" style="display: none;">
            No records found for the selected criteria.
          </div>

        </div>
      </div>
    </div>
  </div>
</section>

<script>
let currentPage = 1;

document.addEventListener("DOMContentLoaded", () => {
    const typeSelect = document.getElementById('reportType');
    const monthSelect = document.getElementById('reportMonth');
    const monthContainer = document.getElementById('monthFilterContainer');
    const downloadBtn = document.getElementById('downloadBtn');

    // Handle Type Selection
    typeSelect.addEventListener('change', async function() {
        const type = this.value;
        monthContainer.style.display = 'none';
        downloadBtn.style.display = 'none';
        document.getElementById('reportResults').style.display = 'none';
        document.getElementById('noData').style.display = 'none';

        if (type) {
            try {
                const response = await fetch(`api/get_report_options.php?type=${type}`);
                const data = await response.json();
                
                monthSelect.innerHTML = '<option value="">Select Month</option>';
                data.options.forEach(opt => {
                    monthSelect.innerHTML += `<option value="${opt.value}">${opt.label}</option>`;
                });
                
                monthContainer.style.display = 'block';
            } catch (error) {
                console.error('Error fetching options:', error);
            }
        }
    });

    // Handle Month Selection
    monthSelect.addEventListener('change', () => {
        if (monthSelect.value) {
            currentPage = 1;
            fetchReportData();
            downloadBtn.style.display = 'inline-block';
        } else {
            document.getElementById('reportResults').style.display = 'none';
            downloadBtn.style.display = 'none';
        }
    });

    // Handle Download
    downloadBtn.addEventListener('click', () => {
        const type = typeSelect.value;
        const date = monthSelect.value;
        window.location.href = `api/export_report.php?type=${type}&date=${date}`;
    });
});

async function fetchReportData(page = 1) {
    const type = document.getElementById('reportType').value;
    const date = document.getElementById('reportMonth').value;
    const resultsDiv = document.getElementById('reportResults');
    const loading = document.getElementById('loading');
    const noData = document.getElementById('noData');

    resultsDiv.style.display = 'none';
    noData.style.display = 'none';
    loading.style.display = 'block';

    try {
        const response = await fetch(`api/get_report_data.php?type=${type}&date=${date}&page=${page}`);
        const data = await response.json();

        loading.style.display = 'none';

        if (data.records.length > 0) {
            renderTable(type, data.records);
            renderPagination(data.total_pages, data.current_page);
            document.getElementById('recordCount').innerText = `Total Records: ${data.total_records}`;
            resultsDiv.style.display = 'block';
        } else {
            noData.style.display = 'block';
        }
    } catch (error) {
        console.error('Error fetching data:', error);
        loading.style.display = 'none';
    }
}

function renderTable(type, records) {
    const head = document.getElementById('tableHead');
    const body = document.getElementById('tableBody');

    if (type === 'expense') {
        head.innerHTML = `
            <tr>
                <th>Date</th>
                <th>Category</th>
                <th>Amount</th>
                <th>Note</th>
            </tr>
        `;
        body.innerHTML = records.map(r => `
            <tr>
                <td>${r.expense_date}</td>
                <td><span class="badge bg-primary">${r.category_name || 'N/A'}</span></td>
                <td class="fw-bold">₹${parseFloat(r.amount).toLocaleString('en-IN', {minimumFractionDigits: 2})}</td>
                <td><span class="text-muted small">${r.note || '-'}</span></td>
            </tr>
        `).join('');
    } else {
        head.innerHTML = `
            <tr>
                <th>Date</th>
                <th>Amount</th>
                <th>Note</th>
            </tr>
        `;
        body.innerHTML = records.map(r => `
            <tr>
                <td>${r.income_date}</td>
                <td class="fw-bold text-success">₹${parseFloat(r.amount).toLocaleString('en-IN', {minimumFractionDigits: 2})}</td>
                <td><span class="text-muted small">${r.note || '-'}</span></td>
            </tr>
        `).join('');
    }
}

function renderPagination(totalPages, currentPage) {
    const pagination = document.getElementById('pagination');
    pagination.innerHTML = '';

    if (totalPages <= 1) return;

    // Previous
    pagination.innerHTML += `
        <li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
            <a class="page-link" href="#" onclick="fetchReportData(${currentPage - 1}); return false;">Previous</a>
        </li>
    `;

    // Pages
    for (let i = 1; i <= totalPages; i++) {
        pagination.innerHTML += `
            <li class="page-item ${currentPage === i ? 'active' : ''}">
                <a class="page-link" href="#" onclick="fetchReportData(${i}); return false;">${i}</a>
            </li>
        `;
    }

    // Next
    pagination.innerHTML += `
        <li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
            <a class="page-link" href="#" onclick="fetchReportData(${currentPage + 1}); return false;">Next</a>
        </li>
    `;
}
</script>

<?php include "footer.php"; ?>