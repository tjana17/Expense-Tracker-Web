<?php
include "../config/db.php";

if (!isset($_SESSION['user_id'])) {
    die("Unauthorized");
}

$user_id = $_SESSION['user_id'];
$type = isset($_GET['type']) ? $_GET['type'] : 'expense';
$date_val = isset($_GET['date']) ? $_GET['date'] : '';

if (!$date_val) {
    die("Date missing");
}

list($year, $month) = explode('-', $date_val);

$table = ($type === 'income') ? 'incomes' : 'expenses';
$date_col = ($type === 'income') ? 'income_date' : 'expense_date';

if ($type === 'expense') {
    $query = "SELECT e.amount, c.name as category, e.expense_date as date, e.note 
              FROM expenses e 
              LEFT JOIN categories c ON e.category_id = c.id 
              WHERE e.user_id='$user_id' AND MONTH(e.expense_date) = '$month' AND YEAR(e.expense_date) = '$year' 
              ORDER BY e.expense_date DESC";
    $headers = ['Amount', 'Category', 'Date', 'Note'];
} else {
    $query = "SELECT amount, income_date as date, note 
              FROM incomes 
              WHERE user_id='$user_id' AND MONTH(income_date) = '$month' AND YEAR(income_date) = '$year' 
              ORDER BY income_date DESC";
    $headers = ['Amount', 'Date', 'Note'];
}

$result = mysqli_query($conn, $query);

$filename = "report_" . $type . "_" . $date_val . ".csv";

header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="' . $filename . '";');

$output = fopen('php://output', 'w');
fputcsv($output, $headers);

while ($row = mysqli_fetch_assoc($result)) {
    fputcsv($output, $row);
}

fclose($output);
exit;
