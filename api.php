<?php
require 'config.php';
header('Content-Type: application/json');

// --- 1. SECURITY & VALIDATION ---
if (!isset($_GET['v']) || !isset($_GET['i'])) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Missing parameters"]);
    exit;
}

$v = floatval($_GET['v']); // Sanitasi input
$i = floatval($_GET['i']);
$p = $v * $i;
$ip = $_SERVER['REMOTE_ADDR'];
$loc = isset($_GET['loc']) ? mysqli_real_escape_string($conn, $_GET['loc']) : 'Server Rack A';

// --- 2. LOGIC INSERT ---
$sql = "INSERT INTO sensor_logs (tegangan, arus, daya, ip_address, lokasi) VALUES ('$v', '$i', '$p', '$ip', '$loc')";

if(mysqli_query($conn, $sql)) {
    // --- 3. THRESHOLD CHECKING (FITUR SETTINGS) ---
    // Ambil limit dari DB
    $q_lim = mysqli_query($conn, "SELECT setting_value FROM settings WHERE setting_key='voltage_limit_high'");
    $limit = mysqli_fetch_assoc($q_lim)['setting_value'] ?? 240;

    if ($v > $limit) {
        // Auto-Generate Alert
        $msg = "CRITICAL: Overvoltage detected ($v V) at $loc";
        mysqli_query($conn, "INSERT INTO system_alerts (tipe, pesan) VALUES ('Danger', '$msg')");
    }

    echo json_encode([
        "status" => "success", 
        "data" => [
            "voltage" => $v,
            "power" => $p,
            "timestamp" => date('Y-m-d H:i:s')
        ]
    ]);
} else {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => mysqli_error($conn)]);
}
?>