<?php
header('Content-Type: application/json');
require_once 'config.php';

try {
    $stmt = $pdo->query("SELECT * FROM rumah_ibadah ORDER BY created_at DESC");
    $data = $stmt->fetchAll();
    echo json_encode($data);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
