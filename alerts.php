<?php
session_start();
require 'config.php';
if (!isset($_SESSION['login'])) { header("Location: login.php"); exit; }

$query = mysqli_query($conn, "SELECT * FROM system_alerts ORDER BY waktu DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>Alerts - VoltControl</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="sidebar">
    <div class="brand"><i class="fas fa-bolt" style="color:var(--accent)"></i> VoltControl</div>
    <div class="menu">
        <a href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
        <a href="history.php"><i class="fas fa-history"></i> Data History</a>
        <a href="alerts.php" class="active"><i class="fas fa-bell"></i> System Alerts</a>
        <a href="settings.php"><i class="fas fa-cog"></i> Settings</a>
        <a href="logout.php" class="logout"><i class="fas fa-sign-out-alt"></i> Sign out</a>
    </div>
</div>

<div class="main-content">
    <div class="header" style="margin-bottom:30px;">
        <h2 style="margin:0;">System Alerts</h2>
        <small style="color:#8b949e">Notifikasi dan peringatan sistem</small>
    </div>

    <table>
        <thead>
            <tr>
                <th width="20%">Waktu</th>
                <th width="15%">Level</th>
                <th>Pesan Sistem</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = mysqli_fetch_assoc($query)): 
                $badge = "bg-info";
                if($row['tipe'] == 'Warning') $badge = "bg-warn";
                if($row['tipe'] == 'Danger') $badge = "bg-danger";
            ?>
            <tr>
                <td><?= $row['waktu'] ?></td>
                <td><span class="badge <?= $badge ?>"><?= $row['tipe'] ?></span></td>
                <td><?= $row['pesan'] ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
</body>
</html>