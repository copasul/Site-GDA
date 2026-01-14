<?php
    session_start();
    include __DIR__ . '/conexao.php';

    $token = $_GET['token'] ?? '';
    $senha = $_POST['senha'] ?? '';
    
    date_default_timezone_set('America/Sao_paulo');
    $dataAtual = date("Y-m-d H:i:s");

    if(!empty($token)){
        $sqlBuscaToken = $conn->prepare("SELECT token_inicial, hash FROM usuarios WHERE token_inicial = :token LIMIT 1");
        $sqlBuscaToken->execute([':token' => $token]);
        $respostaToken = $sqlBuscaToken->fetch(PDO::FETCH_ASSOC);

        if(empty($respostaToken)){
            header('Location: ../index.php');
            exit;
        }

        $senhaBD = md5($senha.$respostaToken['hash']."%wUgk3S@3yq6cqrxP%H!&CtHV*YvI$");
        
        $sqlSalvaSenha = $conn->prepare("UPDATE usuarios SET senha=:senhaBD, token_inicial= null WHERE token_inicial = :token");
        $sqlSalvaSenha->bindParam(':token', $token);
        $sqlSalvaSenha->bindParam(':senhaBD', $senhaBD);
        $sqlSalvaSenha->execute();
        
        $stmtEsqueceu = $conn->prepare("UPDATE esqueceu_senha SET token= null WHERE token = :token");
        $stmtEsqueceu->execute([':token' => $token]);
    
        header('Location: ../index.php');
        exit;

    }else{
        $tokenAcess = $_SESSION['token'] ?? '';
        
        if(empty($tokenAcess)){
            header("Location: ../login.php");
            exit;
        }

        $sqlToken = $conn->prepare("SELECT * FROM login_registro WHERE token = :token AND validade > :dataAtual");
        $sqlToken->execute([
            ':token' => $tokenAcess,
            ':dataAtual' => $dataAtual
        ]);
        $buscaToken = $sqlToken->fetch(PDO::FETCH_ASSOC);


        if(empty($buscaToken)){
            header('Location: encerrar-sessao.php');
            exit;
        }
        
        $idUsuario = $buscaToken['id_usuario'];

        $sqlBuscaToken = $conn->prepare("SELECT hash FROM usuarios WHERE id = :id LIMIT 1");
        $sqlBuscaToken->execute([':id' => $idUsuario]);
        $respostaToken = $sqlBuscaToken->fetch(PDO::FETCH_ASSOC);


        $senhaBD = md5($senha.$respostaToken['hash']."%wUgk3S@3yq6cqrxP%H!&CtHV*YvI$");
        
        $sqlSalvaSenha = $conn->prepare("UPDATE usuarios SET senha=:senhaBD WHERE id = :idUsuario");
        $sqlSalvaSenha->bindParam(':idUsuario', $idUsuario);
        $sqlSalvaSenha->bindParam(':senhaBD', $senhaBD);
        $sqlSalvaSenha->execute();
        
        header('Location: ../perfil.php');
        exit;
    }

?>