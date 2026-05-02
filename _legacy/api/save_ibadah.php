<?php
header('Content-Type: application/json');
require_once 'config.php';

$input = json_decode(file_get_contents('php://input'), true);

if (!$input || empty($input['nama']) || empty($input['latitude']) || empty($input['longitude'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Data tidak lengkap']);
    exit();
}

try {
    $stmt = $pdo->prepare("INSERT INTO rumah_ibadah (nama, alamat, latitude, longitude, radius) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([
        $input['nama'],
        $input['alamat'] ?? '',
        $input['latitude'],
        $input['longitude'],
        $input['radius'] ?? 500
    ]);
    
    echo json_encode([
        'success' => true,
        'id' => $pdo->lastInsertId(),
        'message' => 'Data Rumah Ibadah berhasil disimpan'
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
