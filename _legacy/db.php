<?php
// Function to parse minimal .env file
function parseEnv($filePath) {
    if (!file_exists($filePath)) return [];
    $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $env = [];
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        list($name, $value) = explode('=', $line, 2) + [NULL, NULL];
        if ($name !== NULL) {
            $env[trim($name)] = trim($value);
            putenv(trim($name) . "=" . trim($value));
        }
    }
    return $env;
}

$env = parseEnv(__DIR__ . '/.env');
$host = getenv('DB_HOST') ?: '127.0.0.1';
$dbname = getenv('DB_NAME') ?: 'webgis_miskin';
$user = getenv('DB_USER') ?: 'root';
$pass = getenv('DB_PASS') ?: '';

try {
    // First connect without specifying a database to create it if it doesn't exist
    $pdo_init = new PDO("mysql:host=$host", $user, $pass);
    $pdo_init->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo_init->exec("CREATE DATABASE IF NOT EXISTS `$dbname`");
    
    // Now connect to the specific database
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create tables if not exists
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS rumah_ibadah (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nama VARCHAR(255) NOT NULL,
            alamat TEXT NOT NULL,
            latitude DECIMAL(10, 8) NOT NULL,
            longitude DECIMAL(11, 8) NOT NULL,
            radius INT DEFAULT 500,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ");
    
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS rumah_miskin (
            id_rumah VARCHAR(50) PRIMARY KEY,
            alamat TEXT,
            jumlah_kk INT NOT NULL DEFAULT 1,
            jumlah_orang INT NOT NULL DEFAULT 1,
            latitude DECIMAL(10, 8) NOT NULL,
            longitude DECIMAL(11, 8) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ");

    // Ensure alamat column exists if table was already created
    try {
        $pdo->exec("ALTER TABLE rumah_miskin ADD COLUMN alamat TEXT AFTER id_rumah");
    } catch (PDOException $e) {
        // Column might already exist, ignore error
    }
    
} catch(PDOException $e) {
    die("Database Connection failed: " . $e->getMessage());
}
?>
