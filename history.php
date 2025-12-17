<?php
session_start();
include 'config.php';

// Cek Login
if (!isset($_SESSION['login_status'])) {
    header("location:login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>History Logs</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
</head>
<body>
    <div class="crt-overlay"></div>
    <div class="sys-loader" id="loader"><div class="spinner"></div></div>

    <div class="layout-wrapper">
        <aside class="sidebar">
            <div class="brand">
                <i class="ph-fill ph-lightning" style="font-size: 32px; color: var(--neon-yellow);"></i>
                <div class="brand-text">Volt<span class="text-neon">Control</span></div>
            </div>
            <nav>
                <ul class="nav-links">
                    <li class="nav-item"><a href="dashboard.php" class="nav-btn"><i class="ph-bold ph-squares-four"></i> Dashboard</a></li>
                    <li class="nav-item"><a href="control.php" class="nav-btn"><i class="ph-bold ph-sliders-horizontal"></i> Device Control</a></li>
                    <li class="nav-item"><a href="history.php" class="nav-btn active"><i class="ph-bold ph-clock-counter-clockwise"></i> History Logs</a></li>
                    <li class="nav-item"><a href="settings.php" class="nav-btn"><i class="ph-bold ph-gear"></i> System Config</a></li>
                </ul>
            </nav>
            <div style="margin-top: auto;">
                <a href="logout.php" class="nav-btn" style="color: var(--neon-red);"><i class="ph-bold ph-sign-out"></i> LOGOUT</a>
            </div>
        </aside>

        <main class="main-content">
            <h2 class="glitch-text" data-text="HISTORY LOGS" style="margin-bottom: 30px;">HISTORY LOGS</h2>
            
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>LOG ID</th>
                            <th>DATA READING (Volt / Amp / Watt)</th>
                            <th>TIMESTAMP</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        // Query mengambil data terbaru
                        $q = mysqli_query($koneksi, "SELECT * FROM sensor_logs ORDER BY id DESC LIMIT 20");
                        
                        if (mysqli_num_rows($q) > 0) {
                            while($row = mysqli_fetch_array($q)){
                                // --- PERBAIKAN DI SINI: MENYESUAIKAN NAMA KOLOM DATABASE ---
                                // Menggunakan fallback (tanda ??) agar tidak error jika kolom kosong
                                $waktu = isset($row['waktu']) ? $row['waktu'] : (isset($row['timestamp']) ? $row['timestamp'] : '-');
                                
                                // Deteksi kolom mana yang ada (tegangan/arus/value)
                                $reading = "";
                                if(isset($row['tegangan'])) {
                                    $reading = "Voltage: " . $row['tegangan'] . "V | Current: " . $row['arus'] . "A";
                                } elseif (isset($row['value'])) {
                                    $reading = "Sensor Value: " . $row['value'];
                                } else {
                                    $reading = "No Data Reading";
                                }
                        ?>
                        <tr>
                            <td>#LOG-<?= $row['id']; ?></td>
                            <td class="text-neon"><?= $reading; ?></td>
                            <td><?= $waktu; ?></td>
                        </tr>
                        <?php 
                            }
                        } else {
                            echo "<tr><td colspan='3' style='text-align:center; padding:20px;'>No logs found in database.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <script>
        window.addEventListener('load', () => {
            const loader = document.getElementById('loader');
            loader.style.opacity = '0';
            setTimeout(() => { loader.style.display = 'none'; }, 500);
        });
    </script>
</body>
</html>