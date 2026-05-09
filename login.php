<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

$data = json_decode(file_get_contents('php://input'), true);

if (!$data || !isset($data['email']) || !isset($data['password'])) {
    echo json_encode(['success' => false, 'message' => 'Ошибка: не переданы данные'], JSON_UNESCAPED_UNICODE);
    exit;
}

$email = trim($data['email']);
$password = $data['password'];

$usersFile = 'users.txt';

if (!file_exists($usersFile)) {
    echo json_encode(['success' => false, 'message' => 'Нет зарегистрированных пользователей'], JSON_UNESCAPED_UNICODE);
    exit;
}

$content = file_get_contents($usersFile);
$users = json_decode($content, true);

if (!is_array($users)) {
    echo json_encode(['success' => false, 'message' => 'Ошибка чтения данных'], JSON_UNESCAPED_UNICODE);
    exit;
}

foreach ($users as $user) {
    if ($user['email'] === $email && $user['password'] === $password) {
        echo json_encode([
            'success' => true, 
            'message' => 'Вход выполнен',
            'user' => [
                'fullname' => $user['fullname'],
                'email' => $user['email'],
                'phone' => $user['phone'],
                'username' => $user['username']
            ]
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
}

echo json_encode(['success' => false, 'message' => 'Неверный email или пароль'], JSON_UNESCAPED_UNICODE);
?>