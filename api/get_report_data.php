<?php
include "../config/db.php";

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$user_id = $_SESSION['user_id'];
$type = isset($_GET['type']) ? $_GET['type'] : 'expense';
$date_val = isset($_GET['date']) ? $_GET['date'] : '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

if (!$date_val) {
    echo json_encode(['error' => 'Date missing']);
    exit;
}

list($year, $month) = explode('-', $date_val);

$table = ($type === 'income') ? 'incomes' : 'expenses';
$date_col = ($type === 'income') ? 'income_date' : 'expense_date';

// For Expenses, we might want to join categories
if ($type === 'expense') {
    $count_query = "SELECT COUNT(*) as total FROM expenses WHERE user_id='$user_id' AND MONTH(expense_date) = '$month' AND YEAR(expense_date) = '$year'";
    $data_query = "SELECT e.*, c.name as category_name FROM expenses e 
                   LEFT JOIN categories c ON e.category_id = c.id 
                   WHERE e.user_id='$user_id' AND MONTH(e.expense_date) = '$month' AND YEAR(e.expense_date) = '$year' 
                   ORDER BY e.expense_date DESC LIMIT $offset, $limit";
} else {
    $count_query = "SELECT COUNT(*) as total FROM incomes WHERE user_id='$user_id' AND MONTH(income_date) = '$month' AND YEAR(income_date) = '$year'";
    $data_query = "SELECT * FROM incomes WHERE user_id='$user_id' AND MONTH(income_date) = '$month' AND YEAR(income_date) = '$year' 
                   ORDER BY income_date DESC LIMIT $offset, $limit";
}

$count_result = mysqli_query($conn, $count_query);
$total_rows = mysqli_fetch_assoc($count_result)['total'];
$total_pages = ceil($total_rows / $limit);

$data_result = mysqli_query($conn, $data_query);
$records = [];
while ($row = mysqli_fetch_assoc($data_result)) {
    $records[] = $row;
}

echo json_encode([
    'records' => $records,
    'total_pages' => $total_pages,
    'current_page' => $page,
    'total_records' => (int)$total_rows
]);
