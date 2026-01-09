<?php
include "../config/db.php";

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$user_id = $_SESSION['user_id'];
$month = isset($_GET['month']) ? $_GET['month'] : date('m');
$year = isset($_GET['year']) ? $_GET['year'] : date('Y');

// Total Expenses
$expense_query = mysqli_query($conn, 
  "SELECT SUM(amount) AS total FROM expenses 
   WHERE user_id='$user_id' 
   AND MONTH(expense_date) = '$month' 
   AND YEAR(expense_date) = '$year'");
$expense_data = mysqli_fetch_assoc($expense_query);
$total_expense = (float)($expense_data['total'] ?? 0);

// Total Income
$income_query = mysqli_query($conn, 
  "SELECT SUM(amount) AS total FROM incomes 
   WHERE user_id='$user_id' 
   AND MONTH(income_date) = '$month' 
   AND YEAR(income_date) = '$year'");
$income_data = mysqli_fetch_assoc($income_query);
$total_income = (float)($income_data['total'] ?? 0);

// Total Savings
$total_savings = $total_income - $total_expense;

// Daily Expenses for Chart
$chart_query = mysqli_query($conn, 
  "SELECT DATE(expense_date) as date, SUM(amount) as total 
   FROM expenses 
   WHERE user_id='$user_id' 
   AND MONTH(expense_date) = '$month' 
   AND YEAR(expense_date) = '$year' 
   GROUP BY DATE(expense_date) 
   ORDER BY DATE(expense_date)");

$dates = [];
$amounts = [];
while ($row = mysqli_fetch_assoc($chart_query)) {
    $dates[] = $row['date'];
    $amounts[] = (float)$row['total'];
}

// Category Summary
$category_query = mysqli_query($conn, 
  "SELECT c.name, c.icon, SUM(e.amount) as total 
   FROM expenses e 
   JOIN categories c ON e.category_id = c.id 
   WHERE e.user_id='$user_id' 
   AND MONTH(e.expense_date) = '$month' 
   AND YEAR(e.expense_date) = '$year' 
   GROUP BY e.category_id");

$categories = [];
while ($row = mysqli_fetch_assoc($category_query)) {
    $categories[] = [
        'name' => $row['name'],
        'icon' => $row['icon'],
        'total' => (float)$row['total']
    ];
}

echo json_encode([
    'total_expense' => $total_expense,
    'total_income' => $total_income,
    'total_savings' => $total_savings,
    'chart' => [
        'dates' => $dates,
        'amounts' => $amounts
    ],
    'categories' => $categories
]);
