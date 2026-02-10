<?php
session_start();
require_once __DIR__ . '/db.php';
$pdo = getDB();

function is_logged_in() {
    return isset($_SESSION['user']) && !empty($_SESSION['user']['id']);
}

function current_user() {
    return $_SESSION['user'] ?? null;
}

function require_json_post($keys=[]) {
    foreach ($keys as $k) {
        if (!isset($_POST[$k])) {
            http_response_code(400);
            header('Content-Type: application/json');
            echo json_encode(['status'=>'error','message'=>"Missing field: $k"]);
            exit;
        }
    }
}
