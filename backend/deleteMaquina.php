<?php
    include __DIR__ . '/conexao.php';

    date_default_timezone_set('America/Sao_Paulo');
    $dataCriacao = date('Y-m-d H:i:s');

    $token = $_GET['token'] ?? '';
    $id = base64_decode($_GET['id'] ?? '');
    $propriedade = $_GET['propriedade'] ?? '';

    if(md5($id."bazinga123") <> $token){
        echo "acesso Negado";
        exit;
    }else{
        $sqlDelete = $conn->prepare("UPDATE maquina SET status= 0 WHERE id = :id");
        $sqlDelete->execute([':id' => $id]);

        $idUsuario = 123;
        $acao = "Apagou a maquina: ".$id;
        
        $ip = $_SERVER['REMOTE_ADDR'];

        $sqlInsert3 = $conn->prepare("INSERT INTO registro_acao(id_usuario, acao, data, ip) VALUES (:idUser, :acao, :data, :ip)");
        $sqlInsert3->execute([
            ':idUser' => $idUsuario,
            ':acao'   => $acao,
            ':data'   => $dataCriacao,
            ':ip'     => $ip
        ]);

        header("Location: ../detalhes-propriedade.php?id=".$propriedade);
        exit;
    }

?>