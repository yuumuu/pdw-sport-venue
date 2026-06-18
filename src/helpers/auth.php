<?php

function auth_login($username, $password)
{
    require_once __DIR__ . '/../config/database.php';
    $db = Database::connect();

    $stmt = $db->prepare("SELECT * FROM users WHERE username = ? LIMIT 1");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if (!$user || !password_verify($password, $user['password_hash'])) {
        return false;
    }

    $_SESSION['admin'] = [
        'id' => $user['id'],
        'username' => $user['username'],
        'display_name' => $user['display_name'],
        'role' => $user['role'],
    ];

    return true;
}

function auth_check()
{
    return isset($_SESSION['admin']) && !empty($_SESSION['admin']);
}

function auth_user()
{
    return $_SESSION['admin'] ?? null;
}

function auth_logout()
{
    unset($_SESSION['admin']);
    session_destroy();
}

function auth_required()
{
    if (!auth_check()) {
        $_SESSION['flash_error'] = 'Silakan login terlebih dahulu.';
        header('Location: ' . base_url('admin/login'));
        exit;
    }
}
