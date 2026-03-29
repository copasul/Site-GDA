<?php 
    include __DIR__ . '/backend/conexao.php';
    include __DIR__ . '/backend/verificaLog.php';

    $titulo = $titulo ?? 'Copasul';

    $idUsuario = $User['id'];
    
    $sqlBusca = $conn->prepare("
        SELECT * FROM login_registro 
        WHERE id_usuario = :idUsuario 
        AND token != '' 
        ORDER BY data_hora
    ");
    $sqlBusca->execute([':idUsuario' => $idUsuario]);
   
?>
<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <title><?php echo $titulo?> - Dispositivos</title>

        <?php include __DIR__ . '/head.php'; ?>
        <link href="vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
    </head>

<body id="page-top">
    <div id="wrapper">
        <?php include __DIR__ . '/nav-bar.php'; ?>
        <div id="content-wrapper" class="d-flex flex-column">

            <div id="content">

                <?php include __DIR__ . '/top-bar.php'; ?>
                <div class="container-fluid">

                    <div class="d-sm-flex align-items-center justify-content-between mb-4">
                        <h1 class="h3 mb-0 text-gray-800">Dispositivos Ativos</h1>
                    </div>
                    <?php 
                        while( $dados = $sqlBusca->fetch(PDO::FETCH_ASSOC)){
                            $tokenAtual = $tokenAcess ?? ''; 
                    ?>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card shadow mb-4">
                                <?php
                                    if($tokenAtual == $dados['token']){
                                            
                                ?>
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Este dispositivo</h6>
                                </div>
                                <?php
                                    }
                                ?>
                                <div class="card-body">
                                   <div class="row">
                                            <div class="col-md-2">
                                                <div class="card shadow mb-4">
                                                    <div class="card-body">
                                                        <img src="img/<?php if($dados['tipo_acesso'] == 'web'){ echo "notebook.png";}else{echo "smartphone.png";}?>" alt="" width="100%">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-9">
                                                <div class="row">
                                                    <h6 class="h3 m-0 font-weight-bold text-gray"><?php if($dados['tipo_acesso'] == 'web'){ echo "Computador";}else{echo "Smartphone";}?></h6>
                                                </div>
                                                <?php
                                                    if($dados['tipo_acesso']=='app'){
                                                        $marca = $dados['marca_dispositivo'] ?? $dados['marca-dispositivo'] ?? '';
                                                        $modelo = $dados['modelo_dispositivo'] ?? $dados['modelo-dispositivo'] ?? '';
                                                        $versao = $dados['versao_dispositivo'] ?? $dados['versao-dispositivo'] ?? '';
                                                ?>
                                                <div class="row">
                                                    <h6 class="m-0 text-gray"><?php echo $marca." ".$modelo." | Android ".$versao?></h6>
                                                </div>
                                                <?php
                                                    }
                                                ?>
                                                <div class="row">
                                                    <h6 class="m-0 text-gray">IP: <?php echo $dados['ip']?></h6>
                                                </div>
                                                <div class="row">
                                                    <h6 class="m-0  text-gray">Data conexão:  <?php echo date('d/m/Y H:i', strtotime($dados['data_hora']))?> (horario de Brasília)</h6>
                                                </div>
                                                <br>
                                                <?php
                                                    if($tokenAtual <> $dados['token']){
                                            
                                                ?>
                                                <div class="row">
                                                    <button class="d-none d-sm-inline-block btn btn-sm btn-danger shadow-sm" onclick="location.href='backend/matarSessao.php?token=<?php echo $dados['token']?>'">Desconectar</button>
                                                </div>
                                                <?php
                                                    }
                                                ?>
                                            </div>
                                   </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php
                        }
                    ?>
                    
                </div>
                </div>
            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>Copyright &copy; Copasul <?php echo date('Y')?></span>
                    </div>
                </div>
            </footer>
            </div>
        </div>
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Deseja sair?</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">Selecione "Sair" para confirmar!</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancelar</button>
                    <a class="btn btn-primary" href="login.html">Sair</a>
                </div>
            </div>
        </div>
    </div>

    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>

    <script src="js/sb-admin-2.min.js"></script>

    <script src="vendor/datatables/jquery.dataTables.min.js"></script>
    <script src="vendor/datatables/dataTables.bootstrap4.min.js"></script>

    <script src="js/demo/datatables-demo.js"></script>
    
    <!-- Vercel Speed Insights -->
    <script>
      window.si = window.si || function () { (window.siq = window.siq || []).push(arguments); };
    </script>
    <script defer src="/_vercel/speed-insights/script.js"></script>

</body>

</html>