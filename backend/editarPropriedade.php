<?php
// echo '<pre>';
// var_dump($_POST);
// echo '</pre>';
    include __DIR__ . '../backend/conexao.php';
    date_default_timezone_set('America/Sao_Paulo');
    $dataCriacao = date('Y-m-d H:i:s');
    $usuario = 'joaoK';


    $idPropriedade = base64_decode(filter_input(INPUT_POST, 'idPropriedade', FILTER_SANITIZE_STRING));
    $nomePropriedade = filter_input(INPUT_POST, 'nomePropriedade', FILTER_SANITIZE_STRING);
    $latitudePropriedade = filter_input(INPUT_POST, 'latitudePropriedade', FILTER_SANITIZE_STRING);
    $longitudePropriedade = filter_input(INPUT_POST, 'longitudePropriedade', FILTER_SANITIZE_STRING);
    $cidade = filter_input(INPUT_POST, 'cidade', FILTER_SANITIZE_STRING);
    $estado = filter_input(INPUT_POST, 'estado', FILTER_SANITIZE_STRING);
    
    $nomeMaquina = $_POST['nomeMaquina'];
    $nomeTalhao =  $_POST['nomeTalhao'];
    $numero_de_maquinas = count($nomeMaquina);
    $numero_de_talhao = count($nomeTalhao);

    
    $sqlInsert = $conn->prepare("UPDATE propriedades SET nome=:nomePropriedade, latitude=:latitudePropriedade,longitude=:longitudePropriedade,cidade=:cidade,estado=:estado WHERE id = :idPropriedade");
    $sqlInsert->bindParam(':nomePropriedade', $nomePropriedade);       
    $sqlInsert->bindParam(':latitudePropriedade', $latitudePropriedade);
    $sqlInsert->bindParam(':longitudePropriedade', $longitudePropriedade);
    $sqlInsert->bindParam(':cidade', $cidade);
    $sqlInsert->bindParam(':estado', $estado);
    $sqlInsert->bindParam(':idPropriedade', $idPropriedade);
    $sqlInsert->execute();


    
   
    for ($i = 0; $i < $numero_de_maquinas; $i++) {
        $idMaquina = base64_decode($_POST['idMaquina'][$i]);

        $nomeMaquina = $_POST['nomeMaquina'][$i];
        $modeloMaquina =  $_POST['modeloMaquina'][$i];
        $marcaMaquina = $_POST['marcaMaquina'][$i];
        $anoMaquina =  $_POST['anoMaquina'][$i];
        $proprietario =  $_POST['proprietario'][$i];
        
        if(empty($idMaquina)){  
            if(!empty($nomeMaquina)){
                $sqlInsert2 = $conn->query("INSERT INTO maquina(nome, modelo, marca, ano_fabricacao, tipo_proprietario, id_propriedade, status) VALUES ('$nomeMaquina', '$modeloMaquina', '$marcaMaquina', '$anoMaquina', '$proprietario', '$idPropriedade', 1)");
            }
                
            }else{
            $sqlInsert3 = $conn->query("UPDATE maquina SET nome='$nomeMaquina',modelo='$modeloMaquina',marca='$marcaMaquina',ano_fabricacao='$anoMaquina',tipo_proprietario='$proprietario' WHERE id = '$idMaquina' ");
        }
    }

    for ($i = 0; $i < $numero_de_talhao; $i++) {
        $idTalhao = $_POST['idTalhao'][$i];
        $nomeTalhao =  $_POST['nomeTalhao'][$i];
        $numeros = explode(',',$_POST['areaTalhao'][$i]);
        $areaTalhao = number_format(floatval($numeros[0].'.'.$numeros[1]), 2,'.',',');
    
    
        if(empty($idTalhao)){
            if(!empty($nomeTalhao)){
                $sqlInsert4 = $conn->query("INSERT INTO talhao(nome, area, id_propriedade, status) VALUES ('$nomeTalhao', '$areaTalhao', '$idPropriedade', 1)");
            }
                
            }else{
            $sqlInsert5 = $conn->query("UPDATE talhao SET nome='$nomeTalhao',area='$areaTalhao' WHERE id = $idTalhao");
        }


    }

    $idUsuario = 123;
    $acao = "Editou a propriedade ".$nomePropriedade;
    $ip = $_SERVER['REMOTE_ADDR'];

    $sqlInsert3 = $conn->query("INSERT INTO registro_acao(id_usuario, acao, data, ip) VALUES ('$idUsuario', '$acao', '$dataCriacao', '$ip')");


    header("Location: ../detalhes-propriedade.php?id=".filter_input(INPUT_POST, 'idPropriedade', FILTER_SANITIZE_STRING));

    
    
?>