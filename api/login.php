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

$stmt = $pdo->prepare('SELECT id, passhash FROM users WHERE username = ? LIMIT 1');
$stmt->execute([$username]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$user || !password_verify($password, $user['passhash'])) {
    echo json_encode(['status'=>'error','message'=>'Invalid credentials']);
    exit;
}

$_SESSION['user'] = ['id' => (int)$user['id'], 'username' => $username];

echo json_encode(['status'=>'ok','user'=>$_SESSION['user']]);
