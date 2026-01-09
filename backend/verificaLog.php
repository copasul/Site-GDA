<?php
/**
 * verificaLog.php — versão FINAL (Vercel + WAF safe)
 */

date_default_timezone_set('America/Sao_Paulo');
$dataAtual = date("Y-m-d H:i:s");

/**
 * 1️⃣ Conexão
 */
require_once __DIR__ . "/conexao.php";

/**
 * 2️⃣ Busca parâmetros globais
 */
$stmt = $conn->prepare("
    SELECT parametro, valor 
    FROM parametros_gerais 
    WHERE parametro IN ('url_base','titulo')
");
$stmt->execute();
$params = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

$urlBase = $params['url_base'] ?? '/';
$titulo  = $params['titulo']  ?? '';

/**
 * 3️⃣ Token vem do COOKIE (não sessão)
 */
$tokenAcess = $_COOKIE['auth_token'] ?? null;
$ip         = $_SERVER['REMOTE_ADDR'] ?? '';

if (!$tokenAcess) {
    header("Location: {$urlBase}login.php");
    exit;
}

/**
 * 4️⃣ Valida token no banco
 */
$stmt = $conn->prepare("
    SELECT id_usuario
    FROM login_registro
    WHERE token = :token
      AND validade > NOW()
      AND ip = :ip
    ORDER BY id DESC
    LIMIT 1
");
$stmt->execute([
    ':token' => $tokenAcess,
    ':ip'    => $ip
]);

$buscaToken = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$buscaToken) {
    setcookie('auth_token', '', time() - 3600, '/', '', true, true);
    header("Location: {$urlBase}login.php");
    exit;
}

/**
 * 5️⃣ Busca usuário
 */
$idUser = (int)$buscaToken['id_usuario'];

$stmt = $conn->prepare("
    SELECT *
    FROM usuarios
    WHERE id = :id
    LIMIT 1
");
$stmt->execute([':id' => $idUser]);

$User = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$User) {
    setcookie('auth_token', '', time() - 3600, '/', '', true, true);
    header("Location: {$urlBase}login.php");
    exit;
}

/**
 * ✔ A PARTIR DAQUI:
 * $User está válido e o login FUNCIONA
 */
