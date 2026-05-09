<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    echo json_encode(['success' => false, 'message' => 'Ошибка: не переданы данные'], JSON_UNESCAPED_UNICODE);
    exit;
}

$fullname = trim($data['fullname'] ?? '');
$email = trim($data['email'] ?? '');
$phone = trim($data['phone'] ?? '');
$username = trim($data['username'] ?? '');
$password = $data['password'] ?? '';

// Валидация
if (strlen($fullname) < 3) {
    echo json_encode(['success' => false, 'message' => 'ФИО должно быть минимум 3 символа'], JSON_UNESCAPED_UNICODE);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Введите корректный email'], JSON_UNESCAPED_UNICODE);
    exit;
}

if (strlen($phone) < 10) {
    echo json_encode(['success' => false, 'message' => 'Введите корректный номер телефона'], JSON_UNESCAPED_UNICODE);
    exit;
}

if (strlen($username) < 3) {
    echo json_encode(['success' => false, 'message' => 'Логин должен быть минимум 3 символа'], JSON_UNESCAPED_UNICODE);
    exit;
}

if (strlen($password) < 4) {
    echo json_encode(['success' => false, 'message' => 'Пароль должен быть минимум 4 символа'], JSON_UNESCAPED_UNICODE);
    exit;
}

$usersFile = 'users.txt';

$users = [];
if (file_exists($usersFile)) {
    $content = file_get_contents($usersFile);
    if ($content) {
        $users = json_decode($content, true);
        if (!is_array($users)) {
            $users = [];
        }
    }
}

// Проверка на уникальность email, телефона и логина
foreach ($users as $user) {
    if ($user['email'] === $email) {
        echo json_encode(['success' => false, 'message' => 'Пользователь с таким email уже существует'], JSON_UNESCAPED_UNICODE);
        exit;
    }
    if ($user['phone'] === $phone) {
        echo json_encode(['success' => false, 'message' => 'Пользователь с таким номером телефона уже существует'], JSON_UNESCAPED_UNICODE);
        exit;
    }
    if ($user['username'] === $username) {
        echo json_encode(['success' => false, 'message' => 'Пользователь с таким логином уже существует'], JSON_UNESCAPED_UNICODE);
        exit;
    }
}

// Добавляем нового пользователя
$users[] = [
    'fullname' => $fullname,
    'email' => $email,
    'phone' => $phone,
    'username' => $username,
    'password' => $password
];

if (file_put_contents($usersFile, json_encode($users, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE))) {
    echo json_encode(['success' => true, 'message' => 'Регистрация успешна! Теперь войдите.'], JSON_UNESCAPED_UNICODE);
} else {
    echo json_encode(['success' => false, 'message' => 'Ошибка при сохранении данных'], JSON_UNESCAPED_UNICODE);
}
?>