<?php
require 'config.php';
header('Content-Type: application/json');

// Ambil Sensor Terakhir
$q_sensor = mysqli_query($conn, "SELECT * FROM sensor_logs ORDER BY id DESC LIMIT 1");
$sensor = mysqli_fetch_assoc($q_sensor);

// Ambil Status Relay
$q_relay = mysqli_query($conn, "SELECT relay_status FROM device_control WHERE id=1");
$relay = mysqli_fetch_assoc($q_relay);

// Gabungkan jadi satu JSON
$response = [
    'tegangan' => $sensor['tegangan'],
    'arus' => $sensor['arus'],
    'daya' => $sensor['daya'],
    'relay' => $relay['relay_status']
];

echo json_encode($response);
?>