<?php
/**
 * Encerrar sessão — versão segura e compatível com Vercel
 */

/**
 * 1️⃣ Sessão — SEMPRE primeiro
 */
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 1);
ini_set('session.cookie_samesite', 'Strict');

session_start();

/**
 * 2️⃣ Conexão (path absoluto correto)
 */
require_once __DIR__ . '/conexao.php';

/**
 * 3️⃣ Remove token do banco (prepare)
 */
if (!empty($_SESSION['token'])) {
    $stmt = $conn->prepare("
        UPDATE login_registro
        SET token = NULL
        WHERE token = :token
    ");
    $stmt->execute([
        ':token' => $_SESSION['token']
    ]);
}

/**
 * 4️⃣ Limpa sessão
 */
$_SESSION = [];
session_destroy();

/**
 * 5️⃣ Redirect seguro (Vercel-safe)
 */
header("Location: ../login.php");
exit;
