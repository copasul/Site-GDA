<?php
session_start();

/**
 * Arquivo está em /backend
 * conexao.php também está em /backend
 */
require_once __DIR__ . '/conexao.php';

if (empty($_SESSION['token'])) {
    http_response_code(401);
    exit('Sessão inválida');
}

$token = $_SESSION['token'];

$stmt = $conn->prepare("
    UPDATE login_registro
    SET token = NULL
    WHERE token = :token
");
$stmt->execute([
    ':token' => $token
]);

$_SESSION = [];
session_destroy();

echo 'OK';
