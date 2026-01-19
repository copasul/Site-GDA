<?php 
    header('Content-Type: application/json');
    include __DIR__ . '/../backend/conexao.php';
    date_default_timezone_set('America/Sao_paulo');
    $dataAtual = date("Y-m-d H:i:s");

    $tokenAcess = $_POST['token'] ?? '';


    $sqlToken = $conn->query("SELECT * FROM login_registro WHERE token = '$tokenAcess' AND validade > date('$dataAtual')");
    $buscaToken = $sqlToken->fetch(PDO::FETCH_ASSOC);
    

    if(!empty($buscaToken)){
       $success = array(
            "response" => "ativo",
        );
                   
	    echo json_encode($success);
    }else{
        $sqlUpdate = $conn->query("UPDATE login_registro SET token=null WHERE token = '$tokenAcess'");
         $error = array(
            "response" => "inativo",
        );
                   
	    echo json_encode($error);
    }
?>