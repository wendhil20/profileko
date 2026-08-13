<?php
// client/auth/auth-1/login-handler.php

include ROOT_PATH . '/network/connect.php'; // dapat naka-define dito yung $conn (mysqli)

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . '/');
    exit;
}

$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

if (empty($username) || empty($password)) {
    $_SESSION['login_error'] = 'Please fill in both fields.';
    header('Location: ' . BASE_URL . '/');
    exit;
}

$stmt = $conn->prepare("SELECT id, username, password, full_name, role FROM portusers WHERE username = ? LIMIT 1");
$stmt->bind_param('s', $username);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

if ($user && password_verify($password, $user['password'])) {
    session_regenerate_id(true);

    $_SESSION['user_id']   = $user['id'];
    $_SESSION['username']  = $user['username'];
    $_SESSION['full_name'] = $user['full_name'];
    $_SESSION['role']      = $user['role'];

    header('Location: ' . BASE_URL . '/main');
    exit;
} else {
    $_SESSION['login_error'] = 'Invalid username or password.';
    header('Location: ' . BASE_URL . '/');
    exit;
}