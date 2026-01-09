<?php
include __DIR__ . '../backend/conexao.php';
    if(!empty($_GET['token'])){
        $token = $_GET['token'];
        $sql = $conn->query("UPDATE login_registro SET token=NULL WHERE token = '$token'");
        header('location: ../dispositivos-ativos.php');
    }

?>