<?php

include __DIR__ . '/../backend/conexao.php';

$token = filter_input(INPUT_GET, 'token', FILTER_SANITIZE_SPECIAL_CHARS);
$cultura = filter_input(INPUT_GET, 'cultura', FILTER_SANITIZE_SPECIAL_CHARS);
$type = filter_input(INPUT_GET, 'type', FILTER_SANITIZE_SPECIAL_CHARS);

if($type == 1){
    $propriedade = filter_input(INPUT_GET, 'propriedade', FILTER_SANITIZE_SPECIAL_CHARS);
    $safra = filter_input(INPUT_GET, 'safra', FILTER_SANITIZE_SPECIAL_CHARS);
    if($cultura == 1){
        $sqlBusca = $conn->prepare("SELECT * FROM dados_milho WHERE id_propriedade = :propriedade AND id_safra = :safra");
        $sqlBusca->bindParam(':propriedade', $propriedade);
        $sqlBusca->bindParam(':safra', $safra);
        $sqlBusca->execute();
        $dados = $sqlBusca->fetch(PDO::FETCH_ASSOC);
        echo json_encode($dados);

    }elseif($cultura == 2){
        $sqlBusca = $conn->prepare("SELECT * FROM dados_soja WHERE id_propriedade = :propriedade AND id_safra = :safra");
        $sqlBusca->bindParam(':propriedade', $propriedade);
        $sqlBusca->bindParam(':safra', $safra);
        $sqlBusca->execute();
        $dados = $sqlBusca->fetch(PDO::FETCH_ASSOC);
        echo json_encode($dados);
    }
    
}elseif($type == 2){
    $id = filter_input(INPUT_GET, 'id_registro', FILTER_SANITIZE_SPECIAL_CHARS);
    if($cultura == 1){
        $sqlBusca = $conn->prepare("SELECT * FROM dados_milho WHERE id = :id");
        $sqlBusca->bindParam(':id', $id);
        $sqlBusca->execute();
        $dados = $sqlBusca->fetch(PDO::FETCH_ASSOC);
        echo json_encode($dados);

    }elseif($cultura == 2){
        $sqlBusca = $conn->prepare("SELECT * FROM dados_soja WHERE id = :id");
        $sqlBusca->bindParam(':id', $id);
        $sqlBusca->execute();
        $dados = $sqlBusca->fetch(PDO::FETCH_ASSOC);
        echo json_encode($dados);
    }
}


















