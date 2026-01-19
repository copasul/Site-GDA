<?php
header('Content-Type: application/json');
date_default_timezone_set('America/Sao_paulo');

$dataAtual = date("Y/m/d H:i:s");
$validade = date("Y-m-d H:i:s", strtotime('+1 months'));


$email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_SPECIAL_CHARS);
$senha = filter_input(INPUT_POST, 'senha', FILTER_SANITIZE_SPECIAL_CHARS);

$android = filter_input(INPUT_POST, 'android', FILTER_SANITIZE_SPECIAL_CHARS);
$pais = filter_input(INPUT_POST, 'pais', FILTER_SANITIZE_SPECIAL_CHARS);
$marca = filter_input(INPUT_POST, 'marca', FILTER_SANITIZE_SPECIAL_CHARS);
$modelo = filter_input(INPUT_POST, 'modelo', FILTER_SANITIZE_SPECIAL_CHARS);


$ip = $_SERVER["REMOTE_ADDR"];
$chave = date('m/Y');
$token = md5($email."iwHv%C,z0j!qYa".$chave."app".$ip.rand(0,100));
$tipo_acesso = 'app';


$error = array(
    "response" => "error"
);
    

try{
    require_once __DIR__ . '/../backend/conexao.php'
;
    //  $sqlInsert = $conn->query("INSERT INTO teste(valor) VALUES ('$senha')");
    try{
        $sqlBusca = $conn->prepare("SELECT * FROM Usuarios WHERE email = :email AND status = 1");
        $sqlBusca->bindParam(':email', $email);
        $sqlBusca->execute();
        $dados = $sqlBusca->fetch(PDO::FETCH_ASSOC);

        if ($dados &&!empty($dados['email'])) {

            $idUsuario = $dados['id'];
            $hash = $dados['hash'];
            
            $senha = md5($senha.$hash."%wUgk3S@3yq6cqrxP%H!&CtHV*YvI$");
                
                if ($senha == $dados['senha']) {
                    $sqlAcesso = $conn->prepare("INSERT INTO login_registro(data_hora, validade, id_usuario, token, tipo_acesso, ip,  marca-dispositivo, modelo-dispositivo, versao-dispositivo, pais-dispositivo) VALUES (:dataHora, :validade,:idUsuario, :token, :tipo_acesso, :ip, :marca, :modelo, :android, :pais)");
                    $sqlAcesso->bindParam(':dataHora', $dataAtual);
                    $sqlAcesso->bindParam(':validade', $validade);
                    $sqlAcesso->bindParam(':idUsuario', $idUsuario);
                    $sqlAcesso->bindParam(':token', $token);
                    $sqlAcesso->bindParam(':tipo_acesso', $tipo_acesso);
                    $sqlAcesso->bindParam(':ip', $ip);
                    $sqlAcesso->bindParam(':android', $android);
                    $sqlAcesso->bindParam(':pais', $pais);
                    $sqlAcesso->bindParam(':marca', $marca);
                    $sqlAcesso->bindParam(':modelo', $modelo);
                    $sqlAcesso->execute();

                    $success = array(
                        "response" => "success",
                        "token" => $token,
                        "nome_usuario" => $dados['nome']
                    );
                   
			        
                    echo json_encode($success);
                    


                }else{
                    echo json_encode($error);
                }
        }else{
             echo json_encode($error);
        }
    }catch (PDOException $usuario){
        print "Erro ao buscar lista de clientes";
        print $usuario->getMessage();
    }
}catch (PDOException $erro){
    print "A conex達o com o banco deu errado: ".$erro;
}
?>