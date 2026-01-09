<?php
session_start();
date_default_timezone_set('America/Sao_Paulo');

$dataAtual = date("Y-m-d H:i:s");

require_once __DIR__ . "/conexao.php";

/**
 * Configurações básicas
 */
$stmt = $conn->prepare("
    SELECT parametro, valor
    FROM parametros_gerais
    WHERE parametro IN ('url_base','titulo')
");
$stmt->execute();
$params = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

$urlBase = '/';
$titulo  = $params['titulo'] ?? '';

/**
 * Token da sessão
 */
$tokenAcess = $_SESSION['token'] ?? '';

if (empty($tokenAcess)) {
    header("Location: {$urlBase}login.php");
    exit;
}

/**
 * 🔥 VALIDAÇÃO SEM IP (SERVERLESS SAFE)
 */
$stmt = $conn->prepare("
    SELECT id_usuario
    FROM login_registro
    WHERE token = :token
      AND validade > :agora
    ORDER BY id DESC
    LIMIT 1
");
$stmt->execute([
    ':token' => $tokenAcess,
    ':agora' => $dataAtual,
]);

$buscaToken = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$buscaToken) {
    header("Location: {$urlBase}backend/encerrar-sessao.php");
    exit;
}

$idUser = (int)$buscaToken['id_usuario'];

/**
 * Busca usuário
 */
$stmt = $conn->prepare("
    SELECT *
    FROM usuarios
    WHERE id = :id
    LIMIT 1
");
$stmt->execute([':id' => $idUser]);
$User = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$User) {
    header("Location: {$urlBase}backend/encerrar-sessao.php");
    exit;
}
