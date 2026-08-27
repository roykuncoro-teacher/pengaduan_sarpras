<?php
session_start();
include 'koneksi.php';

if (isset($_POST['login'])) {
    $username = mysqli_real_escape_string($koneksi, $_POST['Username']);
    $password = mysqli_real_escape_string($koneksi, $_POST['password']);

    $query  = "SELECT * FROM Admin WHERE Username='$username' AND password='$password'";
    $result = mysqli_query($koneksi, $query);

    if (mysqli_num_rows($result) > 0) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_user'] = $username;
        header("Location: admin_dashboard.php");
    } else {
        echo "<script>alert('Username atau Password salah!'); window.location='login.php';</script>";
    }
}
?>