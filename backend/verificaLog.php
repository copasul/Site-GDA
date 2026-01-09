<?php
session_start();
date_default_timezone_set('America/Sao_Paulo');
$dataAtual = date("Y-m-d H:i:s");

// ✅ 1) Carregue a conexão ANTES de usar $conn
// Ajuste o caminho conforme seu projeto:
require_once __DIR__ . "/conexao.php"; // este arquivo deve definir $conn (PDO pgsql)

// ✅ 2) Busque url_base/titulo ANTES de redirecionar
$stmt = $conn->prepare("SELECT parametro, valor FROM parametros_gerais WHERE parametro IN ('url_base','titulo')");
$stmt->execute();
$params = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

$urlBase =  '/';
$titulo  = $params['titulo'] ?? '';

// ✅ 3) Pegue o token com segurança (sem warning)
$tokenAcess = $_SESSION['token'] ?? '';
$ip = $_SERVER['REMOTE_ADDR'] ?? '';

if (empty($tokenAcess)) {
    header("Location: " . $urlBase . "login.php");
    exit;
}

// ✅ 4) Valide token (Postgres: NOW() ou comparação timestamp)
// Evite string concatenation (SQL injection)
$stmt = $conn->prepare("
    SELECT id_usuario, validade, ip
    FROM login_registro
    WHERE token = :token
      AND validade > :agora
      AND ip = :ip
    ORDER BY id DESC
    LIMIT 1
");
$stmt->execute([
    ':token' => $tokenAcess,
    ':agora' => $dataAtual,
    ':ip'    => $ip
]);

$buscaToken = $stmt->fetch();

if (!$buscaToken) {
    header("Location: " . $urlBase . "backend/encerrar-sessao.php");
    exit;
}

$idUser = (int)($buscaToken['id_usuario'] ?? 0);

if ($idUser <= 0) {
    header("Location: " . $urlBase . "backend/encerrar-sessao.php");
    exit;
}

// ✅ 5) Buscar usuário (tabela em minúsculo no Postgres)
$stmt = $conn->prepare("SELECT * FROM usuarios WHERE id = :id LIMIT 1");
$stmt->execute([':id' => $idUser]);
$User = $stmt->fetch();

if (!$User) {
    header("Location: " . $urlBase . "backend/encerrar-sessao.php");
    exit;
}
?>
