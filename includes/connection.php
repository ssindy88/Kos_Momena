<?php
$host     = "localhost";
$username = "root";       // Username default XAMPP
$password = "";           // Kosongkan untuk default XAMPP
$database = "kos_momena";   // Pastikan nama ini sama dengan di phpMyAdmin

$koneksi = mysqli_connect($host, $username, $password, $database);

if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
} 
?>