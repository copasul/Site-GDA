<?php
    include __DIR__ . '../backend/conexao.php';
	session_start();
    $token = $_SESSION['token'];

    $sqlUpdate = $conn->query("UPDATE login_registro SET token=null WHERE token = '$token'");

    $_SESSION['token'] = "";   
    header("Location: ".$urlBase."login.php");

?>