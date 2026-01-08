<?php
include "../config/db.php";

$id = $_GET['id'];

// Optional safety: prevent delete if category has expenses
$check = mysqli_query($conn,
    "SELECT id FROM expenses WHERE category_id=$id");

if (mysqli_num_rows($check) == 0) {
    mysqli_query($conn, "DELETE FROM categories WHERE id=$id");
}

header("Location: index.php");
exit;
?>