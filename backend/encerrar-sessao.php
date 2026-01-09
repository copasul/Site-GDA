<?php
/**
 * encerrar-sessao.php — corrigido
 */

/**
 * 1️⃣ Sessão SEMPRE primeiro
 */
session_start();

/**
 * 2️⃣ Include com path correto
 * encerrar-sessao.php já está em /backend
 */
require_once __DIR__ . '/conexao.php';

/**
 * 3️⃣ Remove token do banco (seguro)
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
 * 5️⃣ Redirect
 */
header("Location: ../login.php");
exit;
