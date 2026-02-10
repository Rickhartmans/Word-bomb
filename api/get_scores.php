<?php
header('Content-Type: application/json');

require_once __DIR__ . '/db.php';
$pdo = getDB();

$max = isset($_GET['max']) ? intval($_GET['max']) : 10;
if ($max < 1) $max = 10;

try {
    $stmt = $pdo->prepare('SELECT name, score, ts FROM leaderboard ORDER BY score DESC, ts ASC LIMIT :max');
    $stmt->bindValue(':max', $max, PDO::PARAM_INT);
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($rows);
} catch (Exception $e) {
    http_response_code(500);
    // If debug flag is present, return the error message to help troubleshooting.
    if (isset($_GET['debug']) && $_GET['debug']) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    } else {
        echo json_encode([]);
    }
}

