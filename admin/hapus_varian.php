
<?php
session_start();
include('../config/db.php');

if ($_SESSION['role'] != 'admin') {
    header('Location: ../auth/login.php');
}

$id = $_GET['id'];
mysqli_query($conn, "DELETE FROM varian_ps WHERE id=$id");

header('Location: varian.php');
?>
