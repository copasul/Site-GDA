<?php
// ARQUIVO: data_dashboard.php

// 1. Conexão e Verificação
include __DIR__ . '/backend/conexao.php';
include __DIR__ . '/backend/verificaLog.php';

// 2. Recebe os dados enviados pelo JavaScript
$safra = isset($_GET['safra']) ? $_GET['safra'] : 0;
$idUser = $User['id'];
$tipoUser = $User['tipo']; // 1 = Admin/Tecnico, Empty = Produtor (supondo baseada no seu codigo)

// 3. Define a tabela correta (Soja ou Milho)
$sqlBuscaSafra = $conn->query("SELECT safra.id_cultura, culturas.cultura FROM safra INNER JOIN culturas ON safra.id_cultura = culturas.id WHERE safra.id = $safra");
$ListaSafra = $sqlBuscaSafra->fetch(PDO::FETCH_ASSOC);

if (!$ListaSafra) {
    echo json_encode(["data" => []]);
    exit;
}
$nomeTabela = 'dados_' . strtolower($ListaSafra['cultura']);

// 4. Configuração da Paginação e Busca do DataTables
$start = isset($_GET['start']) ? intval($_GET['start']) : 0;
$length = isset($_GET['length']) ? intval($_GET['length']) : 10;
$search = isset($_GET['search']['value']) ? $_GET['search']['value'] : '';

// 5. Monta a Query Principal (Baseada no seu código original)
if (!empty($tipoUser)) {
    // ADMIN/TÉCNICO: Vê todas as fazendas exceto a ID 54
    $sqlBase = " FROM $nomeTabela t 
                 JOIN propriedades p ON t.id_propriedade = p.id 
                 WHERE t.id_safra = $safra AND t.id_propriedade <> 54 ";
} else {
    // PRODUTOR: Vê apenas as suas fazendas
    $sqlBase = " FROM $nomeTabela t 
                 JOIN relacao_usuario_propriedade r ON t.id_propriedade = r.id_propriedade
                 JOIN propriedades p ON t.id_propriedade = p.id
                 WHERE t.id_safra = $safra 
                 AND r.id_usuario = $idUser 
                 AND r.status = 1 ";
}

// Se o usuário digitou algo na busca
if (!empty($search)) {
    $sqlBase .= " AND p.nome LIKE '%$search%' ";
}

// Agrupamento para não repetir fazenda
$sqlGroup = " GROUP BY p.id, p.nome ";

// 6. Conta o total de registros (para a paginação saber quantas páginas existem)
$sqlCount = $conn->query("SELECT COUNT(DISTINCT p.id) as total $sqlBase");
$totalRecords = $sqlCount->fetch(PDO::FETCH_ASSOC)['total'];

// 7. Busca apenas os 10 registros da página atual
$sqlFinal = "SELECT DISTINCT p.id as id_propriedade, p.nome $sqlBase $sqlGroup ORDER BY p.nome ASC LIMIT $length OFFSET $start";
$stmt = $conn->query($sqlFinal);

$dadosFormatados = [];

// 8. Faz o cálculo pesado APENAS para essas 10 fazendas
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $idPropriedade = $row['id_propriedade'];
    $nomeFazenda = $row['nome'];

    // Lógica do seu cálculo original
    $sqlCalcula = $conn->query("SELECT id_talhao, AVG(perda_total) AS medidatalhao 
                                FROM $nomeTabela 
                                WHERE id_safra = '$safra' 
                                AND id_propriedade = $idPropriedade 
                                AND perda_total > 0 
                                GROUP BY id_talhao");
    
    $perdaAcumulada = 0;
    $areaAcumulada = 0;

    while ($MediaPorTalhao = $sqlCalcula->fetch(PDO::FETCH_ASSOC)) {
        $idTalhao = $MediaPorTalhao['id_talhao'];
        
        // Busca área do talhão
        $sqlInfo = $conn->query("SELECT area FROM talhao WHERE id = '$idTalhao'");
        $info = $sqlInfo->fetch(PDO::FETCH_ASSOC);

        if ($info) {
            $perdaAcumulada += ($MediaPorTalhao['medidatalhao'] * $info['area']);
            $areaAcumulada += $info['area'];
        }
    }

    // Evita divisão por zero
    $mediaFinal = ($areaAcumulada > 0) ? ($perdaAcumulada / $areaAcumulada) : 0;

    // Adiciona na lista de resposta
    $dadosFormatados[] = [
        $nomeFazenda,
        number_format($mediaFinal, 2, ',', '.') . " sc/ha"
    ];
}

// 9. Devolve para o JavaScript
echo json_encode([
    "draw" => isset($_GET['draw']) ? intval($_GET['draw']) : 1, // <--- CORRIGIDO AQUI
    "recordsTotal" => intval($totalRecords),
    "recordsFiltered" => intval($totalRecords),
    "data" => $dadosFormatados
]);
?>