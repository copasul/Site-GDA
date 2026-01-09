<?php
session_start();

date_default_timezone_set('America/Campo_grande');
$dataAtual = date("Y/m/d H:i:s");
$data = date("Y-m-d");
$dataRegistro = date("Y-m-d H:i:s");
$email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
$senha = filter_input(INPUT_POST, 'senha', FILTER_SANITIZE_STRING);
$ip = $_SERVER["REMOTE_ADDR"];
$chave = date('m/Y');
$token_acesso = md5($email."iwHv%C,z0j!qYa".$chave);
$tipo_acesso = "web";
try{
    include __DIR__ . '/backend/conexao.php';
    
    try{
        $sqlBusca = $conn->prepare("SELECT * FROM Usuarios WHERE email = :email");
        $sqlBusca->bindParam(':email', $email);
        $sqlBusca->execute();
        $dados = $sqlBusca->fetch(PDO::FETCH_ASSOC);
        $id_usuario = $dados['id'];
        if (!empty($dados['email'])) {
            $hash = $dados['hash'];
                $senha = md5($senha);
                $senha = md5($senha.$hash);
                $senha = md5($senha);
                
                if ($senha == $dados['senha']) {
                    $sqlAcesso = $conn->prepare("INSERT INTO login_registro (data_hora,id_usuario,token,tipo_acesso,status,ip) VALUES (:data_hora, :id_usuario, :token, :tipo_acesso, :status, :ip)");
                    $sqlAcesso->bindParam(':data_hora', $dataAtual);
                    $sqlAcesso->bindParam(':id_usuario', $id_usuario);
                    $sqlAcesso->bindParam(':token', $token_acesso);
                    $sqlAcesso->bindParam(':tipo_acesso', $tipo_acesso);
                    $sqlAcesso->bindParam(':status', 1);
                    $sqlAcesso->bindParam(':ip', $ip);
                    $sqlAcesso->execute();

                    
                    $_SESSION['token_acesso'] = $token_acesso;

                    header("Location: ../index.php");
                }else{
                    header("Location: ../login.php?status=error");
                }
        }else{
            header("Location: ../login.php?status=error");
        }
    }catch (PDOException $usuario){
        print "Erro ao buscar lista de clientes";
        print $usuario->getMessage();
    }
}catch (PDOException $erro){
    print "A conex達o com o banco deu errado: ".$erro;
}
?>