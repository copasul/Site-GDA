<?php
    header('Content-Type: application/json');
    include __DIR__ . '/../backend/conexao.php';
    date_default_timezone_set('America/Sao_paulo');
    
    
    $token = filter_input(INPUT_POST, 'token', FILTER_SANITIZE_SPECIAL_CHARS);
    //busca tokens
    $sqlBusca = $conn->query("SELECT * FROM login_registro WHERE token= '$token'");
    $dados = $sqlBusca->fetch(PDO::FETCH_ASSOC);
    
    if(empty($dados)){//se token não estiver valido
        echo json_encode(array('response'=> 'error'));
    }else{//se token estiver valido
        
        $idUsuario = $dados['id_usuario'];
    
        $sqlBuscaTipo = $conn->query("SELECT * FROM usuarios WHERE id= '$idUsuario'"); // busca tipo do usuario ('copasul ou externo')
        $tipo = $sqlBuscaTipo->fetch(PDO::FETCH_ASSOC);
        
        if(empty($tipo['tipo'])){ // tipo externo
            $sqlPropriedades = $conn->query("SELECT Propriedades.id, Propriedades.nome FROM propriedades INNER JOIN relacao_usuario_propriedade ON Propriedades.id = relacao_usuario_propriedade.id_propriedade WHERE relacao_usuario_propriedade.status = 1 AND relacao_usuario_propriedade.id_usuario = '$idUsuario' AND Propriedades.status = 1");
        //   echo "SELECT Propriedades.id, Propriedades.nome FROM propriedades INNER JOIN relacao_usuario_propriedade ON Propriedades.id = relacao_usuario_propriedade.id_propriedade WHERE relacao_usuario_propriedade.status = 1 AND relacao_usuario_propriedade.id_usuario = '$idUsuario'";
            
        }else{// tipo copasul
            $sqlPropriedades = $conn->query("SELECT id, nome FROM propriedades WHERE status = 1");
            
        }
        
        
        $dataAtual = date('Y-m-d');
        $sqlUltimaSafra = $conn->query("SELECT safra.id, safra.Descricao, culturas.cultura FROM safra LEFT JOIN culturas ON safra.id_cultura = culturas.id WHERE safra.data_inicio < date('2022-01-12') ORDER BY safra.data_fim DESC LIMIT 1");
        $ultimaSafra = $sqlUltimaSafra->fetch(PDO::FETCH_ASSOC);

        if ($ultimaSafra) {
            $safra = $ultimaSafra['id'];
            $safraDesc = $ultimaSafra['Descricao'];
            $nomeTabela = 'dados_'.strtolower($ultimaSafra['cultura']);
        } else {
            // Se não houver safra, retornamos erro ou definimos padrão para não travar
            // Aqui optei por retornar erro para você saber o que houve
            echo json_encode(array('response' => 'error', 'msg' => 'Nenhuma safra encontrada'));
            exit;
        }

        $listaPro = [];
        $lisPro = [];
        $listaMaq = [];
        $listaTal = [];
        $listaPerdaPropriedade = [];
        $gra_maq_perda = [];
        $gra_ta_perda = [];

        while($listPropriedade = $sqlPropriedades->fetch(PDO::FETCH_ASSOC)){
            $listaPro[] = base64_encode($listPropriedade['id']."-".$listPropriedade['nome']);
            $lisPro[base64_encode($listPropriedade['id'])] = base64_encode($listPropriedade['nome']);
            
            $idPropriedade = $listPropriedade['id'];
            
            // ... (MANTENHA SUAS QUERIES SQL AQUI: $sqlMaquina, $sqlTalhao, etc) ...
            $sqlMaquina = $conn->query("SELECT id, nome, modelo, id_propriedade FROM maquina WHERE id_propriedade = '$idPropriedade' AND status = 1");
            $sqlTalhao = $conn->query("SELECT id, nome, id_propriedade FROM talhao WHERE id_propriedade = '$idPropriedade' AND status = 1");
            $sqlPerdaTalhao = $conn->query("SELECT * FROM (SELECT id_talhao, AVG(perda_total) AS medidatalhao FROM $nomeTabela WHERE id_propriedade = '$idPropriedade' AND id_safra = '$safra' AND perda_total > 0 GROUP BY id_talhao ORDER BY medidatalhao DESC LIMIT 5) as A INNER JOIN Talhao ON A.id_talhao = Talhao.id");
            $sqlPerdaMaquina = $conn->query("SELECT A.MediaMaquina, Maquina.nome, Maquina.modelo FROM (SELECT id_maquina, AVG(perda_total) AS MediaMaquina FROM $nomeTabela WHERE id_propriedade = '$idPropriedade' AND id_safra = '$safra' AND perda_total > 0 GROUP BY id_maquina ORDER BY MediaMaquina DESC LIMIT 5) as A INNER JOIN Maquina ON A.id_maquina = Maquina.id");
            $sqlPerdaTalhaoMedia = $conn->query("SELECT * FROM (SELECT id_talhao, AVG(perda_total) AS medidatalhao FROM $nomeTabela WHERE id_propriedade = '$idPropriedade' AND id_safra = '$safra' AND perda_total > 0 GROUP BY id_talhao ORDER BY medidatalhao DESC) as A INNER JOIN Talhao ON A.id_talhao = Talhao.id");


            //lista as maquinas
            $listaMaqUni = []; // Zera o array localmente
            $listaMaqUni[base64_encode("--")] = base64_encode("Selecione...");
            while($listMaquina = $sqlMaquina->fetch(PDO::FETCH_ASSOC)){
                $listaMaqUni[base64_encode($listMaquina['id'])] = base64_encode($listMaquina['nome']."-".$listMaquina['modelo']);
            }
            
            //lista os talhoes
            $listaTalUni = []; // Zera o array localmente
            $listaTalUni[base64_encode("--")] = base64_encode("Selecione...");
            while($listTalhao = $sqlTalhao->fetch(PDO::FETCH_ASSOC)){
                $listaTalUni[base64_encode($listTalhao['id'])] = base64_encode($listTalhao['nome']);
            }
            
            //lista perdas maquinas
            $listaMaqPerda = []; // Zera o array localmente
            while($listPerMaq = $sqlPerdaMaquina->fetch(PDO::FETCH_ASSOC)){
                $listaMaqPerda[base64_encode($listPerMaq['nome'])] = base64_encode($listPerMaq['MediaMaquina']);
            }
            
            //lista perdas talhoes
            $listaTaPerda = []; // Zera o array localmente
            while($listPerTa = $sqlPerdaTalhao->fetch(PDO::FETCH_ASSOC)){
                $listaTaPerda[base64_encode($listPerTa['nome'])] = base64_encode($listPerTa['medidatalhao']);
            }
            
            //calcular perda media
            $perdaAculumada = 0;
            $areaAcumulada = 0;
            while($listPerda= $sqlPerdaTalhaoMedia->fetch(PDO::FETCH_ASSOC)){
                $perdaAculumada += ($listPerda['medidatalhao'] * $listPerda['area']);
                $areaAcumulada += $listPerda['area'];
            }
            if($areaAcumulada>0){
                $mediaPonderadaTalhao = $perdaAculumada/$areaAcumulada;
            }else{
                $mediaPonderadaTalhao = 0;
            }
            
            // Popula os arrays globais
            $listaMaq[base64_encode($idPropriedade)] = $listaMaqUni;
            $listaTal[base64_encode($idPropriedade)] = $listaTalUni;
            $listaPerdaPropriedade[base64_encode($idPropriedade)] = base64_encode(number_format($mediaPonderadaTalhao, 2, ',', '.'));
            
            // Verifica se tem dados antes de adicionar ao array global
            if(!empty($listaMaqPerda)){
                $gra_maq_perda[base64_encode($idPropriedade)] = $listaMaqPerda;
            } else {
                 // IMPORTANTE: Enviar vazio ao invés de indefinido para o app não quebrar
                 $gra_maq_perda[base64_encode($idPropriedade)] = []; 
            }
            
            if(!empty($listaTaPerda)){
                $gra_ta_perda[base64_encode($idPropriedade)] = $listaTaPerda;
            } else {
                 $gra_ta_perda[base64_encode($idPropriedade)] = [];
            }
        }
        
        
       
        
        if(empty($lisPro)) $lisPro = (object)[];
        
        $envio = array('listPro'=>$lisPro,
                      'propriedade'=>$listaPro,
                      'talhao'=>$listaTal, 
                      'maquina'=>$listaMaq, 
                      'gra_maq_perda'=>$gra_maq_perda, 
                      'gra_ta_perda'=>$gra_ta_perda,
                      'safra'=>base64_encode($safraDesc),
                      'perda_propriedade'=>$listaPerdaPropriedade);
                      
                       
        echo json_encode($envio);
        
        // echo "<pre>";
        // print_r($envio);
        // echo "</pre>";
    
    }
?>