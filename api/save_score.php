<?php
header('Content-Type: application/json');

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/auth.php';
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
    // If user is logged in, override provided name and record user_id
    $userId = null;
    if (is_logged_in()) {
        $u = current_user();
        if (!empty($u['username'])) {
            $name = $u['username'];
            $userId = $u['id'];
        }
    }

    $stmt = $pdo->prepare('INSERT INTO leaderboard (user_id, name, score, ts, ip) VALUES (?, ?, ?, ?, ?)');
    $params1 = [$userId, $name, $score, $ts, $ip];
    @file_put_contents(__DIR__ . '/save_score_errors.log', date('c') . " | DEBUG PREPARE: " . $stmt->queryString . " | PARAMS: " . json_encode($params1) . "\n", FILE_APPEND);
    $stmt->execute($params1);
    // Keep only top 200 entries by score (and timestamp as tiebreaker)
    // Use a derived-table for the subquery to avoid MySQL "You can't specify target table for update in FROM clause" (error 1093)
    $driver = $pdo->getAttribute(PDO::ATTR_DRIVER_NAME);
    if ($driver === 'mysql') {
        $pdo->exec("DELETE FROM leaderboard WHERE id NOT IN (SELECT id FROM (SELECT id FROM leaderboard ORDER BY score DESC, ts ASC LIMIT 200) AS _keep)");
    } else {
        $pdo->exec("DELETE FROM leaderboard WHERE id NOT IN (SELECT id FROM leaderboard ORDER BY score DESC, ts ASC LIMIT 200)");
    }

    // Determine rank of this entry (1-based)
    $stmtRank = $pdo->prepare('SELECT COUNT(*) AS higher FROM leaderboard WHERE (score > ?) OR (score = ? AND ts < ?)');
    $params2 = [$score, $score, $ts];
    @file_put_contents(__DIR__ . '/save_score_errors.log', date('c') . " | DEBUG PREPARE: " . $stmtRank->queryString . " | PARAMS: " . json_encode($params2) . "\n", FILE_APPEND);
    $stmtRank->execute($params2);
    $row = $stmtRank->fetch(PDO::FETCH_ASSOC);
    $rank = ($row && isset($row['higher'])) ? intval($row['higher']) + 1 : 1;

    echo json_encode(['status' => 'ok', 'entry' => ['name' => $name, 'score' => $score, 'ts' => $ts], 'rank' => $rank]);
} catch (Exception $e) {
    http_response_code(500);
    // Log full exception to a server-side file for debugging
    $logFile = __DIR__ . '/save_score_errors.log';
    $pdoDriver = 'unknown';
    if (isset($pdo) && $pdo instanceof PDO) {
        try { $pdoDriver = $pdo->getAttribute(PDO::ATTR_DRIVER_NAME); } catch (Exception $ex) { }
    }
    $reqBody = json_encode($_POST);
    $remote = $_SERVER['REMOTE_ADDR'] ?? ($ip ?? '');
    $entry = date('c') . " | Exception: " . $e->getMessage() . " | Driver: " . $pdoDriver . " | POST: " . $reqBody . " | REMOTE_ADDR: " . $remote . "\nTrace:\n" . $e->getTraceAsString() . "\n---\n";
    @file_put_contents($logFile, $entry, FILE_APPEND);

    // If debug flag present, show full message to help troubleshoot
    if ((isset($_GET['debug']) && $_GET['debug']) || (isset($_POST['debug']) && $_POST['debug'])) {
        echo json_encode(['status' => 'error', 'message' => 'DB error: ' . $e->getMessage(), 'log' => basename($logFile)]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'DB error', 'log' => basename($logFile)]);
    }
}

