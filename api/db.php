<?php
// DB helper. If `api/config.php` exists and contains a MySQL DSN it will use MySQL,
// otherwise it falls back to a local SQLite file `leaderboard.sqlite`.
function getDB() {
    static $pdo = null;
    if ($pdo) return $pdo;

    // If user provided a config.php with DSN (for MySQL), use it
    $configPath = __DIR__ . '/config.php';
    if (file_exists($configPath)) {
        $cfg = include $configPath;
        if (is_array($cfg) && isset($cfg['dsn']) && strpos($cfg['dsn'], 'mysql:') === 0) {
            try {
                $pdo = new PDO($cfg['dsn'], $cfg['user'] ?? null, $cfg['pass'] ?? null, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_EMULATE_PREPARES => false,
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
                ]);
                return $pdo;
            } catch (Exception $e) {
                http_response_code(500);
                header('Content-Type: application/json');
                echo json_encode(['status' => 'error', 'message' => 'MySQL connection error: ' . $e->getMessage()]);
                exit;
            }
        }
    }

    // Fallback: SQLite
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
        // Useful index (sqlite syntax)
        $pdo->exec("CREATE INDEX IF NOT EXISTS idx_score ON leaderboard(score);");
    } catch (Exception $e) {
        http_response_code(500);
        header('Content-Type: application/json');
        echo json_encode(['status' => 'error', 'message' => 'DB error: ' . $e->getMessage()]);
        exit;
    }

    return $pdo;
}
