<?php
header('Content-Type: application/json');
require_once 'config.php';

$input = json_decode(file_get_contents('php://input'), true);

if (!$input || empty($input['id_rumah'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'ID Rumah tidak ditemukan']);
    exit();
}

try {
    $stmt = $pdo->prepare("DELETE FROM rumah_miskin WHERE id_rumah = ?");
    $stmt->execute([$input['id_rumah']]);
    
    echo json_encode([
        'success' => true,
        'message' => 'Data Rumah Miskin berhasil dihapus'
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
