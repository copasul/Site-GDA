<?php
    header('Content-Type: application/json');
    include __DIR__ . '/../backend/conexao.php';

    date_default_timezone_set('America/Sao_paulo');
    $dataAtual = date("Y-m-d H:i:s");
        
    $token = filter_input(INPUT_POST, 'token', FILTER_SANITIZE_SPECIAL_CHARS);
    $cultura = filter_input(INPUT_POST, 'cultura', FILTER_SANITIZE_SPECIAL_CHARS);
    $property = filter_input(INPUT_POST, 'propriedade', FILTER_SANITIZE_SPECIAL_CHARS);
    $talhao = filter_input(INPUT_POST, 'talhao', FILTER_SANITIZE_SPECIAL_CHARS);
    $machine = filter_input(INPUT_POST, 'maquina', FILTER_SANITIZE_SPECIAL_CHARS);
    $perda2m = filter_input(INPUT_POST, 'perda2m', FILTER_SANITIZE_SPECIAL_CHARS);
    $obs = filter_input(INPUT_POST, 'obs', FILTER_SANITIZE_SPECIAL_CHARS);
    $data = filter_input(INPUT_POST, 'data', FILTER_SANITIZE_SPECIAL_CHARS);
    $rotulo = filter_input(INPUT_POST, 'rotulo', FILTER_SANITIZE_SPECIAL_CHARS);

    $sqlToken = $conn->prepare("SELECT * FROM login_registro WHERE token = :token AND validade > :dataAtual");
    $sqlToken->execute([
        ':token' => $token,
        ':dataAtual' => $dataAtual
    ]);
    $buscaToken = $sqlToken->fetch(PDO::FETCH_ASSOC);

    if ($buscaToken) {
        $UserId = $buscaToken['id_usuario'];
        
        $sqlBuscaCultura = $conn->prepare("SELECT * FROM culturas WHERE cultura = :cultura");
        $sqlBuscaCultura->execute([':cultura' => $cultura]);
        $culturaBusca = $sqlBuscaCultura->fetch(PDO::FETCH_ASSOC);
        
        if (!$culturaBusca) {
            echo json_encode(['status' => 'Cultura Nao Encontrada']);
            exit;
        }

        $idCultura = $culturaBusca['id'];
        
        $sqlBusca = $conn->prepare("SELECT * FROM safra WHERE id_cultura = :idCultura AND :data BETWEEN data_inicio AND data_fim");
        $sqlBusca->execute([
            ':idCultura' => $idCultura,
            ':data' => $data
        ]);
        $dados = $sqlBusca->fetch(PDO::FETCH_ASSOC);
        
        $safra = $dados['id'] ?? null;

        if(!empty($safra)){
            
            if($cultura == "Milho"){  #Milho
                $perda30m = filter_input(INPUT_POST, 'perda30m', FILTER_SANITIZE_SPECIAL_CHARS);
                $perdaTotal = $perda2m + $perda30m;
            
                $sqlInsert = $conn->prepare("INSERT INTO dados_milho(id_usuario, data_hora, id_safra,id_propriedade, id_talhao, id_maquina, perda_2m, perda_30m, perda_total, obs) VALUES (:UserId, :data, :id_safra, :property, :talhao, :machine, :perda2m, :perda30m, :perdaTotal, :obs)");
                $sqlInsert->bindParam(':UserId', $UserId);
                $sqlInsert->bindParam(':id_safra', $safra);
                $sqlInsert->bindParam(':property', $property);
                $sqlInsert->bindParam(':talhao', $talhao);
                $sqlInsert->bindParam(':machine', $machine);
                $sqlInsert->bindParam(':perda2m', $perda2m);
                $sqlInsert->bindParam(':perda30m', $perda30m);
                $sqlInsert->bindParam(':perdaTotal', $perdaTotal);
                $sqlInsert->bindParam(':obs', $obs);
                $sqlInsert->bindParam(':data', $data);
                
                if($sqlInsert->execute()){
                    echo json_encode(array('status' => 'ok', 'rotulo'=>$rotulo));
                } else {
                    echo json_encode(array('status' => 'erro_insercao'));
                }
            
            } elseif($cultura == "Soja"){  #Soja
                $perdaTotal = $perda2m;
            
                $sqlInsert = $conn->prepare("INSERT INTO dados_soja(id_usuario, data_hora, id_safra, id_propriedade, id_talhao, id_maquina, perda_2m, perda_total, obs) VALUES (:UserId, :data, :id_safra, :property, :talhao, :machine, :perda2m, :perdaTotal, :obs)");
                $sqlInsert->bindParam(':UserId', $UserId);
                $sqlInsert->bindParam(':property', $property);
                $sqlInsert->bindParam(':id_safra', $safra);
                $sqlInsert->bindParam(':talhao', $talhao);
                $sqlInsert->bindParam(':machine', $machine);
                $sqlInsert->bindParam(':perda2m', $perda2m);
                $sqlInsert->bindParam(':perdaTotal', $perdaTotal);
                $sqlInsert->bindParam(':obs', $obs);
                $sqlInsert->bindParam(':data', $data);
                
                if($sqlInsert->execute()){
                    echo json_encode(array('status' => 'ok', 'rotulo'=>$rotulo));
                } else {
                    echo json_encode(array('status' => 'erro_insercao'));
                }

            } else {
                echo json_encode(array('status' => 'Cultura Nao Suportada'));
            }
        } else {
            echo json_encode(array('status' => 'safra nao cadastrada'));
        }
    } else {
        echo json_encode(array('status' => 'token_invalido'));
    }
?>