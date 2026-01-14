<?php
    include __DIR__ . '/conexao.php';

    date_default_timezone_set('America/Sao_Paulo');
    $dataCriacao = date('Y-m-d H:i:s');

    $token = $_GET['token'] ?? '';
    $idRaw = $_GET['id'] ?? '';
    $id = base64_decode($idRaw);
    $propriedade = $_GET['propriedade'] ?? '';

    if(md5($id."bazinga123") <> $token){
        echo "acesso Negado";
        exit;
    }else{
        $sqlDelete = $conn->prepare("UPDATE talhao SET status= 0 WHERE id = :id");
        $sqlDelete->execute([':id' => $id]);

        $idUsuario = 123;
        $acao = "Apagou o Talhão: ".$id;
        
        $ip = $_SERVER['REMOTE_ADDR'];
        
        $sqlInsert3 = $conn->prepare("INSERT INTO registro_acao(id_usuario, acao, data, ip) VALUES (:idUsuario, :acao, :data, :ip)");
        $sqlInsert3->execute([
            ':idUsuario' => $idUsuario,
            ':acao'      => $acao,
            ':data'      => $dataCriacao,
            ':ip'        => $ip
        ]);

        header("Location: ../detalhes-propriedade.php?id=".$propriedade);
        exit;
    }
?>