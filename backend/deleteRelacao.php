<?php
    include __DIR__ . '../backend/conexao.php';
    date_default_timezone_set('America/Sao_Paulo');
    $dataCriacao = date('Y-m-d H:i:s');


    $token = $_GET['token'];
    $id = base64_decode($_GET['id']);
    $usuario = $_GET['usuario'];

    if(md5($id."bazinga123") <> $token){
        echo "acesso Negado";
    }else{
        $sqlDelete = $conn->query("UPDATE relacao_usuario_propriedade SET status= 0 WHERE id = '$id'");


        $idUsuario = 123;
        $acao = "Apagou a relacao: ".$id;
        $ip = $_SERVER['REMOTE_ADDR'];
        $sqlInsert3 = $conn->query("INSERT INTO registro_acao(id_usuario, acao, data, ip) VALUES ('$idUsuario', '$acao', '$dataCriacao', '$ip')");

        header("Location: ../detalhes-usuario.php?id=".$usuario);
    }




?>