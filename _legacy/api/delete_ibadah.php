<?php
header('Content-Type: application/json');
require_once 'config.php';

$input = json_decode(file_get_contents('php://input'), true);

if (!$input || empty($input['id'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'ID tidak ditemukan']);
    exit();
}

try {
    $stmt = $pdo->prepare("DELETE FROM rumah_ibadah WHERE id = ?");
    $stmt->execute([$input['id']]);
    
    echo json_encode([
        'success' => true,
        'message' => 'Data Rumah Ibadah berhasil dihapus'
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
