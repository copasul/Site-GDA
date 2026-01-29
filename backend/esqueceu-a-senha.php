<?php
// Exibir todos os erros na tela
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h1>Iniciando Teste de E-mail...</h1>";

require ("phpmailer/src/Exception.php");
require ("phpmailer/src/PHPMailer.php");
require ("phpmailer/src/SMTP.php");    

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$mail = new PHPMailer(true);

try {
    // Configurações do Servidor
    $mail->isSMTP();
    $mail->SMTPDebug = 2; // Mostra o log detalhado da conversa com o Google
    $mail->Debugoutput = 'html'; 
    
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->SMTPSecure = 'tls'; // Tente TLS na porta 587 (mais compatível com Vercel)
    $mail->Port = 587; 
    
    // SUAS CREDENCIAIS
    $mail->Username = 'informa@copasul.coop.br';
    
    // ATENÇÃO: Esta senha DEVE ser uma "Senha de App" do Google.
    // Se for a senha normal de login, NÃO VAI FUNCIONAR.
    $mail->Password = '5fB0qwb0BLtv'; 

    $mail->setFrom('informa@copasul.coop.br', 'Teste Copasul');
    
    // COLOQUE SEU E-MAIL PESSOAL AQUI PARA TESTAR
    $mail->addAddress('jose.gabriel@exemplo.com'); // <--- MUDE ISSO
    
    $mail->isHTML(true);
    $mail->Subject = "Teste de depuracao Vercel";
    $mail->Body    = "Se voce ler isso, o SMTP esta funcionando.";

    $mail->send();
    echo "<h1>SUCESSO! E-mail enviado.</h1>";
    
} catch (Exception $e) {
    echo "<h1>FALHA NO ENVIO</h1>";
    echo "Erro detalhado: " . $mail->ErrorInfo;
}
?>