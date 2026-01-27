<?php
//conexao com o banco
    include __DIR__ . '/../backend/conexao.php';

//verifica se o metodo get está recebendo
    if(empty($_GET['propriedade'])){
        $propriedade = "";
    }else{
        $propriedade = base64_decode($_GET['propriedade']);
    }
    if(empty($_GET['safra'])){
        $safra = "";
    }else{
        $safra = base64_decode($_GET['safra']);
    }

    //busca propriedade e safra para disponibilizar na busca
    $sqlBusca = $conn->query('SELECT * FROM propriedades');
    $sqlBusca2 = $conn->query('SELECT * FROM safra');



    if($buscarDados['count'] > 0){

            $sqlBuscaTalhao = $conn->query("
                SELECT 
                    d.id_talhao, 
                    AVG(d.perda_total) AS medidatalhao,
                    t.nome,
                    t.area
                FROM $nomeTabela d
                INNER JOIN Talhao t ON d.id_talhao = t.id
                WHERE d.id_propriedade = '$propriedade' 
                AND d.id_safra = '$safra' 
                AND d.perda_total > 0 
                GROUP BY d.id_talhao, t.nome, t.area 
                ORDER BY medidatalhao DESC
            ");

            $n = 0;
            $perdaAculumada = 0;
            $areaAcumulada = 0;
            
            while($talhao = $sqlBuscaTalhao->fetch(PDO::FETCH_ASSOC)){
                $listaTalhao[$n]['id'] = $talhao['id_talhao'];
                $listaTalhao[$n]['nome'] = $talhao['nome']; 
                $listaTalhao[$n]['area'] = $talhao['area'];
                $listaTalhao[$n]['medidatalhao'] = $talhao['medidatalhao'];
                
                $listaTalhao[$n]['perdaTotal'] = ($talhao['medidatalhao'] * $talhao['area']); 
                $perdaAculumada += ($talhao['medidatalhao'] * $talhao['area']);
                $areaAcumulada += $talhao['area'];
                
                $listaPerdasMedia[] = $talhao['medidatalhao'];
                $n += 1;
            }

            // Calculo da Média Ponderada da perda
            // verificação > 0 para evitar divisão por zero
            if($areaAcumulada > 0){
                $mediaPonderadaTalhao = $perdaAculumada / $areaAcumulada;
            } else {
                $mediaPonderadaTalhao = 0;
            }

            $sqlCountMaquinas = $conn->query("SELECT COUNT(*) as count FROM (SELECT * FROM dados_milho WHERE id_propriedade = 55 AND id_safra = 4 GROUP BY id_maquina) AS maq;");
            $countMaq = $sqlCountMaquinas->fetch(PDO::FETCH_ASSOC);
        }

?>
<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <title>Propriedades</title>

        <!-- Inclusão do arquivo 'head', contendo informações gerais -->
        <?php include __DIR__ . '/../head.php'; ?>
        <link href="../vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
    </head>

<body id="page-top">
    <!-- Page Wrapper -->
    <div id="wrapper">
        <!-- Sidebar -->

        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

    
                <!-- End of Topbar -->

                <!-- Begin Page Content -->
                <div class="container-fluid">
                    <!-- Page Heading -->
                    <div class="d-sm-flex align-items-center justify-content-between mb-4">
                        <h1 class="h3 mb-0 text-gray-800">Perda na Colheita</h1>
                       
                    </div>

                    <br>  
                    <?php 
                    if(!empty($propriedade) && !empty($safra)){
                        if($buscarDados['count'] > 0){?>
                    
                    <div class="row">

                        <!-- Earnings (Monthly) Card Example -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-primary shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                                Perda Média ponderada
                                            </div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo number_format($mediaPonderadaTalhao, 2, ',', '.');?> sc/ha</div>
                                        </div>
                                        <div class="col-auto">
                                            <img src="../img/saco.png" width="100%" text-align="center">
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
                                                Área Plantada
                                            </div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800"> <?php echo number_format($areaAcumulada, 2, ',', '.');?> ha</div>
                                        </div>
                                        <div class="col-auto">
                                            <img src="../img/campo.png" width="100%" text-align="center">
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
                                               Perda Total Estimada
                                            </div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo number_format($perdaAculumada, 2, ',', '.'); ?> sc</div>
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
                                                Nº de Maquinas
                                                </div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $countMaq['count']?></div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-tractor fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>      
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Perda por talhão</h6>
                                </div>
                                <div class="card-body">
                                    <div class="chart-bar">
                                        <canvas id="BarTalhao"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Perda por Máquina</h6>
                                </div>
                                <div class="card-body">
                                    <div class="chart-bar">
                                        <canvas id="BarMaquina"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Perda na colheita por maquina (Evolução)</h6>
                                </div>
                                <div class="card-body">
                                    <div class="chart-bar">
                                        <canvas id="areaAmostra"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>        
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Lista de Talhões</h6>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                            <thead>
                                                <tr>
                                                    <th>Talhão</th>
                                                    <th>Área Total</th>
                                                    <th>Perda Média</th>
                                                    <th>Perda Total</th>
                                                </tr>
                                            </thead>
                                            <tfoot>
                                                <tr>
                                                    <th>Talhão</th>
                                                    <th>Área Total</th>
                                                    <th>Perda Média</th>
                                                    <th>Perda Total</th>

                                                </tr>
                                            </tfoot>
                                            <tbody>
                                                <?php
                                                   
                                                    foreach($listaTalhao as $dadosTalhao){
                                                ?>
                                                <tr>
                                                    <td><?php echo $dadosTalhao['nome']?></td>
                                                    <td><?php echo number_format($dadosTalhao['area'], 2, ',', '.')?> ha</td>
                                                    <td><?php echo number_format($dadosTalhao['medidatalhao'], 2, ',', '.')?> sc/ha</td>
                                                    <td><?php echo number_format($dadosTalhao['perdaTotal'], 2, ',', '.');?> sc</td>
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
                                    <h6 class="m-0 font-weight-bold text-primary">Lista de Máquinas</h6>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered" id="dataTable2" width="100%" cellspacing="0">
                                            <thead>
                                                <tr>
                                                    <th>Nome</th>
                                                    <th>Modelo</th>
                                                    <th>Perda Média</th>
                                                </tr>
                                            </thead>
                                            <tfoot>
                                                <tr>
                                                    <th>Nome</th>
                                                    <th>Modelo</th>
                                                    <th>Perda Média</th>

                                                </tr>
                                            </tfoot>
                                            <tbody>
                                                <?php
                                                $n = 0;
                                                $sqlBuscaMaquina = $conn->query("SELECT id_maquina, AVG(perda_total) AS MediaMaquina FROM $nomeTabela WHERE id_propriedade = '$propriedade' AND id_safra = '$safra' AND perda_total > 0 GROUP BY id_maquina");
                                                    while($maquina = $sqlBuscaMaquina->fetch(PDO::FETCH_ASSOC)){
                                                        $idMaquina = $maquina['id_maquina'];
                                                        $sqlBuscaInfoMaquina = $conn->query("SELECT * FROM Maquina WHERE id = '$idMaquina'");
                                                        $infoMaquina = $sqlBuscaInfoMaquina->fetch(PDO::FETCH_ASSOC);

                                                        $listaMaquina[$n]['id'] = $infoMaquina['id'];
                                                        $listaMaquina[$n]['nome'] = $infoMaquina['nome'];
                                                        $listaMaquina[$n]['modelo'] = $infoMaquina['modelo'];
                                                        $listaMaquina[$n]['MediaMaquina'] = $maquina['MediaMaquina'];
                                                        $listPerdaMaq[] = $maquina['MediaMaquina'];


                                                       $n+=1; 
                                                ?>
                                                <tr>
                                                    <td><?php echo $infoMaquina['nome']?></td>
                                                    <td><?php echo $infoMaquina['modelo']?></td>
                                                    <td><?php echo number_format($maquina['MediaMaquina'], 2, ',', ' ')?> sc/ha</td>
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
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Comparativo</h6>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered" id="dataTable4" width="100%" cellspacing="0">
                                            <thead>
                                                <tr>
                                                    <th>Talhao</th>
                                                    <?php
                                                            $sqlBuscaMaquinas2 = $conn->query("SELECT id_maquina FROM $nomeTabela WHERE id_propriedade = $propriedade AND id_safra = $safra AND perda_total > 0 GROUP BY id_maquina;");
                                                            // echo ("SELECT id_talhao, AVG(perda_total) AS medidatalhao FROM dados_milho WHERE id_propriedade = '$propriedade' AND id_safra = '$safra' GROUP BY id_talhao;");
                                                            while($Maquinas = $sqlBuscaMaquinas2->fetch(PDO::FETCH_ASSOC)){
                                                                $idMaquinas = $Maquinas['id_maquina'];

                                                                $sqlBuscaInfoMaquina2 = $conn->query("SELECT * FROM Maquina WHERE id = '$idMaquinas'");
                                                                $infoMaquina2 = $sqlBuscaInfoMaquina2->fetch(PDO::FETCH_ASSOC);
                                                        ?>
                                                            <th><?php echo $infoMaquina2['nome'].' - '.$infoMaquina2['modelo'];?></th>
                                                        <?php
                                                             }
                                                        ?>
                                                </tr>
                                            </thead>
                                            <tfoot>
                                                <tr>
                                                    <th>Talhao</th>
                                                        <?php
                                                             $sqlBuscaMaquinas2 = $conn->query("SELECT id_maquina FROM $nomeTabela WHERE id_propriedade = $propriedade AND id_safra = $safra AND perda_total > 0 GROUP BY id_maquina;");
                                                             // echo ("SELECT id_talhao, AVG(perda_total) AS medidatalhao FROM dados_milho WHERE id_propriedade = '$propriedade' AND id_safra = '$safra' GROUP BY id_talhao;");
                                                             while($Maquinas = $sqlBuscaMaquinas2->fetch(PDO::FETCH_ASSOC)){
                                                                $idMaquinas = $Maquinas['id_maquina'];
                                                                $listMaquinas[] = $idMaquinas;
                                                                $sqlBuscaInfoMaquina2 = $conn->query("SELECT * FROM Maquina WHERE id = '$idMaquinas'");
                                                                $infoMaquina2 = $sqlBuscaInfoMaquina2->fetch(PDO::FETCH_ASSOC);
                                                        ?>
                                                            <th><?php echo $infoMaquina2['nome'].' - '.$infoMaquina2['modelo'];?></th>
                                                        <?php
                                                             }
                                                        ?>
                                                    </tr>
                                            </tfoot>
                                            <tbody>
                                                     <?php
                                                    
                                                            foreach($listaTalhao as $list){
                                                        ?>
                                                <tr>
                                                        <td><?php echo $list['nome'];?></td>
                                                        <?php
                                                            $idTalhao2 = $list['id'];
                                                           
                                                            foreach($listMaquinas as $listMaq){
                                                                
                                                                $sqlBuscaMaquina2 = $conn->query("SELECT id_maquina, AVG(perda_total) AS MediaMaquina FROM $nomeTabela WHERE id_propriedade = '$propriedade' AND id_safra = '$safra' AND id_talhao = $idTalhao2 AND id_maquina = $listMaq AND perda_total > 0 GROUP BY id_maquina");
                                                                // echo "SELECT id_maquina, AVG(perda_total) AS MediaMaquina FROM dados_milho WHERE id_propriedade = '$propriedade' AND id_safra = '$safra' AND id_talhao = $idTalhao2 AND id_maquina = $listMaq AND perda_total > 0 GROUP BY id_maquina";
                                                                
                                                                $maquina2 = $sqlBuscaMaquina2->fetch(PDO::FETCH_ASSOC);
                                                               
                                                           
                                                              
                                                            ?>
                                                                
                                                                <td><?php if(!empty($maquina2['MediaMaquina'])){echo number_format($maquina2['MediaMaquina'], 2, ',', '.');}else{echo "Sem registro";}?></td>
                                                        <?php
                                                            
                                                        }
                                                        ?>
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
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Lista de registros</h6>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered" id="dataTable3" width="100%" cellspacing="0">
                                            <thead>
                                                <tr>
                                                        <th>Data</th>
                                                        <th>Talhão</th>
                                                        <th>Máquina</th>
                                                        <th>Perda Total</th>
                                                        <th>Observação</th>
                                                        <th>usuários</th>
                                                    </tr>
                                                </thead>
                                                <tfoot>
                                                    <tr>
                                                        <th>Data</th>
                                                        <th>Talhão</th>
                                                        <th>Máquina</th>
                                                        <th>Perda Total</th>
                                                        <th>Observação</th>
                                                        <th>usuários</th>
                                                    </tr>
                                                </tfoot>
                                                <tbody>
                                                    <?php
                                                         $sqlBuscaRegistro = $conn->query("SELECT * FROM $nomeTabela WHERE id_propriedade = $propriedade AND id_safra = $safra");
                                                         while($registros = $sqlBuscaRegistro->fetch(PDO::FETCH_ASSOC)){
                                                            $idTalhao = $registros['id_talhao'];
                                                            $idMaquina = $registros['id_maquina'];
                                                            $idUsuario = $registros['id_usuario'];
                                                            $sqlBuscaInfoTalhao = $conn->query("SELECT * FROM Talhao WHERE id = '$idTalhao' ");
                                                            $infoTalhao = $sqlBuscaInfoTalhao->fetch(PDO::FETCH_ASSOC);

                                                            $sqlBuscaInfoMaquina = $conn->query("SELECT * FROM Maquina WHERE id = '$idMaquina' ");
                                                            $infoMaquina = $sqlBuscaInfoMaquina->fetch(PDO::FETCH_ASSOC);

                                                            $sqlBuscaInfoUsuario = $conn->query("SELECT * FROM Usuarios WHERE id = '$idUsuario' ");
                                                            $infoUsuario = $sqlBuscaInfoUsuario->fetch(PDO::FETCH_ASSOC);
                                                             
                                                         
                                                    ?>
                                                    <tr>
                                                        <td><?php echo date('d/m/Y', strtotime($registros['data_hora']))?></td>
                                                        <td><?php echo $infoTalhao['nome'];?></td>
                                                        <td><?php echo $infoMaquina['nome']." - ".$infoMaquina['modelo']?></td>
                                                        <td><?php echo number_format($registros['perda_total'], 2,',', '.')?> sc/ha</td>
                                                        <td><?php echo $registros['obs'];?></td>
                                                        <td><?php echo $infoUsuario['nome'];?></td>
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
                    </div>
                    <?php }else{ ?>
                        <div class="row">
                            <div class="col-md-12">
                                <h3 class="display-5">Sem dados encontrados</h3>
                            </div>
                        </div>
                    <?php
                    }    
                    }else{
                    ?>
                 
                    <?php
                    }
                    ?>
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
    

    <!-- Bootstrap core JavaScript-->
    <script src="../vendor/jquery/jquery.min.js"></script>
    <script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="../vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="../js/sb-admin-2.min.js"></script>

    <!-- Page level plugins -->
    <script src="../vendor/datatables/jquery.dataTables.min.js"></script>
    <script src="../vendor/datatables/dataTables.bootstrap4.min.js"></script>

    <!-- Page level custom scripts -->
    <script src="../js/demo/datatables-demo.js"></script>
    <script src="../vendor/chart.js/Chart.min.js"></script>

    <!-- Page level custom scripts -->
    <?php include "../js/demo/barTalhao.php" ?>
    <?php include "../js/demo/area.php" ?>

    <script>
        // function editar(l, c){
        //     var campo = document.getElementById('id-'+c+'-'+l);
        //     if(campo.getAttribute('status') == "fechado"){
        //         campo.innerHTML = '<input type="text" value="'+campo.getAttribute('valor')+'" class="form-control" id="input-'+c+'-'+l+'" onfocusout="salvar('+c+','+l+')">'
        //         campo.setAttribute('status','aberto');
        //     }
        // }
        // function salvar(c, l){
        //     var input = document.getElementById('input-'+c+'-'+l);
        //     var campo = document.getElementById('id-'+c+'-'+l);
        //     campo.innerText = input.value;
        //     campo.setAttribute('status', 'fechado');
        //     campo.setAttribute('valor', input.value);

        // }
       
    </script>
</body>

</html>