<?php
// Migration script: import existing scores.json into leaderboard.sqlite
require_once __DIR__ . '/db.php';
$pdo = getDB();

$path = __DIR__ . '/scores.json';
if (!file_exists($path)) {
    echo "No scores.json found, nothing to migrate.\n";
    exit;
}

$content = file_get_contents($path);
$data = json_decode($content, true);
if (!is_array($data)) {
    echo "scores.json invalid or empty.\n";
    exit;
}

$inserted = 0;
$pdo->beginTransaction();
try {
    $stmt = $pdo->prepare('INSERT INTO leaderboard (name, score, ts, ip) VALUES (:name, :score, :ts, :ip)');
    foreach ($data as $row) {
        $name = isset($row['name']) ? substr($row['name'],0,50) : 'Anonymous';
        $score = isset($row['score']) ? intval($row['score']) : 0;
        $ts = isset($row['time']) ? intval($row['time']) : time();
        $ip = isset($row['ip']) ? $row['ip'] : '';
        $stmt->execute([':name'=>$name, ':score'=>$score, ':ts'=>$ts, ':ip'=>$ip]);
        $inserted++;
    }
    $pdo->commit();
    echo "Inserted $inserted rows into leaderboard.sqlite\n";
} catch (Exception $e) {
    $pdo->rollBack();
    echo "Migration failed: " . $e->getMessage() . "\n";
}
