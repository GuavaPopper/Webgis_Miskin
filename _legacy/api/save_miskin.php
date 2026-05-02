<?php
header('Content-Type: application/json');
require_once 'config.php';

$input = json_decode(file_get_contents('php://input'), true);

if (!$input || empty($input['id_rumah']) || empty($input['latitude']) || empty($input['longitude'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Data tidak lengkap']);
    exit();
}

try {
    // Check if ID exists
    $check = $pdo->prepare("SELECT id_rumah FROM rumah_miskin WHERE id_rumah = ?");
    $check->execute([$input['id_rumah']]);
    if ($check->rowCount() > 0) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'ID Rumah sudah terdaftar']);
        exit();
    }

    $stmt = $pdo->prepare("INSERT INTO rumah_miskin (id_rumah, alamat, jumlah_kk, jumlah_orang, latitude, longitude) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        $input['id_rumah'],
        $input['alamat'] ?? '',
        $input['jumlah_kk'] ?? 1,
        $input['jumlah_orang'] ?? 1,
        $input['latitude'],
        $input['longitude']
    ]);
    
    echo json_encode([
        'success' => true,
        'message' => 'Data Rumah Miskin berhasil disimpan'
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
