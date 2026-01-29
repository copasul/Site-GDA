<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

date_default_timezone_set('America/Sao_paulo');
$validade = date("Y-m-d H:i:s", strtotime('+12 hours'));

$email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);

$ip = $_SERVER["REMOTE_ADDR"];
$chave = date('m/Y');
// Token gerado
$token = md5($email."iwHv%C,zwgs4hh23uyuehwhqp4432rsdfrfdhsfdavcae4365iujhffwevr0j!qYa".$chave.$ip.rand(0,1000));

$urlBase = "https://site-gda.vercel.app/"; 

try {
    require_once __DIR__ . '/conexao.php';
   
    try {
        $sqlBusca = $conn->prepare("SELECT * FROM Usuarios WHERE email = :email");
        $sqlBusca->bindParam(':email', $email);
        $sqlBusca->execute();
        $dados = $sqlBusca->fetch(PDO::FETCH_ASSOC);

        // Verifica se achou o usuário
        if ($dados && !empty($dados['email'])) {
            
            $idUsuario = $dados['id'];
            
            // --- CORREÇÃO 2: Definindo a variável $nome vinda do banco ---
            // Certifique-se que a coluna no banco se chama 'nome'. Se for 'usuario', troque abaixo.
            $nome = $dados['nome']; 

            // Limpa tokens antigos
            $sql = $conn->query("UPDATE esqueceu_senha SET token= null WHERE id_usuario = '$idUsuario'");
            
            // Insere novo token
            $sqlAcesso = $conn->prepare("INSERT INTO esqueceu_senha(id_usuario, token, data_validade) VALUES (:idUsuario, :token, :validade)");
            $sqlAcesso->bindParam(':validade', $validade);
            $sqlAcesso->bindParam(':idUsuario', $idUsuario);
            $sqlAcesso->bindParam(':token', $token);
            $sqlAcesso->execute();
            
            // Atualiza token inicial (Legado?)
            $sql = $conn->query("UPDATE usuarios SET token_inicial= '$token' WHERE id = '$idUsuario'");
            
            // Monta o template
            $template = '<body>
            Olá '.$nome.'.
            <br>
            Aqui está o link para alterar a senha na plataforma de Lean na Fazenda:
            <br>
            Link de acesso: <a href="'.$urlBase.'novo-cadastro.php?action=esqueceu-senha&token='.$token.'">'.$urlBase.'novo-cadastro.php?action=esqueceu-senha&token='.$token.'</a>
            
            </body>';
            
            // Envia o e-mail
            email($email, $template);
        }

        // Redireciona (mesmo se não achar o e-mail, por segurança, para não revelar se o e-mail existe ou não)
        header("Location: ../login.php?action=enviado");
        exit; // Sempre use exit após header location

    } catch (PDOException $usuario) {
        header("Location: ../login.php?action=enviado");
        exit;
    }
} catch (PDOException $erro) {
    header("Location: ../login.php?action=enviado");
    exit;
}

function email($email, $template) {
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
    $mail->Subject = "Alterar senha | COPASUL";
    $mail->Body = $template;
    $mail->Send();
}
?>