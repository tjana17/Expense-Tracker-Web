<?php
$conn = mysqli_connect("localhost", "root", "root", "expense_tracker");
date_default_timezone_set('Asia/Kolkata');

if (!$conn) {
    die("Database connection failed");
}
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
