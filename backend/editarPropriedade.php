<?php
// echo '<pre>';
// var_dump($_POST);
// echo '</pre>';
    include __DIR__ . '/conexao.php';
    date_default_timezone_set('America/Sao_Paulo');
    $dataCriacao = date('Y-m-d H:i:s');
    $usuario = 'joaoK';


    $idPropriedade = base64_decode(filter_input(INPUT_POST, 'idPropriedade', FILTER_SANITIZE_SPECIAL_CHARS));
    $nomePropriedade = filter_input(INPUT_POST, 'nomePropriedade', FILTER_SANITIZE_SPECIAL_CHARS);
    $latitudePropriedade = filter_input(INPUT_POST, 'latitudePropriedade', FILTER_SANITIZE_SPECIAL_CHARS);
    $longitudePropriedade = filter_input(INPUT_POST, 'longitudePropriedade', FILTER_SANITIZE_SPECIAL_CHARS);
    $cidade = filter_input(INPUT_POST, 'cidade', FILTER_SANITIZE_SPECIAL_CHARS);
    $estado = filter_input(INPUT_POST, 'estado', FILTER_SANITIZE_SPECIAL_CHARS);
    
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
                $sqlInsert2 = $conn->prepare("INSERT INTO maquina(nome, modelo, marca, ano_fabricacao, tipo_proprietario, id_propriedade, status) VALUES (?, ?, ?, ?, ?, ?, 1)");
                $sqlInsert2->execute([$nomeMaquina, $modeloMaquina, $marcaMaquina, $anoMaquina, $proprietario, $idPropriedade]);
            }
                
            }else{
                $sqlInsert3 = $conn->prepare("UPDATE maquina SET nome=?, modelo=?, marca=?, ano_fabricacao=?, tipo_proprietario=? WHERE id = ?");
                $sqlInsert3->execute([$nomeMaquina, $modeloMaquina, $marcaMaquina, $anoMaquina, $proprietario, $idMaquina]);
        }
    }

    for ($i = 0; $i < $numero_de_talhao; $i++) {
        $idTalhao = $_POST['idTalhao'][$i];
        $nomeTalhao =  $_POST['nomeTalhao'][$i];

        $valArea = str_replace(',', '.', $_POST['areaTalhao'][$i]);
        $areaTalhao = number_format((float)$valArea, 2, '.', ',');
    
    
        if(empty($idTalhao)){
            if(!empty($nomeTalhao)){
                $sqlInsert4 = $conn->prepare("INSERT INTO talhao(nome, area, id_propriedade, status) VALUES (?, ?, ?, 1)");
                $sqlInsert4->execute([$nomeTalhao, $areaTalhao, $idPropriedade]);
            }
                
        }else{
            $sqlInsert5 = $conn->prepare("UPDATE talhao SET nome=?, area=? WHERE id = ?");
            $sqlInsert5->execute([$nomeTalhao, $areaTalhao, $idTalhao]);
        }


    }

    $idUsuario = 123;
    $acao = "Editou a propriedade ".$nomePropriedade;
    $ip = $_SERVER['REMOTE_ADDR'];

    $sqlInsert3 = $conn->prepare("INSERT INTO registro_acao(id_usuario, acao, data, ip) VALUES (?, ?, ?, ?)");
    $sqlInsert3->execute([$idUsuario, $acao, $dataCriacao, $ip]);

    header("Location: ../detalhes-propriedade.php?id=".filter_input(INPUT_POST, 'idPropriedade', FILTER_SANITIZE_SPECIAL_CHARS));

    
    
?>