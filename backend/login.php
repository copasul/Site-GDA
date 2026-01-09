<?php
/**
 * =========================
 * 4️⃣ COOKIE DE SESSÃO SEGURO
 * =========================
 */
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 1); // exige HTTPS
ini_set('session.cookie_samesite', 'Strict');

session_start();
date_default_timezone_set('America/Sao_Paulo');

/**
 * =========================
 * Dados básicos
 * =========================
 */
$dataAtual = date("Y-m-d H:i:s");
$validade  = date("Y-m-d H:i:s", strtotime('+1 hour'));

$email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
$senhaInput = trim((string)($_POST['senha'] ?? ''));

if (!$email || $senhaInput === '') {
    header("Location: ../login.php?status=error");
    exit;
}

/**
 * =========================
 * IP (compatível com WAF)
 * =========================
 */
$ip = $_SERVER['HTTP_X_FORWARDED_FOR']
    ?? $_SERVER['REMOTE_ADDR']
    ?? '';

/**
 * =========================
 * Token seguro
 * =========================
 */
$token = bin2hex(random_bytes(32));
$tipo_acesso = 'web';

try {
    require_once __DIR__ . '/conexao.php';

    /**
     * =========================
     * Busca usuário
     * =========================
     */
    $sqlBusca = $conn->prepare("
        SELECT id, email, hash, senha
        FROM usuarios
        WHERE email = :email
        LIMIT 1
    ");
    $sqlBusca->execute([':email' => $email]);
    $dados = $sqlBusca->fetch(PDO::FETCH_ASSOC);

    if (!$dados) {
        header("Location: ../login.php?status=error");
        exit;
    }

    /**
     * =========================
     * 2️⃣ VALIDAÇÃO + MIGRAÇÃO DE SENHA
     * =========================
     */
    $senhaValida = false;

    // ✅ Senha nova (password_hash)
    if (password_verify($senhaInput, $dados['senha'])) {
        $senhaValida = true;
    } else {
        // ⚠️ Fallback legado (MD5)
        $hash = (string)$dados['hash'];
        $senhaCalc = md5($senhaInput . $hash . "%wUgk3S@3yq6cqrxP%H!&CtHV*YvI$");

        if (hash_equals($dados['senha'], $senhaCalc)) {
            $senhaValida = true;

            // 🔄 Migra automaticamente para password_hash
            $novoHash = password_hash($senhaInput, PASSWORD_DEFAULT);
            $conn->prepare("
                UPDATE usuarios
                SET senha = :senha
                WHERE id = :id
            ")->execute([
                ':senha' => $novoHash,
                ':id'    => (int)$dados['id']
            ]);
        }
    }

    if (!$senhaValida) {
        header("Location: ../login.php?status=error");
        exit;
    }

    /**
     * =========================
     * 3️⃣ EXPIRA SESSÕES ANTIGAS
     * =========================
     */
    $conn->prepare("
        DELETE FROM login_registro
        WHERE id_usuario = :id
    ")->execute([
        ':id' => (int)$dados['id']
    ]);

    /**
     * =========================
     * Registra novo login
     * =========================
     */
    $sqlAcesso = $conn->prepare("
        INSERT INTO login_registro
        (data_hora, validade, id_usuario, token, tipo_acesso, ip)
        VALUES
        (:dataHora, :validade, :idUsuario, :token, :tipo_acesso, :ip)
    ");
    $sqlAcesso->execute([
        ':dataHora'    => $dataAtual,
        ':validade'    => $validade,
        ':idUsuario'   => (int)$dados['id'],
        ':token'       => $token,
        ':tipo_acesso' => $tipo_acesso,
        ':ip'          => $ip,
    ]);

    /**
     * =========================
     * Sessão
     * =========================
     */
    $_SESSION['token'] = $token;
    $_SESSION['usuario_id'] = (int)$dados['id'];

    header("Location: ../index.php");
    exit;

} catch (Throwable $e) {
    http_response_code(500);
    exit('Erro interno');
}
