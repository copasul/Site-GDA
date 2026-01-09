<?php
session_start();
date_default_timezone_set('America/Sao_Paulo');

$dataAtual = date("Y-m-d H:i:s");
$validade  = date("Y-m-d H:i:s", strtotime('+1 hour'));

$email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
$senhaInput = trim((string)($_POST['senha'] ?? ''));

if (!$email || $senhaInput === '') {
    header("Location: ../login.php?status=error");
    exit;
}

require_once __DIR__ . '/conexao.php';

/**
 * Busca usuário
 */
$stmt = $conn->prepare("
    SELECT id, email, hash, senha
    FROM usuarios
    WHERE email = :email
    LIMIT 1
");
$stmt->execute([':email' => $email]);
$dados = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$dados) {
    header("Location: ../login.php?status=error");
    exit;
}

/**
 * Validação da senha (mantida 100% igual)
 */
$hash = (string)$dados['hash'];
$senhaCalc = md5($senhaInput . $hash . "%wUgk3S@3yq6cqrxP%H!&CtHV*YvI$");

if ($senhaCalc !== $dados['senha']) {
    header("Location: ../login.php?status=error");
    exit;
}

/**
 * Gera token seguro
 */
$token = bin2hex(random_bytes(32));

$stmt = $conn->prepare("
    INSERT INTO login_registro
    (data_hora, validade, id_usuario, token, tipo_acesso)
    VALUES
    (:data_hora, :validade, :id_usuario, :token, 'web')
");
$stmt->execute([
    ':data_hora'  => $dataAtual,
    ':validade'   => $validade,
    ':id_usuario' => (int)$dados['id'],
    ':token'      => $token,
]);

$_SESSION['token'] = $token;

header("Location: ../index.php");
exit;
