<?php
    include __DIR__ . '/conexao.php';
    
    session_start();
    
    $token = $_SESSION['token'] ?? '';

    if (!empty($token)) {
        $sqlUpdate = $conn->prepare("UPDATE login_registro SET token=null WHERE token = :token");
        $sqlUpdate->execute([':token' => $token]);
    }

    $_SESSION = [];
    session_destroy();
    
    header("Location: ../login.php");
    exit;

?>