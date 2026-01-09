<?php 
    include __DIR__ . '../backend/conexao.php';
    date_default_timezone_set('America/Sao_paulo');
    $dataAtual = date("Y-m-d H:i:s");
    
    
    $sql = $conn->query("UPDATE login_registro SET token=null WHERE validade < date('$dataAtual')");



?>