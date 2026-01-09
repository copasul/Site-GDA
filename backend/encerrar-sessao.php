<?php
/**
 * Encerrar sessão (cookie-based)
 */

require_once __DIR__ . '/conexao.php';

$token = $_COOKIE['auth_token'] ?? '';

if (!empty($token)) {
    $stmt = $conn->prepare("
        UPDATE login_registro
        SET token = NULL
        WHERE token = :token
    ");
    $stmt->execute([':token' => $token]);
}

/**
 * Remove cookie
 */
setcookie(
    'auth_token',
    '',
    [
        'expires'  => time() - 3600,
        'path'     => '/',
        'secure'   => true,
        'httponly' => true,
        'samesite' => 'Strict'
    ]
);

header("Location: ../login.php");
exit;
