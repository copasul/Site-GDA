<?php
    include __DIR__ . '/conexao.php';

    $token = $_GET['token'];
    $id = base64_decode($_GET['id']);
    $propriedade = $_GET['propriedade'];

    if(md5($id."bazinga123") <> $token){
        echo "acesso Negado";
    }else{
        $sqlDelete = $conn->query("UPDATE talhao SET status= 0 WHERE id = '$id'");


        $idUsuario = 123;
        $acao = "Apagou o Talhão: ".$id;
        $ip = $_SERVER['IP'];
        $sqlInsert3 = $conn->query("INSERT INTO registro_acao(id_usuario, acao, data, ip) VALUES ('$idUsuario', '$acao', '$dataCriacao', '$ip')");

        header("Location: ../detalhes-propriedade.php?id=".$propriedade);
    }




?>