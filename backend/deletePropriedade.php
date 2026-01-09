<?php
    include __DIR__ . '../backend/conexao.php';
    $id = base64_decode(filter_input(INPUT_GET, 'id', FILTER_SANITIZE_STRING));
    // echo "UPDATE propriedades SET status = 0 WHERE id = $id; UPDATE talhao SET status= 0 WHERE id_propriedade = $id; UPDATE maquina SET status= 0 WHERE id_propriedade = $id";
    $sqlInsert = $conn->prepare("UPDATE propriedades SET status = 0 WHERE id = :id; UPDATE talhao SET status= 0 WHERE id_propriedade = :id; UPDATE maquina SET status= 0 WHERE id_propriedade = :id");
    $sqlInsert->bindParam(':id', $id);
    $sqlInsert->execute();
    header("Location: ../propriedades.php");
?>