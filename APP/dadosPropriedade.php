<?php
    ini_set('display_errors', 0);
    error_reporting(E_ALL);

    header('Content-Type: application/json; charset=utf-8');

    try {
        if (!file_exists(__DIR__ . '/../backend/conexao.php')) {
            throw new Exception("Arquivo de conexão não encontrado.");
        }
        include __DIR__ . '/../backend/conexao.php';
        
        date_default_timezone_set('America/Sao_paulo');
        
        $token = filter_input(INPUT_POST, 'token', FILTER_SANITIZE_SPECIAL_CHARS);

        if (!$token) {
            throw new Exception("Token não informado.");
        }

        $sqlBusca = $conn->prepare("SELECT * FROM login_registro WHERE token = :token");
        $sqlBusca->execute([':token' => $token]);
        $dados = $sqlBusca->fetch(PDO::FETCH_ASSOC);
        
        if (!$dados) {
            echo json_encode(array('response'=> 'error', 'msg' => 'Token inválido'));
            exit;
        }

        $idUsuario = $dados['id_usuario'];
    
        $sqlBuscaTipo = $conn->query("SELECT * FROM usuarios WHERE id= '$idUsuario'"); 
        $tipo = $sqlBuscaTipo->fetch(PDO::FETCH_ASSOC);
        
        if (empty($tipo['tipo'])) {
            $sqlPropriedades = $conn->query("SELECT propriedades.id, propriedades.nome FROM propriedades INNER JOIN relacao_usuario_propriedade ON Propriedades.id = relacao_usuario_propriedade.id_propriedade WHERE relacao_usuario_propriedade.status = 1 AND relacao_usuario_propriedade.id_usuario = '$idUsuario' AND propriedades.status = 1");
        } else {
            $sqlPropriedades = $conn->query("SELECT id, nome FROM propriedades WHERE status = 1");
        }
        
        $dataCorte = '2022-01-12';
        $sqlUltimaSafra = $conn->query("SELECT safra.id, safra.descricao, culturas.cultura FROM safra LEFT JOIN culturas ON safra.id_cultura = culturas.id WHERE safra.data_inicio < date('$dataCorte') ORDER BY safra.data_fim DESC LIMIT 1");
        
        if (!$sqlUltimaSafra) {
            throw new Exception("Erro ao consultar safra: " . print_r($conn->errorInfo(), true));
        }

        $ultimaSafra = $sqlUltimaSafra->fetch(PDO::FETCH_ASSOC);

=        if (!$ultimaSafra) {
            echo json_encode(array('response' => 'error', 'msg' => 'Nenhuma safra encontrada para o período.'));
            exit;
        }

        $safra = $ultimaSafra['id'];
        $safraDesc = $ultimaSafra['descricao'];
        
        if (empty($ultimaSafra['cultura'])) {
            throw new Exception("Safra encontrada, mas sem cultura definida.");
        }
        $nomeTabela = 'dados_'.strtolower($ultimaSafra['cultura']);

        $listaPro = [];
        $lisPro = (object)[]; 
        $listaMaq = [];
        $listaTal = [];
        $listaPerdaPropriedade = [];
        $gra_maq_perda = (object)[]; 
        $gra_ta_perda = (object)[]; 

        while ($listPropriedade = $sqlPropriedades->fetch(PDO::FETCH_ASSOC)) {
            $idPropriedade = $listPropriedade['id'];
            
            $idPropEncoded = base64_encode($idPropriedade);
            
            $listaPro[] = base64_encode($idPropriedade."-".$listPropriedade['nome']);
            $lisPro->$idPropEncoded = base64_encode($listPropriedade['nome']);
            
            $sqlMaquina = $conn->query("SELECT id, nome, modelo FROM maquina WHERE id_propriedade = '$idPropriedade' AND status = 1");
            $sqlTalhao = $conn->query("SELECT id, nome FROM talhao WHERE id_propriedade = '$idPropriedade' AND status = 1");
            
            $sqlPerdaTalhao = $conn->query("SELECT * FROM (SELECT id_talhao, AVG(perda_total) AS medidatalhao FROM $nomeTabela WHERE id_propriedade = '$idPropriedade' AND id_safra = '$safra' AND perda_total > 0 GROUP BY id_talhao ORDER BY medidatalhao DESC LIMIT 5) as a INNER JOIN talhao ON a.id_talhao = talhao.id");
            
            $sqlPerdaMaquina = $conn->query("SELECT a.mediamaquina, maquina.nome, maquina.modelo FROM (SELECT id_maquina, AVG(perda_total) AS mediamaquina FROM $nomeTabela WHERE id_propriedade = '$idPropriedade' AND id_safra = '$safra' AND perda_total > 0 GROUP BY id_maquina ORDER BY mediamaquina DESC LIMIT 5) as a INNER JOIN maquina ON a.id_maquina = maquina.id");
            
            $sqlPerdaTalhaoMedia = $conn->query("SELECT * FROM (SELECT id_talhao, AVG(perda_total) AS medidatalhao FROM $nomeTabela WHERE id_propriedade = '$idPropriedade' AND id_safra = '$safra' AND perda_total > 0 GROUP BY id_talhao ORDER BY medidatalhao DESC) as a INNER JOIN talhao ON a.id_talhao = talhao.id");

            $listaMaqUni = [base64_encode("--") => base64_encode("Selecione...")];
            if ($sqlMaquina) {
                while($listMaquina = $sqlMaquina->fetch(PDO::FETCH_ASSOC)){
                    $listaMaqUni[base64_encode($listMaquina['id'])] = base64_encode($listMaquina['nome']."-".$listMaquina['modelo']);
                }
            }

            $listaTalUni = [base64_encode("--") => base64_encode("Selecione...")];
            if ($sqlTalhao) {
                while($listTalhao = $sqlTalhao->fetch(PDO::FETCH_ASSOC)){
                    $listaTalUni[base64_encode($listTalhao['id'])] = base64_encode($listTalhao['nome']);
                }
            }

            $listaMaqPerda = [];
            if ($sqlPerdaMaquina) {
                while($listPerMaq = $sqlPerdaMaquina->fetch(PDO::FETCH_ASSOC)){
                    $listaMaqPerda[base64_encode($listPerMaq['nome'])] = base64_encode($listPerMaq['mediamaquina']);
                }
            }

            $listaTaPerda = [];
            if ($sqlPerdaTalhao) {
                while($listPerTa = $sqlPerdaTalhao->fetch(PDO::FETCH_ASSOC)){
                    $listaTaPerda[base64_encode($listPerTa['nome'])] = base64_encode($listPerTa['medidatalhao']);
                }
            }

            $perdaAculumada = 0;
            $areaAcumulada = 0;
            if ($sqlPerdaTalhaoMedia) {
                while($listPerda = $sqlPerdaTalhaoMedia->fetch(PDO::FETCH_ASSOC)){
                    $perdaAculumada += ($listPerda['medidatalhao'] * $listPerda['area']);
                    $areaAcumulada += $listPerda['area'];
                }
            }
            
            $mediaPonderadaTalhao = ($areaAcumulada > 0) ? ($perdaAculumada/$areaAcumulada) : 0;
            
            $listaMaq[$idPropEncoded] = $listaMaqUni;
            $listaTal[$idPropEncoded] = $listaTalUni;
            $listaPerdaPropriedade[$idPropEncoded] = base64_encode(number_format($mediaPonderadaTalhao, 2, ',', '.'));
            
            if(!empty($listaMaqPerda)){
                $gra_maq_perda->$idPropEncoded = $listaMaqPerda;
            }
            if(!empty($listaTaPerda)){
                $gra_ta_perda->$idPropEncoded = $listaTaPerda;
            }
        }
        
        $envio = array(
            'response' => 'success', 
            'listPro' => $lisPro,
            'propriedade' => $listaPro,
            'talhao' => $listaTal, 
            'maquina' => $listaMaq, 
            'gra_maq_perda' => $gra_maq_perda, 
            'gra_ta_perda' => $gra_ta_perda,
            'safra' => base64_encode($safraDesc),
            'perda_propriedade' => $listaPerdaPropriedade
        );
                      
        echo json_encode($envio);

    } catch (Exception $e) {
        http_response_code(200);
        echo json_encode(array(
            'response' => 'error', 
            'msg' => 'Erro no Servidor: ' . $e->getMessage()
        ));
    }
?>