<?php

$conn = mysqli_connect(
    "127.0.0.1",
    "admin",
    "password123",
    "labserver"
);

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

?>
