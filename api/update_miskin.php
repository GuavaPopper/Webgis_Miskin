<?php
header('Content-Type: application/json');
require_once 'config.php';

$input = json_decode(file_get_contents('php://input'), true);

if (!$input || empty($input['id_rumah'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Data tidak lengkap']);
    exit();
}

try {
    $stmt = $pdo->prepare("UPDATE rumah_miskin SET alamat = ?, jumlah_kk = ?, jumlah_orang = ?, latitude = ?, longitude = ? WHERE id_rumah = ?");
    $stmt->execute([
        $input['alamat'] ?? '',
        $input['jumlah_kk'] ?? 1,
        $input['jumlah_orang'] ?? 1,
        (float)$input['latitude'],
        (float)$input['longitude'],
        $input['id_rumah']
    ]);
    
    echo json_encode([
        'success' => true,
        'message' => 'Data Rumah Miskin berhasil diperbarui'
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
