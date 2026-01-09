<?php
date_default_timezone_set('America/Sao_Paulo');

/**
 * 1️⃣ Validação de entrada
 */
$email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
$senhaInput = trim((string)($_POST['senha'] ?? ''));

if (!$email || $senhaInput === '') {
    header("Location: ../login.php?status=error");
    exit;
}

/**
 * 2️⃣ Conexão
 */
require_once __DIR__ . '/conexao.php';

/**
 * 3️⃣ Busca usuário
 */
$stmt = $conn->prepare("
    SELECT id, email, hash, senha
    FROM usuarios
    WHERE email = :email
    LIMIT 1
");
$stmt->execute([':email' => $email]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$usuario) {
    header("Location: ../login.php?status=error");
    exit;
}

/**
 * 4️⃣ Valida senha (mantendo seu algoritmo)
 */
$hash = (string)$usuario['hash'];
$senhaCalc = md5($senhaInput . $hash . "%wUgk3S@3yq6cqrxP%H!&CtHV*YvI$");

if ($senhaCalc !== $usuario['senha']) {
    header("Location: ../login.php?status=error");
    exit;
}

/**
 * 5️⃣ Gera token
 */
$token     = bin2hex(random_bytes(32));
$agora     = date('Y-m-d H:i:s');
$validade  = date('Y-m-d H:i:s', strtotime('+1 hour'));
$ip        = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';

/**
 * 6️⃣ Registra login
 */
$stmt = $conn->prepare("
    INSERT INTO login_registro
    (data_hora, validade, id_usuario, token, tipo_acesso, ip)
    VALUES
    (:data_hora, :validade, :id_usuario, :token, 'web', :ip)
");
$stmt->execute([
    ':data_hora'  => $agora,
    ':validade'   => $validade,
    ':id_usuario' => $usuario['id'],
    ':token'      => $token,
    ':ip'         => $ip,
]);

/**
 * 7️⃣ Cookie seguro (Vercel / WAF safe)
 */
setcookie(
    'auth_token',
    $token,
    [
        'expires'  => time() + 3600,
        'path'     => '/',
        'secure'   => true,
        'httponly' => true,
        'samesite' => 'Strict'
    ]
);

/**
 * 8️⃣ Redirect
 */
header("Location: ../index.php");
exit;
