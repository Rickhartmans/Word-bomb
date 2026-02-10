<?php
header('Content-Type: application/json');
require_once __DIR__ . '/auth.php';

$user = current_user();
if (!$user) {
    echo json_encode(['status'=>'ok','user'=>null]);
    exit;
}

// return user info and their top scores
$stmt = $pdo->prepare('SELECT id, name, score, ts FROM leaderboard WHERE user_id = ? ORDER BY score DESC, ts ASC LIMIT 100');
$stmt->execute([$user['id']]);
$scores = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode(['status'=>'ok','user'=>$user,'scores'=>$scores]);
