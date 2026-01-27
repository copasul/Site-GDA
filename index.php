<?php
    include __DIR__ . '/backend/conexao.php';
    include __DIR__ . '/backend/verificaLog.php';

    $dataAtual = date('Y-m-d');
    
    // Lógica de Seleção de Safra
    if(empty($_GET['safra'])){
        $sqlUltimaSafra = $conn->query("SELECT * FROM safra WHERE data_inicio < date('$dataAtual') ORDER BY data_fim DESC LIMIT 1");
        $ultimaSafra = $sqlUltimaSafra->fetch(PDO::FETCH_ASSOC);
        header("Location: index.php?safra=".base64_encode($ultimaSafra['id']));
        exit; 
    }else{
        $safra = base64_decode($_GET['safra']);
    }
    
    // Dados Básicos da Safra
    $sqlBuscaSafra = $conn->query("SELECT safra.id, safra.id_cultura, culturas.id, culturas.cultura FROM safra INNER JOIN culturas ON safra.id_cultura = culturas.id WHERE safra.id = $safra");
    $ListaSafra = $sqlBuscaSafra->fetch(PDO::FETCH_ASSOC);
    $nomeTabela = 'dados_'.strtolower($ListaSafra['cultura']);

    // Variáveis iniciais
    $mediaPonderadaTalhao = 0;
    $countPro = ['num' => 0];
    $sumArea = ['area' => 0];
    $countMaq = ['num' => 0];
    $listaRankingFazendas = []; 

    $filtroUsuario = "";
    if(empty($User['tipo'])){
        $filtroUsuario = " AND t.id_propriedade IN (SELECT id_propriedade FROM relacao_usuario_propriedade WHERE id_usuario = {$User['id']} AND status = 1) ";
    }

    // Contagem de Fazendas
    $countPro = $conn->query("SELECT COUNT(DISTINCT id_propriedade) AS num FROM $nomeTabela t WHERE id_safra = $safra AND id_propriedade <> 54 $filtroUsuario")->fetch(PDO::FETCH_ASSOC);
    
    // Contagem de Máquinas
    $countMaq = $conn->query("SELECT COUNT(DISTINCT id_maquina) AS num FROM $nomeTabela t WHERE id_safra = $safra AND id_propriedade <> 54 $filtroUsuario")->fetch(PDO::FETCH_ASSOC);

    // Soma de Área
    $sqlSumArea = $conn->query("
        SELECT COALESCE(SUM(tal.area), 0) as area
        FROM (SELECT DISTINCT id_talhao FROM $nomeTabela WHERE id_safra = $safra) d
        JOIN talhao tal ON d.id_talhao = tal.id
        WHERE tal.id_propriedade <> 54 
        $filtroUsuario
    ");
    $sumArea = $sqlSumArea->fetch(PDO::FETCH_ASSOC);

    $sqlGeral = "
        SELECT 
            AVG(d.perda_total) as media_talhao,
            t.area
        FROM $nomeTabela d
        JOIN talhao t ON d.id_talhao = t.id
        WHERE d.id_safra = $safra 
          AND d.perda_total > 0 
          AND d.id_propriedade <> 54
          $filtroUsuario
        GROUP BY d.id_talhao, t.area
    ";
    
    $stmtGeral = $conn->query($sqlGeral);
    
    $acumuladoPonderado = 0;
    $acumuladoArea = 0;
    
    while($row = $stmtGeral->fetch(PDO::FETCH_ASSOC)){
        $acumuladoPonderado += ($row['media_talhao'] * $row['area']);
        $acumuladoArea += $row['area'];
    }
    
    if($acumuladoArea > 0){
        $mediaPonderadaTalhao = $acumuladoPonderado / $acumuladoArea;
    }

    $sqlRanking = "
        SELECT 
            p.id,  -- Adicionei o ID aqui caso o gráfico precise
            p.nome as nome_fazenda,
            SUM(sub.media_talhao * t.area) / NULLIF(SUM(t.area), 0) as media_ponderada
        FROM (
            SELECT id_talhao, AVG(perda_total) as media_talhao
            FROM $nomeTabela
            WHERE id_safra = $safra AND perda_total > 0
            GROUP BY id_talhao
        ) sub
        JOIN talhao t ON sub.id_talhao = t.id
        JOIN propriedades p ON t.id_propriedade = p.id
        WHERE p.id <> 54
        $filtroUsuario
        GROUP BY p.id, p.nome
        ORDER BY media_ponderada ASC
    ";
    
    $stmtRanking = $conn->query($sqlRanking);
    $listaRankingFazendas = $stmtRanking->fetchAll(PDO::FETCH_ASSOC);

    $listaPerdasMediaPropriedade = [];
    $listaPerdasMediaPro = [];
    $n = 0;

    foreach($listaRankingFazendas as $fazenda){
        $listaPerdasMediaPropriedade[$n]['id']    = $fazenda['id'];
        $listaPerdasMediaPropriedade[$n]['nome']  = $fazenda['nome_fazenda'];
        $listaPerdasMediaPropriedade[$n]['media'] = $fazenda['media_ponderada'];
        $listaPerdasMediaPro[] = $fazenda['media_ponderada'];
        
        $n++;
    }

    $sqlBuscaListaSafras = $conn->query("SELECT * FROM safra ORDER BY data_fim DESC");
?>

<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <title><?php echo $titulo?> - Dashboard</title>

        <!-- Inclusão do arquivo 'head', contendo informações gerais -->
        <?php include __DIR__ . '/head.php'; ?>
        <link href="vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
    </head>

<body id="page-top">
    <!-- Page Wrapper -->
    <div id="wrapper">
        <!-- Sidebar -->
        <?php include __DIR__ . '/nav-bar.php'; ?>
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                <?php include __DIR__ . '/top-bar.php'; ?>
                <!-- End of Topbar -->

                <!-- Begin Page Content -->
                <div class="container-fluid">

                    <!-- Page Heading -->
                    <div class="d-sm-flex align-items-center justify-content-between mb-4">
                        <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
                        <form action="index.php" method="GET">
                            <div class="form-group">
                                <select name="safra" id="safra" class="form-control" onchange="this.form.submit()">
                                    <option value="">Selecione...</option>
                                    <?php 
                                        while($listaSafras = $sqlBuscaListaSafras->fetch(PDO::FETCH_ASSOC)){
                                    ?>
                                        <option value="<?php echo base64_encode($listaSafras['id'])?>" <?php if($listaSafras['id'] == $safra){ echo "Selected";}?>><?php echo $listaSafras['descricao']?></option>
                                    <?php
                                        }
                                    ?>
                                </select>
                            </div>
                        </form>
                        <!-- <a href="#" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i
                                class="fas fa-download fa-sm text-white-50"></i> Generate Report</a> -->
                    </div>

                    <!-- Content Row -->
                    <div class="row">

                        <!-- Earnings (Monthly) Card Example -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-primary shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                                Nº Fazendas
                                            </div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $countPro['num']?></div>
                                        </div>
                                        <div class="col-auto">
                                            <img src="img/soy_dashboard.png" width="50%" text-align="center">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Earnings (Monthly) Card Example -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-success shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                                Área colhida
                                            </div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo number_format($sumArea['area'], 2, ',', '.')?>  ha</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Earnings (Monthly) Card Example -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-success shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                               Maquinas
                                            </div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo  $countMaq['num']?></div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Pending Requests Card Example -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-warning shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                                Perda Média das Fazendas
                                            </div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo number_format($mediaPonderadaTalhao,2, ',', '.');?> sc</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-comments fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Content Row -->

                    <div class="row">

                        <!-- Area Chart -->
                        <div class="col-md-6">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Perda média por Fazenda (sc/ha)</h6>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered" id="propriedadeComparativo" width="100%" cellspacing="0">
                                            <thead>
                                                <tr>
                                                    <th>Fazenda</th>
                                                    <th>Perda Média</th>
                                                </tr>
                                            </thead>
                                            <tfoot>
                                                <tr>
                                                    <th>Fazenda</th>
                                                    <th>Perda Média</th>
                                                </tr>
                                            </tfoot>
                                            <tbody>
                                                <?php
                                                // Loop simples percorrendo o array já pronto
                                                foreach($listaRankingFazendas as $fazenda){
                                                ?>
                                                <tr>
                                                    <td><?php echo $fazenda['nome_fazenda']; ?></td>
                                                    <td><?php echo number_format($fazenda['media_ponderada'], 2, ',', '.'); ?> sc/ha</td>
                                                </tr>
                                                <?php
                                                }
                                                ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Perda por Propriedade</h6>
                                </div>
                                <div class="card-body">
                                    <div class="chart-bar">
                                        <canvas id="barPropriedade"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>

                    
                    </div>

                    <!-- Content Row -->
                </div>
                <!-- /.container-fluid -->

            </div>
            <!-- End of Main Content -->

            <!-- Footer -->
            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>Copyright &copy; Copasul <?php echo date('Y')?></span>
                    </div>
                </div>
            </footer>
            <!-- End of Footer -->

        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

   

    <!-- Bootstrap core JavaScript-->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="js/sb-admin-2.min.js"></script>

    <!-- Page level plugins -->
    <script src="vendor/chart.js/Chart.min.js"></script>

    <!-- Page level custom scripts -->
    <script src="js/demo/chart-area-demo.js"></script>
    <script src="js/demo/chart-pie-demo.js"></script>

        <!-- Page level plugins -->
    <script src="vendor/datatables/jquery.dataTables.min.js"></script>
    <script src="vendor/datatables/dataTables.bootstrap4.min.js"></script>

    <!-- Page level custom scripts -->
    <script src="js/demo/datatables-demo.js"></script>

    <?php include "js/demo/barPropriedade.php" ?>

</body>

</html>