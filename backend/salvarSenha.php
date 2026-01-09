<?php
session_start();
    include __DIR__ . '../backend/conexao.php';
    $token = $_GET['token'];
    $senha = $_POST['senha'];
    date_default_timezone_set('America/Sao_paulo');
    $dataAtual = date("Y-m-d H:i:s");

    if(!empty($token)){
        $sqlBuscaToken = $conn->query("SELECT token_inicial, hash FROM Usuarios WHERE token_inicial = '$token' LIMIT 1");
        $respostaToken = $sqlBuscaToken->fetch(PDO::FETCH_ASSOC);

        if(empty($respostaToken)){
            header('Location: ../index.php');
        }

        $senhaBD = md5($senha.$respostaToken['hash']."%wUgk3S@3yq6cqrxP%H!&CtHV*YvI$");
        $sqlSalvaSenha = $conn->prepare("UPDATE usuarios SET senha=:senhaBD, token_inicial= null WHERE token_inicial = :token");
        $sqlSalvaSenha->bindParam(':token', $token);
        $sqlSalvaSenha->bindParam(':senhaBD', $senhaBD);
        $sqlSalvaSenha->execute();
        
        $sqlBuscaToken = $conn->query("UPDATE esqueceu_senha SET token= null WHERE token = '$token'");
    
        header('Location: ../index.php');
    }else{
        $tokenAcess = $_SESSION['token'];
        if(empty($tokenAcess)){
            header("Location: ../login.php");
        }


        $sqlToken = $conn->query("SELECT * FROM login_registro WHERE token = '$tokenAcess' AND validade > date('$dataAtual')");
        $buscaToken = $sqlToken->fetch(PDO::FETCH_ASSOC);


        if(empty($buscaToken)){
            header('Location: encerrar-sessao.php');
        }
        $idUsuario = $buscaToken['id_usuario'];

        $sqlBuscaToken = $conn->query("SELECT hash FROM Usuarios WHERE id = '$idUsuario' LIMIT 1");
        $respostaToken = $sqlBuscaToken->fetch(PDO::FETCH_ASSOC);


        $senhaBD = md5($senha.$respostaToken['hash']."%wUgk3S@3yq6cqrxP%H!&CtHV*YvI$");
        $sqlSalvaSenha = $conn->prepare("UPDATE usuarios SET senha=:senhaBD WHERE id = :idUsuario");
        $sqlSalvaSenha->bindParam(':idUsuario', $idUsuario);
        $sqlSalvaSenha->bindParam(':senhaBD', $senhaBD);
        $sqlSalvaSenha->execute();
        

        header('Location: ../perfil.php');
    }
    



?>