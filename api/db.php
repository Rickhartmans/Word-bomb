<?php
// Simple SQLite DB helper. Call getDB() to get a PDO instance.
function getDB() {
    static $pdo = null;
    if ($pdo) return $pdo;

    $path = __DIR__ . '/leaderboard.sqlite';
    try {
        $pdo = new PDO('sqlite:' . $path);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        // Create table if it doesn't exist
        $pdo->exec("CREATE TABLE IF NOT EXISTS leaderboard (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            score INTEGER NOT NULL DEFAULT 0,
            ts INTEGER NOT NULL,
            ip TEXT
        );");
        // Useful index
        $pdo->exec("CREATE INDEX IF NOT EXISTS idx_score ON leaderboard(score DESC);");
    } catch (Exception $e) {
        http_response_code(500);
        header('Content-Type: application/json');
        echo json_encode(['status' => 'error', 'message' => 'DB error: ' . $e->getMessage()]);
        exit;
    }

    return $pdo;
}
