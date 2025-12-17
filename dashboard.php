<?php
session_start();
include 'config.php';

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
    <title>Dashboard :: VoltControl</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

    <div class="crt-overlay"></div>

    <div class="sys-loader" id="loader">
        <div class="spinner"></div>
        <p style="margin-top: 15px; font-family: 'Share Tech Mono'; color: var(--neon-blue); letter-spacing: 2px;">
            LOADING SYSTEM MODULES...
        </p>
    </div>

    <div class="layout-wrapper">
        <aside class="sidebar">
            <div class="brand">
                <i class="ph-fill ph-lightning" style="font-size: 32px; color: var(--neon-yellow);"></i>
                <div class="brand-text">Volt<span class="text-neon">Control</span></div>
            </div>

            <nav>
                <ul class="nav-links">
                    <li class="nav-item">
                        <a href="dashboard.php" class="nav-btn active">
                            <i class="ph-bold ph-squares-four"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="control.php" class="nav-btn">
                            <i class="ph-bold ph-sliders-horizontal"></i> Device Control
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="history.php" class="nav-btn">
                            <i class="ph-bold ph-clock-counter-clockwise"></i> History Logs
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="settings.php" class="nav-btn">
                            <i class="ph-bold ph-gear"></i> System Config
                        </a>
                    </li>
                </ul>
            </nav>

            <div style="margin-top: auto;">
                <a href="logout.php" class="nav-btn" style="color: var(--neon-red);">
                    <i class="ph-bold ph-sign-out"></i> LOGOUT SESSION
                </a>
            </div>
        </aside>

        <main class="main-content">
            <header style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 40px;">
                <div>
                    <h2 class="glitch-text" data-text="SYSTEM OVERVIEW" style="font-size: 1.8rem;">SYSTEM OVERVIEW</h2>
                    <p class="text-neon" style="font-family: 'Share Tech Mono';">Realtime Monitoring Panel // User: <?= $_SESSION['username']; ?></p>
                </div>
                <div style="text-align: right;">
                    <div style="font-size: 2rem; font-weight: 700;" id="clock">00:00:00</div>
                    <div style="color: var(--neon-green); font-size: 0.8rem;">● SYSTEM OPERATIONAL</div>
                </div>
            </header>

            <div class="grid-stats">
                <div class="stat-card">
                    <div class="stat-label">Voltage Input</div>
                    <div class="stat-val text-neon" id="val-volt">220 V</div>
                    <i class="ph-fill ph-lightning" style="position: absolute; right: 20px; bottom: 20px; font-size: 40px; opacity: 0.2;"></i>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Current Load</div>
                    <div class="stat-val text-warn" id="val-amp">5.4 A</div>
                    <i class="ph-fill ph-plugs" style="position: absolute; right: 20px; bottom: 20px; font-size: 40px; opacity: 0.2;"></i>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Power Usage</div>
                    <div class="stat-val" style="color: #fff;" id="val-watt">1180 W</div>
                    <i class="ph-fill ph-activity" style="position: absolute; right: 20px; bottom: 20px; font-size: 40px; opacity: 0.2;"></i>
                </div>
            </div>

            <div style="background: rgba(16, 33, 54, 0.4); border: 1px solid #1c2b3e; padding: 20px; border-radius: 12px; height: 350px; margin-bottom: 30px;">
                <h4 style="margin-bottom: 15px; color: var(--text-muted);">POWER CONSUMPTION TREND</h4>
                <canvas id="realtimeChart"></canvas>
            </div>

        </main>
    </div>

    <script>
        // 1. Remove Loader after 1.5s
        window.addEventListener('load', () => {
            setTimeout(() => {
                const loader = document.getElementById('loader');
                loader.style.opacity = '0';
                setTimeout(() => { loader.style.display = 'none'; }, 500);
            }, 1000);
        });

        // 2. Digital Clock
        setInterval(() => {
            const now = new Date();
            document.getElementById('clock').innerText = now.toLocaleTimeString();
        }, 1000);

        // 3. Chart.js Setup
        const ctx = document.getElementById('realtimeChart').getContext('2d');
        const gradient = ctx.createLinearGradient(0, 0, 0, 400);
        gradient.addColorStop(0, 'rgba(0, 243, 255, 0.4)');
        gradient.addColorStop(1, 'rgba(0, 243, 255, 0)');

        const myChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: [],
                datasets: [{
                    label: 'Voltage (V)',
                    data: [],
                    borderColor: '#00f3ff',
                    backgroundColor: gradient,
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: { grid: { color: 'rgba(255,255,255,0.05)' }, ticks: { color: '#627d98' } },
                    y: { grid: { color: 'rgba(255,255,255,0.05)' }, ticks: { color: '#627d98' } }
                },
                plugins: { legend: { display: false } },
                animation: { duration: 0 } // Matikan animasi chart agar mulus saat update realtime
            }
        });

        // 4. Simulasi Realtime Update
        setInterval(() => {
            const now = new Date();
            const timeStr = now.getHours() + ':' + now.getMinutes() + ':' + now.getSeconds();
            
            // Random Value Simulation
            const volt = Math.floor(Math.random() * (225 - 215 + 1)) + 215;
            const amp = (Math.random() * (6 - 4) + 4).toFixed(1);
            const watt = Math.floor(volt * amp);

            // Update DOM
            document.getElementById('val-volt').innerText = volt + " V";
            document.getElementById('val-amp').innerText = amp + " A";
            document.getElementById('val-watt').innerText = watt + " W";

            // Update Chart
            if (myChart.data.labels.length > 20) {
                myChart.data.labels.shift();
                myChart.data.datasets[0].data.shift();
            }
            myChart.data.labels.push(timeStr);
            myChart.data.datasets[0].data.push(volt);
            myChart.update();

        }, 1000);
    </script>
</body>
</html>