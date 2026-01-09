<?php
include __DIR__ . '../backend/conexao.php';
$token = filter_input(INPUT_GET, 'token', FILTER_SANITIZE_STRING);
$cultura = filter_input(INPUT_GET, 'cultura', FILTER_SANITIZE_SPECIAL_CHARS);
$UserId = filter_input(INPUT_GET, 'userId', FILTER_SANITIZE_SPECIAL_CHARS);
$property = filter_input(INPUT_GET, 'property', FILTER_SANITIZE_SPECIAL_CHARS);
$talhao = filter_input(INPUT_GET, 'talhao', FILTER_SANITIZE_SPECIAL_CHARS);
$data = filter_input(INPUT_GET, 'data', FILTER_SANITIZE_SPECIAL_CHARS);

if($token == '1234'){


    if($cultura == 1){  #Milho
        // echo "DELETE FROM dados_milho WHERE id_usuario = $UserId, id_propriedade = $property, id_talhao = $talhao, data_hora = $data";
        $sqlInsert = $conn->prepare("DELETE FROM dados_milho WHERE id_usuario = :UserId AND id_propriedade = :property AND id_talhao = :talhao AND data_hora = :data");
        $sqlInsert->bindParam(':UserId', $UserId);      
        $sqlInsert->bindParam(':property', $property);
        $sqlInsert->bindParam(':talhao', $talhao);       
        $sqlInsert->bindParam(':data', $data);
        $sqlInsert->execute();   
    
        echo json_encode(array('status' => 'ok'));
    
    
    }elseif($cultura == 2){  #Soja
    
        $sqlInsert = $conn->prepare("DELETE FROM dados_soja WHERE id_usuario = :UserId AND id_propriedade = :property AND id_talhao = :talhao AND data_hora = :data");
        $sqlInsert->bindParam(':UserId', $UserId);      
        $sqlInsert->bindParam(':property', $property);
        $sqlInsert->bindParam(':talhao', $talhao);       
        $sqlInsert->bindParam(':data', $data);
        $sqlInsert->execute();   
    
        echo json_encode(array('status' => 'ok'));
    }else{
        echo json_encode(array('status' => 'error'));
    }
}else{
    echo json_encode(array('status' => 'error'));
}