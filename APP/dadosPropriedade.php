<?php
    // CONFIGURAÇÕES INICIAIS
    // Desativa a exibição de erros no HTML para não quebrar o JSON
    ini_set('display_errors', 0);
    error_reporting(E_ALL);

    // Define que a resposta será sempre JSON
    header('Content-Type: application/json; charset=utf-8');

    try {
        // CONEXÃO
        // Usa __DIR__ para garantir que o caminho esteja correto independente de onde o script é chamado
        if (!file_exists(__DIR__ . '/../backend/conexao.php')) {
            throw new Exception("Arquivo de conexão não encontrado.");
        }
        include __DIR__ . '/../backend/conexao.php';
        
        date_default_timezone_set('America/Sao_paulo');
        
        // 1. VALIDAÇÃO DO TOKEN
        $token = filter_input(INPUT_POST, 'token', FILTER_SANITIZE_SPECIAL_CHARS);

        if (!$token) {
            throw new Exception("Token não informado.");
        }

        // Usa Prepared Statement para segurança
        $sqlBusca = $conn->prepare("SELECT * FROM login_registro WHERE token = :token");
        $sqlBusca->execute([':token' => $token]);
        $dados = $sqlBusca->fetch(PDO::FETCH_ASSOC);
        
        if (!$dados) {
            // Token inválido ou expirado
            echo json_encode(array('response'=> 'error', 'msg' => 'Token inválido'));
            exit;
        }

        // 2. BUSCA TIPO DE USUÁRIO E PROPRIEDADES
        $idUsuario = $dados['id_usuario'];
    
        $sqlBuscaTipo = $conn->query("SELECT * FROM usuarios WHERE id= '$idUsuario'"); 
        $tipo = $sqlBuscaTipo->fetch(PDO::FETCH_ASSOC);
        
        if (empty($tipo['tipo'])) { // Tipo Externo
            $sqlPropriedades = $conn->query("SELECT propriedades.id, propriedades.nome FROM propriedades INNER JOIN relacao_usuario_propriedade ON propriedades.id = relacao_usuario_propriedade.id_propriedade WHERE relacao_usuario_propriedade.status = 1 AND relacao_usuario_propriedade.id_usuario = '$idUsuario' AND propriedades.status = 1");
        } else { // Tipo Copasul
            $sqlPropriedades = $conn->query("SELECT id, nome FROM propriedades WHERE status = 1");
        }
        
        // 3. BUSCA DA SAFRA 
        $dataCorte = date('Y-m-d');
        $sqlUltimaSafra = $conn->query("SELECT safra.id, safra.descricao, culturas.cultura FROM safra LEFT JOIN culturas ON safra.id_cultura = culturas.id WHERE safra.data_inicio < date('$dataCorte') ORDER BY safra.data_fim DESC LIMIT 1");
        
        if (!$sqlUltimaSafra) {
            throw new Exception("Erro ao consultar safra: " . print_r($conn->errorInfo(), true));
        }

        $ultimaSafra = $sqlUltimaSafra->fetch(PDO::FETCH_ASSOC);

        // Se não encontrar safra, para aqui antes de quebrar o código
        if (!$ultimaSafra) {
            echo json_encode(array('response' => 'error', 'msg' => 'Nenhuma safra encontrada para o período.'));
            exit;
        }

        $safra = $ultimaSafra['id'];
        $safraDesc = $ultimaSafra['descricao'];
        
        // Verifica se veio a cultura para montar o nome da tabela
        if (empty($ultimaSafra['cultura'])) {
            throw new Exception("Safra encontrada, mas sem cultura definida.");
        }
        $nomeTabela = 'dados_'.strtolower($ultimaSafra['cultura']);

        // 4. INICIALIZAÇÃO DE ARRAYS (PARA EVITAR WARNINGS)
        $listaPro = [];
        $lisPro = (object)[]; // Objeto para JSON {}
        $listaMaq = [];
        $listaTal = [];
        $listaPerdaPropriedade = [];
        $gra_maq_perda = (object)[]; // Objeto para JSON {}
        $gra_ta_perda = (object)[];  // Objeto para JSON {}

        // 5. LOOP DE PROPRIEDADES
        while ($listPropriedade = $sqlPropriedades->fetch(PDO::FETCH_ASSOC)) {
            $idPropriedade = $listPropriedade['id'];
            
            // Cache do encode para usar como chave do array
            $idPropEncoded = base64_encode($idPropriedade);
            
            $listaPro[] = base64_encode($idPropriedade."-".$listPropriedade['nome']);
            $lisPro->$idPropEncoded = base64_encode($listPropriedade['nome']);
            
            // Queries de Máquinas e Talhões
            $sqlMaquina = $conn->query("SELECT id, nome, modelo FROM maquina WHERE id_propriedade = '$idPropriedade' AND status = 1");
            $sqlTalhao = $conn->query("SELECT id, nome FROM talhao WHERE id_propriedade = '$idPropriedade' AND status = 1");
            
            // Queries de Perdas
            $sqlPerdaTalhao = $conn->query("SELECT * FROM (SELECT id_talhao, AVG(perda_total) AS medidatalhao FROM $nomeTabela WHERE id_propriedade = '$idPropriedade' AND id_safra = '$safra' AND perda_total > 0 GROUP BY id_talhao ORDER BY medidatalhao DESC LIMIT 5) as a INNER JOIN talhao ON a.id_talhao = talhao.id");
            
            $sqlPerdaMaquina = $conn->query("SELECT a.mediamaquina, maquina.nome, Maquina.modelo FROM (SELECT id_maquina, AVG(perda_total) AS mediamaquina FROM $nomeTabela WHERE id_propriedade = '$idPropriedade' AND id_safra = '$safra' AND perda_total > 0 GROUP BY id_maquina ORDER BY mediamaquina DESC LIMIT 5) as a INNER JOIN maquina ON a.id_maquina = maquina.id");
            
            $sqlPerdaTalhaoMedia = $conn->query("SELECT * FROM (SELECT id_talhao, AVG(perda_total) AS medidatalhao FROM $nomeTabela WHERE id_propriedade = '$idPropriedade' AND id_safra = '$safra' AND perda_total > 0 GROUP BY id_talhao ORDER BY medidatalhao DESC) as a INNER JOIN talhao ON a.id_talhao = talhao.id");

            // --- Processamento Máquinas ---
            $listaMaqUni = [base64_encode("--") => base64_encode("Selecione...")];
            if ($sqlMaquina) {
                while($listMaquina = $sqlMaquina->fetch(PDO::FETCH_ASSOC)){
                    $listaMaqUni[base64_encode($listMaquina['id'])] = base64_encode($listMaquina['nome']."-".$listMaquina['modelo']);
                }
            }

            // --- Processamento Talhões ---
            $listaTalUni = [base64_encode("--") => base64_encode("Selecione...")];
            if ($sqlTalhao) {
                while($listTalhao = $sqlTalhao->fetch(PDO::FETCH_ASSOC)){
                    $listaTalUni[base64_encode($listTalhao['id'])] = base64_encode($listTalhao['nome']);
                }
            }

            // --- Processamento Perdas Máquinas ---
            $listaMaqPerda = [];
            if ($sqlPerdaMaquina) {
                while($listPerMaq = $sqlPerdaMaquina->fetch(PDO::FETCH_ASSOC)){
                    $listaMaqPerda[base64_encode($listPerMaq['nome'])] = base64_encode($listPerMaq['mediamaquina']);
                }
            }

            // --- Processamento Perdas Talhões ---
            $listaTaPerda = [];
            if ($sqlPerdaTalhao) {
                while($listPerTa = $sqlPerdaTalhao->fetch(PDO::FETCH_ASSOC)){
                    $listaTaPerda[base64_encode($listPerTa['nome'])] = base64_encode($listPerTa['medidatalhao']);
                }
            }

            // --- Cálculo Média ---
            $perdaAculumada = 0;
            $areaAcumulada = 0;
            if ($sqlPerdaTalhaoMedia) {
                while($listPerda = $sqlPerdaTalhaoMedia->fetch(PDO::FETCH_ASSOC)){
                    $perdaAculumada += ($listPerda['medidatalhao'] * $listPerda['area']);
                    $areaAcumulada += $listPerda['area'];
                }
            }
            
            $mediaPonderadaTalhao = ($areaAcumulada > 0) ? ($perdaAculumada/$areaAcumulada) : 0;
            
            // --- Popula Globais ---
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
        
        // 6. ENVIO DA RESPOSTA
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