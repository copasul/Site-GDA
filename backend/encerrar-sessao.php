<?php
    include __DIR__ . '/conexao.php';
    
    session_start();
    
    $token = $_COOKIE['token_acesso'] ?? '';

    if (!empty($token)) {
        $sqlUpdate = $conn->prepare("UPDATE login_registro SET token=null WHERE token = :token");
        $sqlUpdate->execute([':token' => $token]);
    }

    $_SESSION = [];
    session_destroy();

    setcookie("token_acesso", "", time() - 14400, "/");
    
    header("Location: ../login.php");
    exit;

?>