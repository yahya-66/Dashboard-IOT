<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Sesuaikan username dengan pythonuser atau iotuser yang aktif
$conn = new mysqli("localhost", "pythonuser", "Yahya18+", "gas_db");

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

$filename = "riwayat_log_gas_" . date('Y-m-d_H-i-s') . ".csv";

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=' . $filename);

$output = fopen('php://output', 'w');
fputcsv($output, array('ID', 'Nilai Gas', 'Status', 'Waktu Deteksi'));

$result = $conn->query("SELECT id, gas_value, status, created_at FROM gas_logs ORDER BY id DESC");

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        fputcsv($output, $row);
    }
}

fclose($output);
$conn->close();
exit();
?>
