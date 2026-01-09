<?php
session_start();
date_default_timezone_set('America/Sao_Paulo');

$dataAtual = date("Y-m-d H:i:s");
$validade  = date("Y-m-d H:i:s", strtotime('+1 hour'));

$email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
$senhaInput = trim((string)($_POST['senha'] ?? '')); // substitui FILTER_SANITIZE_STRING

$ip = $_SERVER["REMOTE_ADDR"] ?? '';
$chave = date('m/Y');
$token = md5(($email ?? '') . "iwHv%C,z0j!qYa" . $chave . "web" . $ip . rand(0,1000));
$tipo_acesso = 'web';

try {
    require_once __DIR__ . '/conexao.php';

    if (!$email || $senhaInput === '') {
        header("Location: ../login.php?status=error");
        exit;
    }

    // ✅ Postgres: tabela em minúsculo
    $sqlBusca = $conn->prepare("SELECT id, email, hash, senha FROM usuarios WHERE email = :email LIMIT 1");
    $sqlBusca->execute([':email' => $email]);
    $dados = $sqlBusca->fetch(PDO::FETCH_ASSOC);

    if (!$dados) {
        header("Location: ../login.php?status=error");
        exit;
    }

    $hash = (string)$dados['hash'];
    $senhaCalc = md5($senhaInput . $hash . "%wUgk3S@3yq6cqrxP%H!&CtHV*YvI$");

    if ($senhaCalc !== $dados['senha']) {
        header("Location: ../login.php?status=error");
        exit;
    }

    // ✅ Postgres: sem crases + prepare normal
    $sqlAcesso = $conn->prepare("
        INSERT INTO login_registro (data_hora, validade, id_usuario, token, tipo_acesso, ip)
        VALUES (:dataHora, :validade, :idUsuario, :token, :tipo_acesso, :ip)
    ");
    $sqlAcesso->execute([
        ':dataHora'    => $dataAtual,
        ':validade'    => $validade,
        ':idUsuario'   => (int)$dados['id'],
        ':token'       => $token,
        ':tipo_acesso' => $tipo_acesso,
        ':ip'          => $ip,
    ]);

    $_SESSION['token'] = $token;

    header("Location: ../index.php");
    exit;

} catch (Throwable $e) {
    // Em produção, ideal logar em vez de printar
    echo "Erro: " . $e->getMessage();
}
