<?php
session_start();
include 'config.php'; 

if (!isset($_SESSION['login_status'])) {
    header("location:login.php");
    exit;
}

// LOGIK TOGGLE STATUS (ON/OFF)
if(isset($_GET['toggle_id'])){
    $id = $_GET['toggle_id'];
    $current_status = $_GET['current'];
    // Jika ON(1) jadi OFF(0), sebaliknya
    $new_status = ($current_status == 1) ? 0 : 1;
    
    mysqli_query($koneksi, "UPDATE device_control SET status='$new_status' WHERE id='$id'");
    header("location:control.php");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Device Control</title>
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
                    <li class="nav-item"><a href="control.php" class="nav-btn active"><i class="ph-bold ph-sliders-horizontal"></i> Device Control</a></li>
                    <li class="nav-item"><a href="history.php" class="nav-btn"><i class="ph-bold ph-clock-counter-clockwise"></i> History Logs</a></li>
                    <li class="nav-item"><a href="settings.php" class="nav-btn"><i class="ph-bold ph-gear"></i> System Config</a></li>
                </ul>
            </nav>
            <div style="margin-top: auto;">
                <a href="logout.php" class="nav-btn" style="color: var(--neon-red);"><i class="ph-bold ph-sign-out"></i> LOGOUT</a>
            </div>
        </aside>

        <main class="main-content">
            <h2 class="glitch-text" data-text="DEVICE CONTROL" style="margin-bottom: 30px;">DEVICE CONTROL</h2>
            
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>ID DEVICE</th>
                            <th>DEVICE NAME</th>
                            <th>STATUS</th>
                            <th>ACTION</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $q = mysqli_query($koneksi, "SELECT * FROM device_control");
                        if(mysqli_num_rows($q) > 0) {
                            while($row = mysqli_fetch_array($q)) {
                                $isOn = ($row['status'] == 1);
                                $statusClass = $isOn ? 'text-neon' : 'text-muted';
                                $statusText = $isOn ? 'ACTIVE (ON)' : 'OFFLINE (OFF)';
                                $btnText = $isOn ? 'TURN OFF' : 'TURN ON';
                                $btnColor = $isOn ? 'var(--neon-red)' : 'var(--neon-green)';
                        ?>
                        <tr>
                            <td>#DEV-<?= $row['id']; ?></td>
                            <td><?= $row['device_name']; ?></td>
                            <td class="<?= $statusClass; ?>" style="font-weight:bold;">
                                <?= $statusText; ?>
                            </td>
                            <td>
                                <a href="control.php?toggle_id=<?= $row['id']; ?>&current=<?= $row['status']; ?>" 
                                   class="btn-main" 
                                   style="width: auto; padding: 8px 20px; font-size: 0.8rem; text-decoration:none; border-color: <?= $btnColor ?>; color: <?= $btnColor ?>;">
                                   <?= $btnText; ?>
                                </a>
                            </td>
                        </tr>
                        <?php } 
                        } else { ?>
                            <tr><td colspan="4" style="text-align:center; padding:30px;">NO DEVICES FOUND</td></tr>
                        <?php } ?>
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