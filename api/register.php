<?php
header('Content-Type: application/json');
require_once __DIR__ . '/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status'=>'error','message'=>'Method not allowed']);
    exit;
}

require_json_post(['username','password']);
$username = trim($_POST['username']);
$password = $_POST['password'];

if (!preg_match('/^[A-Za-z0-9_\-]{3,50}$/', $username)) {
    echo json_encode(['status'=>'error','message'=>'Invalid username']);
    exit;
}

// check exists
$stmt = $pdo->prepare('SELECT id FROM users WHERE username = ? LIMIT 1');
$stmt->execute([$username]);
if ($stmt->fetch()) {
    echo json_encode(['status'=>'error','message'=>'Username taken']);
    exit;
}

$hash = password_hash($password, PASSWORD_DEFAULT);
$ts = time();
$stmt = $pdo->prepare('INSERT INTO users (username, passhash, created_ts) VALUES (?, ?, ?)');
$stmt->execute([$username, $hash, $ts]);
$userId = $pdo->lastInsertId();

// create session
$_SESSION['user'] = ['id' => (int)$userId, 'username' => $username];

echo json_encode(['status'=>'ok','user'=>$_SESSION['user']]);
