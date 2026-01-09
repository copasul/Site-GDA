<?php
    include __DIR__ . '../backend/conexao.php';
    date_default_timezone_set('America/Sao_Paulo');
    $dataCriacao = date('Y-m-d H:i:s');
    $usuario = 'joaoK';

    $idSafra = base64_decode(filter_input(INPUT_POST, 'idSafra', FILTER_SANITIZE_STRING));
    $descricaoSafra = filter_input(INPUT_POST, 'descricaoSafra', FILTER_SANITIZE_STRING);
    $dataInicio = filter_input(INPUT_POST, 'dataInicio', FILTER_SANITIZE_STRING);
    $dataFinal = filter_input(INPUT_POST, 'dataFinal', FILTER_SANITIZE_STRING);
    $cultura = filter_input(INPUT_POST, 'cultura', FILTER_SANITIZE_STRING);

    
    $sqlInsert = $conn->prepare("UPDATE safra SET id_cultura=:cultura,descricao=:descricaoSafra,data_inicio=:dataInicio,data_fim=:dataFinal WHERE id = :idSafra");
    $sqlInsert->bindParam(':descricaoSafra', $descricaoSafra);       
    $sqlInsert->bindParam(':dataInicio', $dataInicio);
    $sqlInsert->bindParam(':dataFinal', $dataFinal);
    $sqlInsert->bindParam(':cultura', $cultura);
    $sqlInsert->bindParam(':idSafra', $idSafra);
    $sqlInsert->execute();



    $idUsuario = 123;
    $acao = "Cadastrou a safra ".$descricaoSafra;
    $ip = $_SERVER['REMOTE_ADDR'];

    $sqlInsert3 = $conn->query("INSERT INTO registro_acao(id_usuario, acao, data, ip) VALUES ('$idUsuario', '$acao', '$dataCriacao', '$ip')");


    header("Location: ../safras.php?status=created_ok")

    
    
?>