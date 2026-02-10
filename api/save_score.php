<?php
header('Content-Type: application/json');

$path = __DIR__ . '/scores.json';

// Read POST body
$name = isset($_POST['name']) ? trim($_POST['name']) : '';
$score = isset($_POST['score']) ? intval($_POST['score']) : 0;

if ($score < 0) $score = 0;
if ($score > 100000) $score = 100000;

// sanitize name
$name = preg_replace('/[^\w\- \u00C0-\u017F]/u', '', $name);
$name = substr($name, 0, 20);
if ($name === '') $name = 'Anonymous';

$entry = [
    'name' => $name,
    'score' => $score,
    'time' => time(),
    'ip' => $_SERVER['REMOTE_ADDR'] ?? ''
];

// Read existing data
$data = [];
if (file_exists($path)) {
    $content = file_get_contents($path);
    $data = json_decode($content, true);
    if (!is_array($data)) $data = [];
}

// Append and keep only top 200 entries
$data[] = $entry;

usort($data, function($a, $b){
    return $b['score'] - $a['score'];
});

$data = array_slice($data, 0, 200);

// Write back with lock
$written = false;
if ($fh = fopen($path, 'c+')) {
    if (flock($fh, LOCK_EX)) {
        ftruncate($fh, 0);
        rewind($fh);
        fwrite($fh, json_encode($data, JSON_PRETTY_PRINT));
        fflush($fh);
        flock($fh, LOCK_UN);
        $written = true;
    }
    fclose($fh);
}

if ($written) {
    echo json_encode(['status' => 'ok', 'entry' => $entry]);
} else {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Could not write scores file']);
}
