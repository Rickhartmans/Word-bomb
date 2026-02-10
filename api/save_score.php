<?php
header('Content-Type: application/json');

require_once __DIR__ . '/db.php';
$pdo = getDB();

// Read POST body
$name = isset($_POST['name']) ? trim($_POST['name']) : '';
$score = isset($_POST['score']) ? intval($_POST['score']) : 0;

if ($score < 0) $score = 0;
if ($score > 100000) $score = 100000;

// sanitize name
$name = preg_replace('/[^\w\- \u00C0-\u017F]/u', '', $name);
$name = substr($name, 0, 50);
if ($name === '') $name = 'Anonymous';

$ts = time();
$ip = $_SERVER['REMOTE_ADDR'] ?? '';

try {
    $stmt = $pdo->prepare('INSERT INTO leaderboard (name, score, ts, ip) VALUES (:name, :score, :ts, :ip)');
    $stmt->execute([':name' => $name, ':score' => $score, ':ts' => $ts, ':ip' => $ip]);
    // Keep only top 200 entries by score (and timestamp as tiebreaker)
    $pdo->exec("DELETE FROM leaderboard WHERE id NOT IN (SELECT id FROM leaderboard ORDER BY score DESC, ts ASC LIMIT 200)");

    // Determine rank of this entry (1-based)
    $stmtRank = $pdo->prepare('SELECT COUNT(*) AS higher FROM leaderboard WHERE (score > :score) OR (score = :score AND ts < :ts)');
    $stmtRank->execute([':score' => $score, ':ts' => $ts]);
    $row = $stmtRank->fetch(PDO::FETCH_ASSOC);
    $rank = ($row && isset($row['higher'])) ? intval($row['higher']) + 1 : 1;

    echo json_encode(['status' => 'ok', 'entry' => ['name' => $name, 'score' => $score, 'ts' => $ts], 'rank' => $rank]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'DB error: ' . $e->getMessage()]);
}

