<?php
    include __DIR__ . '/backend/conexao.php';
    include __DIR__ . '/backend/verificaLog.php';

    
    $dataAtual = date('Y-m-d');
    if(empty($_GET['safra'])){
        $sqlUltimaSafra = $conn->query("SELECT * FROM safra WHERE data_inicio < date('$dataAtual') ORDER BY data_fim DESC LIMIT 1");
        $ultimaSafra = $sqlUltimaSafra->fetch(PDO::FETCH_ASSOC);
        header("Location: index.php?safra=".base64_encode($ultimaSafra['id']));
        $safra = $ultimaSafra['id'];
    }else{
        $safra = base64_decode($_GET['safra']);
    }
    
    $sqlBuscaSafra = $conn->query("SELECT safra.id, safra.id_cultura, culturas.id, culturas.cultura FROM safra INNER JOIN culturas ON safra.id_cultura = culturas.id WHERE safra.id = $safra");
    $ListaSafra = $sqlBuscaSafra->fetch(PDO::FETCH_ASSOC);
    $nomeTabela = 'dados_'.strtolower($ListaSafra['cultura']);


    if(!empty($User['tipo'])){
        $sqlCountFazendas = $conn->query("SELECT COUNT(DISTINCT id_propriedade) AS num
                                 FROM $nomeTabela
                                 WHERE id_safra = $safra AND id_propriedade <> 54");
        $countPro = $sqlCountFazendas->fetch(PDO::FETCH_ASSOC);

        $sqlSumArea = $conn->query("
    SELECT COALESCE(SUM(t.area),0) AS area
    FROM (
        SELECT DISTINCT id_talhao
        FROM $nomeTabela
        WHERE id_safra = $safra AND id_propriedade <> 54
    ) a
    JOIN talhao t ON t.id = a.id_talhao
");
        $sumArea = $sqlSumArea->fetch(PDO::FETCH_ASSOC);

        $sqlCountMaquinas = $conn->query("SELECT COUNT(DISTINCT id_maquina) AS num
                                 FROM $nomeTabela
                                 WHERE id_safra = $safra AND id_propriedade <> 54");
        $countMaq = $sqlCountMaquinas->fetch(PDO::FETCH_ASSOC);

        $sqlBuscaTalhao = $conn->query("SELECT id_talhao, AVG(perda_total) AS medidatalhao FROM $nomeTabela WHERE id_safra = '$safra' AND perda_total > 0 AND id_propriedade != 54 GROUP BY id_talhao ORDER BY medidatalhao DESC;");
        $n = 0;
        $perdaAculumada= 0;
        $areaAcumulada=0;
        while($talhao = $sqlBuscaTalhao->fetch(PDO::FETCH_ASSOC)){
            $idTalhao = $talhao['id_talhao'];
            $sqlBuscaInfoTalhao = $conn->query("SELECT * FROM Talhao WHERE id = '$idTalhao' ");
            $infoTalhao = $sqlBuscaInfoTalhao->fetch(PDO::FETCH_ASSOC);
            $listaTalhao[$n]['id'] = $idTalhao;
            $listaTalhao[$n]['nome'] = $infoTalhao['nome'];
            $listaTalhao[$n]['area'] = $infoTalhao['area'];
            $listaTalhao[$n]['medidatalhao'] = $talhao['medidatalhao'];
            $listaTalhao[$n]['perdaTotal'] = ($talhao['medidatalhao'] * $infoTalhao['area']); 
            $perdaAculumada += ($talhao['medidatalhao'] * $infoTalhao['area']);
            $areaAcumulada += $infoTalhao['area'];
            $listaPerdasMedia[] = $talhao['medidatalhao'];
            $n += 1;
        }


        //Calculo da Média Ponderada da perda
        if($areaAcumulada>0){
            $mediaPonderadaTalhao = $perdaAculumada/$areaAcumulada;
        }else{
            $mediaPonderadaTalhao = 0;
        }
    }else{
        $idUser = $User['id'];
        $sqlCountFazendas = $conn->query("
    SELECT COUNT(DISTINCT t.id_propriedade) AS num
    FROM $nomeTabela t
    JOIN relacao_usuario_propriedade r
      ON t.id_propriedade = r.id_propriedade
    WHERE t.id_safra = $safra
      AND r.id_usuario = $idUser
      AND r.status = 1
");
        $countPro = $sqlCountFazendas->fetch(PDO::FETCH_ASSOC);

        $sqlSumArea = $conn->query("SELECT SUM(b.area) as area FROM (SELECT * FROM (SELECT $nomeTabela.id_safra, $nomeTabela.id_talhao FROM $nomeTabela WHERE $nomeTabela.id_safra = $safra GROUP BY $nomeTabela.id_talhao) As a INNER JOIN Talhao ON a.id_talhao = Talhao.id) as b INNER JOIN relacao_usuario_propriedade ON b.id_propriedade = relacao_usuario_propriedade.id_propriedade WHERE relacao_usuario_propriedade.id_usuario = $idUser AND relacao_usuario_propriedade.status= 1;");
        $sumArea = $sqlSumArea->fetch(PDO::FETCH_ASSOC);

        $sqlCountMaquinas = $conn->query("
    SELECT COUNT(DISTINCT t.id_maquina) AS num
    FROM $nomeTabela t
    JOIN relacao_usuario_propriedade r
      ON t.id_propriedade = r.id_propriedade
    WHERE t.id_safra = $safra
      AND r.id_usuario = $idUser
      AND r.status = 1
");
        $countMaq = $sqlCountMaquinas->fetch(PDO::FETCH_ASSOC);

        $sqlBuscaTalhao = $conn->query("SELECT * FROM (SELECT * FROM (SELECT $nomeTabela.id_safra, $nomeTabela.id_talhao, AVG($nomeTabela.perda_total) as medidatalhao FROM $nomeTabela WHERE $nomeTabela.id_safra = $safra AND $nomeTabela.perda_total > 0 GROUP BY $nomeTabela.id_talhao) As a INNER JOIN Talhao ON a.id_talhao = Talhao.id) as b INNER JOIN relacao_usuario_propriedade ON b.id_propriedade = relacao_usuario_propriedade.id_propriedade WHERE relacao_usuario_propriedade.id_usuario = $idUser AND relacao_usuario_propriedade.status= 1;");
        $n = 0;
        $perdaAculumada= 0;
        $areaAcumulada=0;
        while($talhao = $sqlBuscaTalhao->fetch(PDO::FETCH_ASSOC)){
            $idTalhao = $talhao['id_talhao'];
            $sqlBuscaInfoTalhao = $conn->query("SELECT * FROM Talhao WHERE id = '$idTalhao' ");
            $infoTalhao = $sqlBuscaInfoTalhao->fetch(PDO::FETCH_ASSOC);
            $listaTalhao[$n]['id'] = $idTalhao;
            $listaTalhao[$n]['nome'] = $infoTalhao['nome'];
            $listaTalhao[$n]['area'] = $infoTalhao['area'];
            $listaTalhao[$n]['medidatalhao'] = $talhao['medidatalhao'];
            $listaTalhao[$n]['perdaTotal'] = ($talhao['medidatalhao'] * $infoTalhao['area']); 
            $perdaAculumada += ($talhao['medidatalhao'] * $infoTalhao['area']);
            $areaAcumulada += $infoTalhao['area'];
            $listaPerdasMedia[] = $talhao['medidatalhao'];
            $n += 1;
        }


        //Calculo da Média Ponderada da perda
        if($areaAcumulada>0){
            $mediaPonderadaTalhao = $perdaAculumada/$areaAcumulada;
        }else{
            $mediaPonderadaTalhao = 0;
        }
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
                                                   $n = 0;
                                                   if(!empty($User['tipo'])){
                                                        $sqlComparativoFazendas = $conn->query("
  SELECT DISTINCT id_propriedade
  FROM $nomeTabela
  WHERE id_safra = $safra AND id_propriedade <> 54
");

                                                   }else{
                                                        $sqlComparativoFazendas = $conn->query("
  SELECT DISTINCT t.id_propriedade
  FROM $nomeTabela t
  JOIN relacao_usuario_propriedade r ON t.id_propriedade = r.id_propriedade
  WHERE t.id_safra = $safra
    AND r.id_usuario = $idUser
    AND r.status = 1
");

                                                    }
                                                   while($listaFazendas = $sqlComparativoFazendas->fetch(PDO::FETCH_ASSOC)){
                                                        $idPropriedade = $listaFazendas['id_propriedade'];

                                                        $sqlBuscaFazenda = $conn->query("SELECT * FROM Propriedades WHERE id = $idPropriedade");
                                                        $fazenda = $sqlBuscaFazenda->fetch(PDO::FETCH_ASSOC);

                                                        $sqlCalculamedidatalhao = $conn->query("SELECT id_talhao, AVG(perda_total) AS medidatalhao FROM $nomeTabela WHERE id_safra = '$safra' AND id_propriedade = $idPropriedade AND perda_total > 0 GROUP BY id_talhao ORDER BY medidatalhao DESC;");
                                                        $perdaAculumada = 0;
                                                        $areaAcumulada =0;
                                                        while($MediaPorTalhao = $sqlCalculamedidatalhao->fetch(PDO::FETCH_ASSOC)){

                                                            $idTalhao = $MediaPorTalhao['id_talhao'];
                                                            $sqlBuscaInfoTalhao = $conn->query("SELECT * FROM Talhao WHERE id = '$idTalhao' ");
                                                            $infoTalhao = $sqlBuscaInfoTalhao->fetch(PDO::FETCH_ASSOC);
                                                            $perdaAculumada += ($MediaPorTalhao['medidatalhao'] * $infoTalhao['area']);
                                                            $areaAcumulada += $infoTalhao['area'];
 
                                                        }
                                                        
                                                        $listaPerdasMediaPropriedade[$n]['id'] = $idPropriedade;
                                                        $listaPerdasMediaPropriedade[$n]['nome'] = $fazenda['nome'];
                                                        $listaPerdasMediaPropriedade[$n]['media'] = ($perdaAculumada/$areaAcumulada);
                                                        $listaPerdasMediaPro[] = ($perdaAculumada/$areaAcumulada);
                                                        $n += 1;
                                                ?>
                                                <tr>
                                                    <td><?php echo $fazenda['nome']?></td>
                                                    <td><?php echo number_format($perdaAculumada/$areaAcumulada, 2, ',', '.')?> sc/ha</td>
                                                   
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

    <!-- Vercel Speed Insights -->
    <script>
      window.si = window.si || function () { (window.siq = window.siq || []).push(arguments); };
    </script>
    <script defer src="/_vercel/speed-insights/script.js"></script>

</body>

</html>