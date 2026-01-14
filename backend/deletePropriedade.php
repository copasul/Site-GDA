<?php
    include __DIR__ . '/conexao.php';
    $id = base64_decode(filter_input(INPUT_GET, 'id', FILTER_SANITIZE_SPECIAL_CHARS));
    // echo "UPDATE propriedades SET status = 0 WHERE id = $id; UPDATE talhao SET status= 0 WHERE id_propriedade = $id; UPDATE maquina SET status= 0 WHERE id_propriedade = $id";
    $sqlProp = $conn->prepare("UPDATE propriedades SET status = 0 WHERE id = :id");
    $sqlProp->bindParam(':id', $id);
    $sqlProp->execute();

    $sqlTalhao = $conn->prepare("UPDATE talhao SET status = 0 WHERE id_propriedade = :id");
    $sqlTalhao->bindParam(':id', $id);
    $sqlTalhao->execute();

    $sqlMaq = $conn->prepare("UPDATE maquina SET status = 0 WHERE id_propriedade = :id");
    $sqlMaq->bindParam(':id', $id);
    $sqlMaq->execute();
    header("Location: ../propriedades.php");
?>