<?php
    include __DIR__ . '../backend/conexao.php';
    date_default_timezone_set('America/Sao_Paulo');
    $dataCriacao = date('Y-m-d H:i:s');
    $usuario = 'joaoK';

    $id = base64_decode(filter_input(INPUT_GET, 'id', FILTER_SANITIZE_STRING));
    $nome = filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_STRING);
    $telefone = filter_input(INPUT_POST, 'telefone', FILTER_SANITIZE_STRING);
    $TipoUser = filter_input(INPUT_POST, 'TipoUser', FILTER_SANITIZE_STRING);

    
    $propriedade = $_POST['propriedade'];
    $tipo =  $_POST['tipo'];
    $numero_de_propriedades = count($propriedade);
    $numero_de_tipo = count($tipo);

    if($TipoUser == "null"){
        $TipoUser = NULL;
    }


    $sqlInsert = $conn->prepare("UPDATE usuarios SET email=:email,tipo=:TipoUser,telefone=:telefone,nome=:nome WHERE id = :id");
    $sqlInsert->bindParam(':email', $email);       
    $sqlInsert->bindParam(':TipoUser', $TipoUser);
    $sqlInsert->bindParam(':telefone', $telefone);
    $sqlInsert->bindParam(':nome', $nome);
    $sqlInsert->bindParam(':id', $id);
    $sqlInsert->execute();


    if($TipoUser == NULL){
        // echo $numero_de_propriedades;
        for ($i = 0; $i < $numero_de_propriedades; $i++) {
            $idRelacao = base64_decode($_POST['idRelacao'][$i]);
            $propriedade = $_POST['propriedade'][$i];
            $tipo =  $_POST['tipo'][$i];
            echo $idRelacao;
            if(empty($idRelacao)){  
                $sqlInsert3 = $conn->query("INSERT INTO relacao_usuario_propriedade(id_usuario, id_propriedade, id_tipo, status) VALUES ('$id','$propriedade','$tipo', 1)");
            }else{
                $sqlInsert2 = $conn->query("UPDATE relacao_usuario_propriedade SET id_propriedade= '$propriedade',id_tipo='$tipo' WHERE id = '$idRelacao'");

            }
        }
    }else{
        $sqlInsert2 = $conn->query("UPDATE relacao_usuario_propriedade SET status = 0 WHERE id_usuario = '$id'");
    }
    $idUsuario = 123;
    $acao = "Cadastrou o usuário com id: ".$id;
    $ip = $_SERVER['REMOTE_ADDR'];

    $sqlInsert3 = $conn->query("INSERT INTO registro_acao(id_usuario, acao, data, ip) VALUES ('$idUsuario', '$acao', '$dataCriacao', '$ip')");


    header("Location: ../detalhes-usuario.php?id=".$_GET['id']);

    
    
?>