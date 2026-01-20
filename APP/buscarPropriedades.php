<?php
    include __DIR__ . '/../backend/conexao.php';

    $token = filter_input(INPUT_POST, 'token', FILTER_SANITIZE_SPECIAL_CHARS);
    
    $sqlBusca = $conn->query("SELECT * FROM login_registro WHERE token= '$token'");
    $dados = $sqlBusca->fetch(PDO::FETCH_ASSOC);
    
    $idUsuario = $dados['id_usuario'];
    // echo $idUsuario;
    
    $sqlBuscaTipo = $conn->query("SELECT * FROM usuarios WHERE id= '$idUsuario'");
    $tipo = $sqlBuscaTipo->fetch(PDO::FETCH_ASSOC);
    
    if(empty($tipo['tipo'])){
        $sqlBusca2 = $conn->query("SELECT Propriedades.id, Propriedades.nome FROM propriedades INNER JOIN relacao_usuario_propriedade ON Propriedades.id = relacao_usuario_propriedade.id_propriedade WHERE relacao_usuario_propriedade.status = 1 AND relacao_usuario_propriedade.id_usuario = '$idUsuario' AND Propriedades.status = 1");
        // echo "SELECT Propriedades.id, Propriedades.nome, relacao_usuario_propriedade.id_usuario, relacao_usuario_propriedade.id_propriedade FROM propriedades INNER JOIN relacao_usuario_propriedade ON Propriedades.id = relacao_usuario_propriedade.id_propriedade WHERE relacao_usuario_propriedade.status = 1 AND relacao_usuario_propriedade.id_usuario = '$idUsuario'";
    }else{
        $sqlBusca2 = $conn->query("SELECT id, nome FROM propriedades WHERE status = 1");
    }
    while($dados2 = $sqlBusca2->fetch(PDO::FETCH_ASSOC)){
        $lista[$dados2['id']] = $dados2['id']."-".$dados2['nome'];
    }
    
    echo json_encode($lista);
    


?>