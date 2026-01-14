<?php
    include __DIR__ . '/conexao.php';

    if(!empty($_GET['token'])){
        $token = $_GET['token'];
        
        $sql = $conn->prepare("UPDATE login_registro SET token=NULL WHERE token = :token");
        $sql->execute([':token' => $token]);
        
        header('location: ../dispositivos-ativos.php');
        exit;
    }

?>