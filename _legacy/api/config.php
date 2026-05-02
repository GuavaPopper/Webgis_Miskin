<?php
// ── SHARED DATABASE CONFIGURATION ───────────────────────────────────────────
// Digunakan oleh semua endpoint API. File .env berada di root project.

$envFile = __DIR__ . '/../.env';

if (!file_exists($envFile)) {
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'File .env tidak ditemukan']);
    exit();
}

$env = [];
foreach (file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
    if (strpos(trim($line), '#') === 0 || !strpos($line, '=')) continue;
    list($key, $value) = explode('=', $line, 2);
    $env[trim($key)] = trim($value);
}

$host     = $env['DB_HOST'] ?? 'localhost';
$dbname   = $env['DB_NAME'] ?? 'webgis_miskin';
$username = $env['DB_USER'] ?? 'root';
$password = $env['DB_PASS'] ?? '';

// ── KONEKSI PDO ───────────────────────────────────────────────────────────────
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);

    // ── SCHEMA MIGRATION ───────────────────────────────────────────────────────
    // Pastikan kolom 'alamat' ada di tabel 'rumah_miskin'
    try {
        $pdo->exec("ALTER TABLE rumah_miskin ADD COLUMN alamat TEXT AFTER id_rumah");
    } catch (PDOException $e) {
        // Kolom mungkin sudah ada, abaikan error 1060 (Duplicate column name)
    }
} catch (PDOException $e) {
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Koneksi database gagal: ' . $e->getMessage()]);
    exit();
}
