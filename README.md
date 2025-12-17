# ⚡ VoltControl - IoT Energy Monitoring System (BETA v2.4)

Project ini adalah sistem monitoring dan kontrol energi berbasis web dengan antarmuka Cyberpunk UI. Sistem ini mencakup fitur keamanan login (Brute Force Protection), kontrol perangkat real-time, dan pencatatan log sensor.

---

## 📋 Persiapan Awal (Prerequisites)

Sebelum melakukan demo, pastikan hal berikut sudah siap:

1. **Web Server:** XAMPP / LAMPP / WAMP sudah terinstall dan berjalan (Apache & MySQL).
2. **Browser:** Google Chrome atau Microsoft Edge (Disarankan mode **Incognito** saat demo login).
3. **Database:** `db_iot_energy` sudah dibuat di phpMyAdmin.

---

## 🛠️ Instalasi & Setup Database

1. Buka **phpMyAdmin** (`http://localhost/phpmyadmin`).
2. Buat database baru dengan nama: `db_iot_energy`.
3. Klik tab **SQL**, copy-paste perintah berikut, lalu klik **Go**:

```sql
-- 1. Tabel Users (Dengan Fitur Blokir)
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    password VARCHAR(255) NOT NULL, -- Plain text untuk keperluan UAS
    role VARCHAR(20) DEFAULT 'Admin',
    failed_attempts INT DEFAULT 0,
    is_blocked TINYINT DEFAULT 0
);

-- Insert User Dummy (User: admin, Pass: 123)
INSERT INTO users (username, password, role) VALUES ('admin', '123', 'Chief Engineer');
INSERT INTO users (username, password, role) VALUES ('uasuser', 'uas123', 'Operator');

-- 2. Tabel Device Control
CREATE TABLE device_control (
    id INT AUTO_INCREMENT PRIMARY KEY,
    device_name VARCHAR(100),
    status TINYINT DEFAULT 0 -- 0: OFF, 1: ON
);

INSERT INTO device_control (device_name, status) VALUES 
('Main Generator', 1),
('Cooling System', 0),
('Emergency Light', 0);

-- 3. Tabel Sensor Logs
CREATE TABLE sensor_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tegangan FLOAT,
    arus FLOAT,
    daya FLOAT,
    waktu TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert Dummy Logs
INSERT INTO sensor_logs (tegangan, arus, daya) VALUES (220.5, 5.2, 1146.6), (219.8, 5.1, 1120.9);

-- 4. Tabel Settings
CREATE TABLE settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(50),
    setting_value VARCHAR(255)
);

INSERT INTO settings (setting_key, setting_value) VALUES 
('app_name', 'VoltControl Enterprise'), 
('voltage_limit_high', '240');

🚀 Skenario Demo (Walkthrough)
Ikuti langkah-langkah ini saat presentasi/demo untuk menunjukkan semua fitur bekerja dengan baik.
Skenario 1: Fitur Keamanan Login (Syarat UAS)
Tujuan: Membuktikan sistem memblokir user setelah 3x salah password.
1. Buka browser dalam Mode Incognito (agar sesi bersih).
2. Akses: localhost/nama_folder_project_anda/login.php.
3. Tes Blokir Sesi (User Asal):
  - Masukkan Username: hacker | Password: ngawur.
  - Klik Login -> Muncul Peringatan (Sisa percobaan berkurang).
  - Ulangi 3x sampai muncul pesan merah: "AKSES DIBLOKIR: Anda telah mencoba 3x...".
  - Poin: Menunjukkan sistem menghitung kegagalan pada sesi browser.
4. Reset Sesi: Tutup Incognito, buka Incognito baru.
5. Tes Blokir Database (User Asli):
  - Masukkan Username: admin | Password: salah (Password asli: 123).
  - Ulangi 3x.
  - Sistem akan menampilkan: "AKUN TERKUNCI: User ID ini telah diblokir permanen".
6. Verifikasi Blokir:
  - Coba login dengan Username: admin | Password: 123 (BENAR).
  - Hasil: Tetap Ditolak. (Membuktikan akun terkunci di database).
7. Cara Unblock (Untuk melanjutkan demo):
  - Buka phpMyAdmin, jalankan SQL: UPDATE users SET is_blocked=0, failed_attempts=0 WHERE username='admin';
  - Login kembali dengan admin / 123 -> Berhasil Masuk Dashboard.

Skenario 2: Dashboard & Monitoring
Tujuan: Menunjukkan UI Cyberpunk dan Data Realtime.
1. Jelaskan tampilan Dashboard:
  - Kartu Voltage/Current/Power.
  - Grafik Chart.js (Animasi visual).
  - Tampilan responsif dan efek neon.

Skenario 3: Device Control
Tujuan: Menunjukkan interaksi dengan Database.
1. Klik menu Device Control di sidebar.
2. Lihat daftar perangkat.
3. Klik tombol TURN ON / TURN OFF pada salah satu perangkat.
4. Perhatikan perubahan status:
  - Warna teks berubah (Hijau/Abu-abu).
  - Status teks berubah (ACTIVE/OFFLINE).
  - Tombol berubah warna.
5. (Opsional) Tunjukkan di phpMyAdmin tabel device_control bahwa kolom status berubah dari 0 ke 1.

Skenario 4: History & Log Data
Tujuan: Menunjukkan fitur Pagination dan Pengambilan Data.
1. Klik menu History Logs.
2. Tunjukkan tabel data sensor.
3. Jelaskan bahwa data ini diambil dari tabel sensor_logs.
4. Coba klik tombol Pagination (angka halaman) di bawah tabel (jika data dummy banyak).

Skenario 5: System Configuration
Tujuan: Menunjukkan fitur Update Data.
1. Klik menu System Config.
2. Ubah Application Name menjadi nama lain (misal: "VoltControl UAS").
3 Klik Save Configuration.
4. Lihat notifikasi sukses.
5. Refresh halaman atau lihat Sidebar, nama aplikasi mungkin berubah (tergantung implementasi di sidebar mengambil dari DB/Session).

Skenario 6: Logout
1. Klik tombol LOGOUT di sidebar bawah.
2. Sistem harus mengarahkan kembali ke halaman Login.
3. Coba tekan tombol "Back" di browser -> Seharusnya tidak bisa masuk kembali ke Dashboard (Session Destroyed).

❓ Troubleshooting (Kendala Umum)
1. Halaman Blank Putih:
  - Cek nama kolom di database. Pastikan tabel device_control memiliki kolom device_name dan status.
2. Login selalu "User tidak ditemukan":
  - Pastikan user admin sudah di-insert ke tabel users lewat SQL di atas.
3. Stuck Loading (Spinner terus berputar):
  - Pastikan script Javascript window.addEventListener('load'...) ada di bagian bawah file PHP tersebut.
4. Akses Ditolak Terus:
  - Anda mungkin terkena blokir sesi. Hapus Cache/Cookie browser atau ganti browser.
