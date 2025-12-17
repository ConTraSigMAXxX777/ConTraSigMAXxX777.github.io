<?php
session_start();
include 'config.php';

// Inisialisasi counter gagal di sesi browser (untuk menghitung user asal)
if (!isset($_SESSION['global_fails'])) {
    $_SESSION['global_fails'] = 0;
}

$msg = "";
$msg_type = "";

// Cek apakah browser ini sudah terblokir secara sesi (sudah 3x salah input apapun)
if ($_SESSION['global_fails'] >= 3) {
    $msg = "AKSES DIBLOKIR: Anda telah mencoba 3x dengan data yang salah.";
    $msg_type = "danger";
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Hanya proses jika belum terblokir
    if ($_SESSION['global_fails'] < 3) {
        
        $username = mysqli_real_escape_string($koneksi, $_POST['username']);
        $password = mysqli_real_escape_string($koneksi, $_POST['password']);

        // 1. Cek Database
        $q = mysqli_query($koneksi, "SELECT * FROM users WHERE username = '$username'");
        $user = mysqli_fetch_assoc($q);

        // Variable penentu login sukses/gagal
        $login_berhasil = false;
        $akun_terblokir_db = false;

        if ($user) {
            // User Ada di DB
            if ($user['is_blocked'] == 1) {
                $akun_terblokir_db = true;
            } elseif ($password == $user['password']) {
                $login_berhasil = true;
            } else {
                // User ada, tapi Password Salah -> Update Counter DB
                $new_db_fails = $user['failed_attempts'] + 1;
                mysqli_query($koneksi, "UPDATE users SET failed_attempts = $new_db_fails WHERE id = {$user['id']}");
                
                if ($new_db_fails >= 3) {
                    mysqli_query($koneksi, "UPDATE users SET is_blocked = 1 WHERE id = {$user['id']}");
                }
            }
        } 
        // Jika User Tidak Ada, logika langsung jatuh ke 'else' di bawah (Login Gagal)

        // --- EKSEKUSI HASIL ---

        if ($akun_terblokir_db) {
            // Kasus A: User ada tapi sudah diblokir permanen di database
            $msg = "AKUN TERKUNCI: User ID ini telah diblokir permanen di sistem.";
            $msg_type = "danger";
            
        } elseif ($login_berhasil) {
            // Kasus B: Login Sukses
            // Reset semua counter
            $_SESSION['global_fails'] = 0; 
            mysqli_query($koneksi, "UPDATE users SET failed_attempts = 0 WHERE id = {$user['id']}");
            
            $_SESSION['login_status'] = true;
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'] ?? 'Admin';
            
            header("location:dashboard.php");
            exit;

        } else {
            // Kasus C: GAGAL (Entah itu User Asal, atau Password Salah)
            // Kita perlakukan SAMA -> Tambah Counter Session
            $_SESSION['global_fails']++;
            
            $limit = 3;
            $sisa = $limit - $_SESSION['global_fails'];

            if ($_SESSION['global_fails'] >= $limit) {
                $msg = "KEAMANAN: Batas percobaan habis (3x). Akses sementara ditutup!";
                $msg_type = "danger";
            } else {
                $msg = "Login Gagal (Username/Password Salah). Sisa percobaan: " . $sisa;
                $msg_type = "warning";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - VoltControl BETA</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <style>
        /* CSS KHUSUS AGAR INTERAKSI MOUSE TEMBUS KE CANVAS */
        .crt-overlay { pointer-events: none; } 
        .login-container { pointer-events: none; } 
        .login-panel { pointer-events: auto; } 
    </style>
</head>
<body>
    
    <div class="crt-overlay"></div>
    <canvas id="bg-canvas"></canvas>

    <div class="login-container">
        <div class="login-panel">
            <div class="login-header">
                <i class="ph-fill ph-circuitry"></i>
                <h1 class="glitch-text" data-text="VOLTCONTROL">VOLTCONTROL</h1>
                <p style="color: var(--neon-yellow); font-family: 'Share Tech Mono'; letter-spacing: 2px; margin-top: 5px;">BETA VERSION</p>
            </div>

            <?php if(!empty($msg)): ?>
                <div class="alert alert-<?= $msg_type; ?>">
                    <i class="ph-bold ph-warning-circle"></i> <span><?= $msg; ?></span>
                </div>
            <?php endif; ?>

            <form method="POST">
                <div class="input-group">
                    <i class="ph-bold ph-user input-icon"></i>
                    <input type="text" name="username" class="input-field" placeholder="OPERATOR ID" required autocomplete="off">
                </div>
                <div class="input-group">
                    <i class="ph-bold ph-lock-key input-icon"></i>
                    <input type="password" name="password" class="input-field" placeholder="ACCESS CODE" required>
                </div>
                <button type="submit" class="btn-main" <?= ($_SESSION['global_fails'] >= 3) ? 'disabled style="opacity:0.5; cursor:not-allowed;"' : '' ?>>
                    INITIATE SEQUENCE
                </button>
            </form>

            <div style="margin-top: 25px; text-align: center; font-size: 0.75rem; color: var(--text-muted);">
                SECURE CONNECTION // ENCRYPTED
            </div>
        </div>
    </div>

    <script>
        const canvas = document.getElementById('bg-canvas');
        const ctx = canvas.getContext('2d');
        
        let width, height, particles = [];
        let mouse = { x: null, y: null, radius: 200 };

        window.addEventListener('mousemove', (e) => {
            mouse.x = e.x;
            mouse.y = e.y;
        });

        function resize() {
            width = canvas.width = window.innerWidth;
            height = canvas.height = window.innerHeight;
        }
        window.addEventListener('resize', resize);
        resize();

        class Particle {
            constructor() {
                this.x = Math.random() * width;
                this.y = Math.random() * height;
                this.vx = (Math.random() - 0.5) * 1.5;
                this.vy = (Math.random() - 0.5) * 1.5;
                this.size = Math.random() * 2 + 1;
            }
            draw() {
                ctx.beginPath();
                ctx.arc(this.x, this.y, this.size, 0, Math.PI * 2);
                ctx.fillStyle = '#00f3ff';
                ctx.fill();
            }
            update() {
                this.x += this.vx;
                this.y += this.vy;

                if (this.x < 0 || this.x > width) this.vx = -this.vx;
                if (this.y < 0 || this.y > height) this.vy = -this.vy;

                let dx = mouse.x - this.x;
                let dy = mouse.y - this.y;
                let distance = Math.sqrt(dx*dx + dy*dy);

                if (distance < mouse.radius) {
                    if (mouse.x < this.x && this.x < width - 10) this.x += 3;
                    if (mouse.x > this.x && this.x > 10) this.x -= 3;
                    if (mouse.y < this.y && this.y < height - 10) this.y += 3;
                    if (mouse.y > this.y && this.y > 10) this.y -= 3;
                }
                this.draw();
            }
        }

        function init() {
            particles = [];
            let density = (width * height) / 9000;
            for (let i = 0; i < density; i++) particles.push(new Particle());
        }

        function animate() {
            ctx.clearRect(0, 0, width, height);
            for (let i = 0; i < particles.length; i++) {
                particles[i].update();
                for (let j = i; j < particles.length; j++) {
                    let dx = particles[i].x - particles[j].x;
                    let dy = particles[i].y - particles[j].y;
                    let distance = Math.sqrt(dx*dx + dy*dy);
                    if (distance < 100) {
                        ctx.beginPath();
                        ctx.strokeStyle = `rgba(0, 243, 255, ${1 - distance/100})`;
                        ctx.lineWidth = 0.5;
                        ctx.moveTo(particles[i].x, particles[i].y);
                        ctx.lineTo(particles[j].x, particles[j].y);
                        ctx.stroke();
                    }
                }
            }
            requestAnimationFrame(animate);
        }

        init();
        animate();
    </script>
</body>
</html>