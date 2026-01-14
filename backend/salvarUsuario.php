<?php
    include __DIR__ . '/conexao.php';
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;
   

    date_default_timezone_set('America/Sao_Paulo');
    $dataCriacao = date('Y-m-d H:i:s');
    $usuario = 'joaoK';

    $protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http");
    $urlBase = $protocol . "://" . $_SERVER['HTTP_HOST'] . "/";

    $nome = filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_SPECIAL_CHARS);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_SPECIAL_CHARS);
    $telefone = filter_input(INPUT_POST, 'telefone', FILTER_SANITIZE_SPECIAL_CHARS);
    $TipoUser = filter_input(INPUT_POST, 'TipoUser', FILTER_SANITIZE_SPECIAL_CHARS);

    
    $propriedade = $_POST['propriedade'];
    $tipo =  $_POST['tipo'];
    $numero_de_propriedades = count($propriedade);
    $numero_de_tipo = count($tipo);

    if($TipoUser == "null"){
        $TipoUser = NULL;
    }


    //Gerar Hash
    $hash = md5($email.rand(0, 100000)."copasul12345");
    $senha = "";

    $token = md5(date('Y-M-D').$email.$hash);
    $status = 1;


    $sqlInsert = $conn->prepare("INSERT INTO usuarios(email, tipo, hash, senha, telefone, nome, status, token_inicial) VALUES (:email, :TipoUser, :hash,  :senha, :telefone, :nome, :status, :token)");
    $sqlInsert->bindParam(':email', $email);       
    $sqlInsert->bindParam(':TipoUser', $TipoUser);
    $sqlInsert->bindParam(':hash', $hash);
    $sqlInsert->bindParam(':senha', $senha);
    $sqlInsert->bindParam(':telefone', $telefone);
    $sqlInsert->bindParam(':nome', $nome);
    $sqlInsert->bindParam(':status', $status);
    $sqlInsert->bindParam(':token', $token);
    $sqlInsert->execute();
    $idUsuario = $conn->lastInsertId();


    if($TipoUser == NULL){

        for ($i = 0; $i < $numero_de_propriedades; $i++) {
            $propriedade = $_POST['propriedade'][$i];
            $tipo =  $_POST['tipo'][$i];
            

            $sqlInsert2 = $conn->query("INSERT INTO relacao_usuario_propriedade(id_usuario, id_propriedade, id_tipo, status) VALUES ('$idUsuario','$propriedade','$tipo', 1)");
        }
    }

    $acao = "Cadastrou o usuário com id: ".$idUsuario;
    $ip = $_SERVER['REMOTE_ADDR'];

    $sqlInsert3 = $conn->query("INSERT INTO registro_acao(id_usuario, acao, data, ip) VALUES ('$idUsuario', '$acao', '$dataCriacao', '$ip')");
    
    $template = '<body>
            Olá '.$nome.'.
            <br>
            Aqui está o link para alterar a senha e realizar o login na plataforma de Lean na Fazenda:
            <br>
            Link de acesso: <a href="'.$urlBase.'novo-cadastro.php?token='.$token.'">'.$urlBase.'/novo-cadastro.php?token='.$token.'</a>
            
            </body>';
        email($email, $template);

    function email($email, $template){
        
        
        require ("phpmailer/src/Exception.php");
        require ("phpmailer/src/PHPMailer.php");
        require ("phpmailer/src/SMTP.php");    
        
        $mail = new PHPMailer();
        
        $mail->isSMTP();
        // $mail->SMTPDebug = 1;
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->SMTPSecure = 'ssl';
        $mail->Username = 'informa@copasul.coop.br';
        $mail->Password = '5fB0qwb0BLtv';
        $mail->Port = 465;
    
        $mail->setFrom('informa@copasul.coop.br', 'Copasul');
        $mail->addAddress($email);
        
        $mail->isHTML(true);
        $mail->CharSet = 'UTF-8';
        $mail->Subject = "Novo cadastro | COPASUL";
        $mail->Body = $template;
        $mail->Send();
    }

    

    header("Location: ../usuarios.php?status=created_ok");

    
    
?>