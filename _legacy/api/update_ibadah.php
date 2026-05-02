<?php
header('Content-Type: application/json');
require_once 'config.php';

$input = json_decode(file_get_contents('php://input'), true);

if (!$input || empty($input['id']) || empty($input['nama'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Data tidak lengkap']);
    exit();
}

try {
    $stmt = $pdo->prepare("UPDATE rumah_ibadah SET nama = ?, alamat = ?, latitude = ?, longitude = ?, radius = ? WHERE id = ?");
    $stmt->execute([
        $input['nama'],
        $input['alamat'] ?? '',
        (float)$input['latitude'],
        (float)$input['longitude'],
        $input['radius'] ?? 500,
        $input['id']
    ]);
    
    echo json_encode([
        'success' => true,
        'message' => 'Data Rumah Ibadah berhasil diperbarui'
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
