<?php
session_start();
include 'config.php';

if (!isset($_SESSION['login_status'])) { header("location:login.php"); exit; }

$msg = "";
// LOGIKA SIMPAN SETTINGS
if (isset($_POST['save_settings'])) {
    foreach($_POST as $key => $val) {
        if($key == 'save_settings') continue;
        $val = mysqli_real_escape_string($koneksi, $val);
        $key = mysqli_real_escape_string($koneksi, $key);
        
        // Cek apakah key sudah ada
        $check = mysqli_query($koneksi, "SELECT * FROM settings WHERE setting_key='$key'");
        if(mysqli_num_rows($check) > 0){
            mysqli_query($koneksi, "UPDATE settings SET setting_value='$val' WHERE setting_key='$key'");
        } else {
            mysqli_query($koneksi, "INSERT INTO settings (setting_key, setting_value) VALUES ('$key', '$val')");
        }
    }
    $msg = "System configuration updated successfully.";
}

// Ambil Data Setting
$s = [];
$q = mysqli_query($koneksi, "SELECT * FROM settings");
while($r = mysqli_fetch_assoc($q)) { $s[$r['setting_key']] = $r['setting_value']; }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Settings</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <style>
        .form-group { margin-bottom: 20px; }
        .form-label { display: block; color: var(--neon-blue); margin-bottom: 8px; font-size: 0.9rem; letter-spacing: 1px; }
        .setting-input {
            width: 100%; padding: 12px 15px; background: rgba(0, 0, 0, 0.4);
            border: 1px solid #1f2f46; border-radius: 4px; color: #fff; font-size: 1rem; transition: 0.3s;
        }
        .setting-input:focus { outline: none; border-color: var(--neon-blue); box-shadow: 0 0 15px var(--neon-blue-dim); }
    </style>
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
                    <li class="nav-item"><a href="history.php" class="nav-btn"><i class="ph-bold ph-clock-counter-clockwise"></i> History Logs</a></li>
                    <li class="nav-item"><a href="settings.php" class="nav-btn active"><i class="ph-bold ph-gear"></i> System Config</a></li>
                </ul>
            </nav>
            <div style="margin-top: auto;">
                <a href="logout.php" class="nav-btn" style="color: var(--neon-red);"><i class="ph-bold ph-sign-out"></i> LOGOUT</a>
            </div>
        </aside>

        <main class="main-content">
            <h2 class="glitch-text" data-text="SYSTEM CONFIG" style="margin-bottom: 30px;">SYSTEM CONFIG</h2>
            
            <?php if(!empty($msg)): ?>
                <div class="alert alert-warning" style="border-color: var(--neon-green); color: var(--neon-green); background: rgba(5, 255, 161, 0.1);">
                    <i class="ph-bold ph-check-circle"></i> <?= $msg; ?>
                </div>
            <?php endif; ?>

            <div style="background: rgba(16, 33, 54, 0.4); border: 1px solid #1c2b3e; border-radius: 12px; padding: 30px;">
                <form method="POST">
                    <div class="form-group">
                        <label class="form-label">APPLICATION NAME</label>
                        <input type="text" name="app_name" class="setting-input" value="<?= $s['app_name'] ?? 'VoltControl Enterprise' ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label">MAX VOLTAGE (V)</label>
                        <input type="number" name="voltage_limit_high" class="setting-input" value="<?= $s['voltage_limit_high'] ?? 240 ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label">TARIFF (IDR / kWh)</label>
                        <input type="number" name="tarif_per_kwh" class="setting-input" value="<?= $s['tarif_per_kwh'] ?? 1444 ?>">
                    </div>

                    <div style="margin-top: 30px; border-top: 1px solid #1c2b3e; padding-top: 20px;">
                        <button type="submit" name="save_settings" class="btn-main" style="width: 200px;">SAVE CONFIG</button>
                    </div>
                </form>
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