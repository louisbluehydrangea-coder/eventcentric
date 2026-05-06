<?php
require_once __DIR__ . '/../db.php';

function startSession(): void {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

function isLoggedIn(): bool {
    startSession();
    return isset($_SESSION['user_id']);
}

function getCurrentUser(): ?array {
    if (!isLoggedIn()) return null;
    $db = getDB();
    $stmt = $db->prepare("SELECT id, name, email FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    return $stmt->fetch() ?: null;
}

function requireLogin(): void {
    if (!isLoggedIn() || !getCurrentUser()) {
        startSession();
        session_unset();
        session_destroy();
        header('Location: ' . SITE_URL . '/login.php');
        exit;
    }
}

function loginUser(string $email, string $password): bool {
    $db = getDB();
    $stmt = $db->prepare("SELECT id, password_hash FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    if ($user && password_verify($password, $user['password_hash'])) {
        startSession();
        $_SESSION['user_id'] = $user['id'];
        return true;
    }
    return false;
}

function registerUser(string $name, string $email, string $password): bool {
    $db = getDB();
    $hash = password_hash($password, PASSWORD_BCRYPT);
    try {
        $stmt = $db->prepare("INSERT INTO users (name, email, password_hash) VALUES (?, ?, ?)");
        $stmt->execute([$name, $email, $hash]);
        startSession();
        $_SESSION['user_id'] = $db->lastInsertId();
        return true;
    } catch (PDOException $e) {
        return false; // email duplicate
    }
}

function logoutUser(): void {
    startSession();
    session_destroy();
    header('Location: ' . SITE_URL . '/index.php');
    exit;
}
