<?php
header('Content-Type: application/json');
include __DIR__ . '/../backend/conexao.php';

$token = $_POST['token'] ?? ''; 

$sql = $conn->query("UPDATE login_registro SET token=NULL WHERE token = '$token'");
?>