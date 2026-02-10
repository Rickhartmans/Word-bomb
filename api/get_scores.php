<?php
header('Content-Type: application/json');

$path = __DIR__ . '/scores.json';
$max = isset($_GET['max']) ? intval($_GET['max']) : 10;

if (!file_exists($path)) {
    echo json_encode([]);
    exit;
}

$data = json_decode(file_get_contents($path), true);
if (!is_array($data)) $data = [];

usort($data, function($a, $b){
    return $b['score'] - $a['score'];
});

$data = array_slice($data, 0, $max);

echo json_encode($data);
