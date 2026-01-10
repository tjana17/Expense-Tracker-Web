<?php
include "../config/db.php";

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$user_id = $_SESSION['user_id'];
$type = isset($_GET['type']) ? $_GET['type'] : 'expense';

$table = ($type === 'income') ? 'incomes' : 'expenses';
$date_col = ($type === 'income') ? 'income_date' : 'expense_date';

$query = mysqli_query($conn, 
  "SELECT DISTINCT MONTH($date_col) as month, YEAR($date_col) as year 
   FROM $table 
   WHERE user_id='$user_id' 
   ORDER BY year DESC, month DESC");

$options = [];
while ($row = mysqli_fetch_assoc($query)) {
    $monthName = date("F Y", mktime(0, 0, 0, $row['month'], 1, $row['year']));
    $options[] = [
        'value' => $row['year'] . '-' . str_pad($row['month'], 2, '0', STR_PAD_LEFT),
        'label' => $monthName
    ];
}

echo json_encode(['options' => $options]);
