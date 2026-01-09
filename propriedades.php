<?php
    include __DIR__ . '/backend/conexao.php';
    include __DIR__ . '/backend/verificaLog.php';

    if(empty($_GET['status'])){
        $status = "";
    }else{
        $status = $_GET['status'];
    }

    $sqlBusca = $conn->query('SELECT * FROM propriedades WHERE status = 1');

    

?>


<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <title><?php echo $titulo?> - Propriedades</title>

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
                        <h1 class="h3 mb-0 text-gray-800">Propriedades</h1>
                        <a href="add-propriedade.php" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
                            <i class="fas fa-plus fa-sm text-white-50"></i> Adicionar Propridedade</a>
                    </div>
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Lista de Propriedades</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>Nome</th>
                                            <th>Proprietário</th>
                                            <th>Área Total</th>
                                            <th>Cidade</th>
                                            <th>Estado</th>
                                            
                                        </tr>
                                    </thead>
                                    <tfoot>
                                        <tr>
                                            <th>Nome</th>
                                            <th>Proprietário</th>
                                            <th>Área Total</th>
                                            <th>Cidade</th>
                                            <th>Estado</th>
                                        </tr>
                                    </tfoot>
                                    <tbody>
                                        <?php
                                            while($dados = $sqlBusca->fetch(PDO::FETCH_ASSOC)){
                                                $idPropriedade = $dados['id'];
                                                
                                                $sqlsomatalhao = $conn->query("SELECT SUM(area) AS soma FROM talhao WHERE id_propriedade = '$idPropriedade'");
                                                $somaTalhao = $sqlsomatalhao->fetch(PDO::FETCH_ASSOC);

                                                $sqlBuscaProprietario = $conn->query("SELECT relacao_usuario_propriedade.id_usuario, relacao_usuario_propriedade.id_propriedade, relacao_usuario_propriedade.id_tipo, Usuarios.nome FROM relacao_usuario_propriedade INNER JOIN Usuarios ON relacao_usuario_propriedade.id_usuario = Usuarios.id WHERE relacao_usuario_propriedade.id_propriedade = '$idPropriedade' AND relacao_usuario_propriedade.id_tipo = 3 AND relacao_usuario_propriedade.status = 1;");
                                                
                                                
                                        ?>  
                                        <tr>
                                            <td onclick="location.href='detalhes-propriedade.php?id=<?php echo base64_encode($idPropriedade);?>'"><?php echo $dados['nome'];?></td>
                                            <td>
                                                <?php 
                                                    while($proprietarios = $sqlBuscaProprietario->fetch(PDO::FETCH_ASSOC)){
                                                       
                                                        echo $proprietarios['nome'];
                                                    }
                                                ?>
                                                </td>
                                            <td><?php echo number_format($somaTalhao['soma'],2, ',','.');?> ha</td>
                                            <td><?php echo $dados['cidade'];?></td>
                                            <td><?php echo $dados['estado'];?></td>
                                        </tr>
                                        <?php
                                            }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <?php

                        if($status == 'created_ok'){
                    ?>
                    <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 1000">
                        <div class="toast fade show" role="alert" aria-live="assertive" aria-atomic="true" id="aviso_sucesso" >
                            <div class="toast-header">
                                
                                <strong class="me-auto">Sucesso...</strong>
                                <small>Agora</small>
                                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close" onclick="fecharAviso()"></button>
                            </div>
                            <div class="toast-body">
                                Propriedade Cadastrada com sucesso.
                            </div>
                        </div>
                    </div>
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
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- Logout Modal-->
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
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
    
    <!-- Bootstrap core JavaScript-->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="js/sb-admin-2.min.js"></script>

    <!-- Page level plugins -->
    <script src="vendor/datatables/jquery.dataTables.min.js"></script>
    <script src="vendor/datatables/dataTables.bootstrap4.min.js"></script>

    <!-- Page level custom scripts -->
    <script src="js/demo/datatables-demo.js"></script>
    <script>
        function fecharAviso(){
            document.getElementById('aviso_sucesso').setAttribute('class', 'toast fade hide')
        }
    </script>
</body>

</html>