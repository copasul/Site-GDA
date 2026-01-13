<?php

    include __DIR__ . '/conexao.php';

    date_default_timezone_set('America/Sao_Paulo');

    $dataCriacao = date('Y-m-d H:i:s');

    $usuario = 'joaoK';

    $nomePropriedade = filter_input(INPUT_POST, 'nomePropriedade', FILTER_SANITIZE_SPECIAL_CHARS);
    $latitudePropriedade = filter_input(INPUT_POST, 'latitudePropriedade', FILTER_SANITIZE_SPECIAL_CHARS);
    $longitudePropriedade = filter_input(INPUT_POST, 'longitudePropriedade', FILTER_SANITIZE_SPECIAL_CHARS);
    $cidade = filter_input(INPUT_POST, 'cidade', FILTER_SANITIZE_SPECIAL_CHARS);
    $estado = filter_input(INPUT_POST, 'estado', FILTER_SANITIZE_SPECIAL_CHARS);

    $nomeMaquina = $_POST['nomeMaquina'] ?? [];
    $nomeTalhao =  $_POST['nomeTalhao'] ?? [];

    $numero_de_maquinas = count($nomeMaquina);
    $numero_de_talhao = count($nomeTalhao);

    $sqlInsert = $conn->prepare("INSERT INTO propriedades(nome, latitude, longitude, cidade, estado, data_criacao, criado_por) VALUES (:nomePropriedade, :latitudePropriedade, :longitudePropriedade, :cidade, :estado, :dataCriacao, :usuario)");

    $sqlInsert->bindParam(':nomePropriedade', $nomePropriedade);      
    $sqlInsert->bindParam(':latitudePropriedade', $latitudePropriedade);
    $sqlInsert->bindParam(':longitudePropriedade', $longitudePropriedade);
    $sqlInsert->bindParam(':cidade', $cidade);
    $sqlInsert->bindParam(':estado', $estado);
    $sqlInsert->bindParam(':dataCriacao', $dataCriacao);
    $sqlInsert->bindParam(':usuario', $usuario);

    $sqlInsert->execute();

    $idPropriedade = $conn->lastInsertId();

    for ($i = 0; $i < $numero_de_maquinas; $i++) {
        $nomeMaquinaAtual = $_POST['nomeMaquina'][$i];
        $modeloMaquina =  $_POST['modeloMaquina'][$i];
        $marcaMaquina = $_POST['marcaMaquina'][$i];
        $anoMaquina =  $_POST['anoMaquina'][$i];
        $proprietario =  $_POST['proprietario'][$i];

        if(!empty($nomeMaquinaAtual)){
            $sqlInsert2 = $conn->query("INSERT INTO maquina(nome, modelo, marca, ano_fabricacao, tipo_proprietario, id_propriedade, status) VALUES ('$nomeMaquinaAtual', '$modeloMaquina', '$marcaMaquina', '$anoMaquina', '$proprietario', '$idPropriedade', 1)");
        }
    }

    for ($i = 0; $i < $numero_de_talhao; $i++) {
        $nomeTalhaoAtual = $_POST['nomeTalhao'][$i];
        
        $numeros = explode(',', $_POST['areaTalhao'][$i]);
        $parteInteira = $numeros[0] ?? '0';
        $parteDecimal = $numeros[1] ?? '0';
        
        $areaTalhao = number_format(floatval($parteInteira.'.'.$parteDecimal), 2,'.',',');

        if(!empty($nomeTalhaoAtual)){
            $sqlInsert2 = $conn->query("INSERT INTO talhao(nome, area, id_propriedade, status) VALUES ('$nomeTalhaoAtual', '$areaTalhao', '$idPropriedade', 1)");
        }
    }

    $idUsuario = 123;
    $acao = "Cadastrou a propriedade ".$nomePropriedade;
    $ip = $_SERVER['REMOTE_ADDR'];

    $sqlInsert3 = $conn->query("INSERT INTO registro_acao(id_usuario, acao, data, ip) VALUES ('$idUsuario', '$acao', '$dataCriacao', '$ip')");

    header("Location: ../propriedades.php?status=created_ok");
?>