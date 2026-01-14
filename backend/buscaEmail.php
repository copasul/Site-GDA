<?php
include __DIR__ . '/conexao.php';

$email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_SPECIAL_CHARS);

$sqlBusca = $conn->prepare("SELECT id, email FROM Usuarios WHERE email = :email");
$sqlBusca->bindParam(':email', $email);       
$sqlBusca->execute();

$busca = $sqlBusca->fetch(PDO::FETCH_ASSOC);

if(empty($busca['id'])){
    echo "yes";
}else{
    echo "error";
}

