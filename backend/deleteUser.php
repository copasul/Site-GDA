<?php
    include __DIR__ . '/conexao.php';
    $id = base64_decode(filter_input(INPUT_GET, 'id', FILTER_SANITIZE_SPECIAL_CHARS));
    
    $sqlBusca = $conn->prepare("SELECT * FROM usuarios WHERE id = :id");
    $sqlBusca->bindParam(':id', $id);
    $sqlBusca->execute();
    $busca = $sqlBusca->fetch(PDO::FETCH_ASSOC);
    
    $email = "**".$busca['email'];
    
    $sqlInsert = $conn->prepare("UPDATE usuarios SET status = 0, email = :email, token_inicial = null WHERE id = :id; UPDATE relacao_usuario_propriedade SET status= 0 WHERE id_usuario = :id");
    $sqlInsert->bindParam(':id', $id);
    $sqlInsert->bindParam(':email', $email);
    $sqlInsert->execute();
    
    
    $sqlInsert2 = $conn->prepare("UPDATE login_registro SET token = null WHERE id_usuario = :id;");
    $sqlInsert2->bindParam(':id', $id);
    $sqlInsert2->execute();
    
    header("Location: ../usuarios.php");
?>