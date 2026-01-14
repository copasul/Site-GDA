<?php
    include __DIR__ . '/conexao.php';
    date_default_timezone_set('America/Sao_Paulo');
    $dataCriacao = date('Y-m-d H:i:s');
    $usuario = 'joaoK';

    $descricaoSafra = filter_input(INPUT_POST, 'descricaoSafra', FILTER_SANITIZE_SPECIAL_CHARS);
    $dataInicio = filter_input(INPUT_POST, 'dataInicio', FILTER_SANITIZE_SPECIAL_CHARS);
    $dataFinal = filter_input(INPUT_POST, 'dataFinal', FILTER_SANITIZE_SPECIAL_CHARS);
    $cultura = filter_input(INPUT_POST, 'cultura', FILTER_SANITIZE_SPECIAL_CHARS);
    
    $sqlInsert = $conn->prepare("INSERT INTO safra(id_cultura, descricao, data_inicio, data_fim) VALUES (:cultura, :descricaoSafra, :dataInicio, :dataFinal)");
    $sqlInsert->bindParam(':descricaoSafra', $descricaoSafra);       
    $sqlInsert->bindParam(':dataInicio', $dataInicio);
    $sqlInsert->bindParam(':dataFinal', $dataFinal);
    $sqlInsert->bindParam(':cultura', $cultura);
    $sqlInsert->execute();

    $idUsuario = 123;
    $acao = "Cadastrou a safra ".$descricaoSafra;
    $ip = $_SERVER['REMOTE_ADDR'];

    $sqlInsert3 = $conn->query("INSERT INTO registro_acao(id_usuario, acao, data, ip) VALUES ('$idUsuario', '$acao', '$dataCriacao', '$ip')");

    header("Location: ../safras.php?status=created_ok")
?>