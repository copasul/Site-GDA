<?php
    include __DIR__ . '/../backend/conexao.php';

    $sqlBusca = $conn->query("SELECT * FROM propriedades");
    $sqlBusca2 = $conn->query("SELECT * FROM safra");
?>
<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <title>Propriedades</title>

        <!-- Inclusão do arquivo 'head', contendo informações gerais -->
        <?php include __DIR__ . '../head.php'; ?>
        <link href="../vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
    </head>

<body id="page-top">
    <!-- Page Wrapper -->
    <div id="wrapper">
        <!-- Sidebar -->
        <?php include __DIR__ . '../nav-bar.php'; ?>
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                <?php include __DIR__ . '../top-bar.php'; ?>
                <!-- End of Topbar -->

                <!-- Begin Page Content -->
                <div class="container-fluid">

                    <!-- Page Heading -->
                    <div class="d-sm-flex align-items-center justify-content-between mb-4">
                        <h1 class="h3 mb-0 text-gray-800">Perda na Colheita - Carregar registros</h1>
                    </div>
                    <form action="registrar.php" method="POST" enctype="multipart/form-data">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Informações dos registros</h6>
                        </div>
                        
                            <div class="card-body">                                       
                                <div class="form-group">
                                    <label for="nomeDaPropriedade">Propriedade *</label>
                                    <select name="propriedade" id="nomeDaPropriedade" class="form-control" required>
                                        <option value="">Selecione...</option>
                                        <?php

                                            while($dados = $sqlBusca->fetch(PDO::FETCH_ASSOC)){

                                        ?>
                                            <option value="<?php echo $dados['id']?>"><?php echo $dados['nome']?></option>
                                        <?php
                                            }
                                        ?>
                                    </select>
                                </div>   
                                <div class="form-group">
                                    <label for="safra">Safra *</label>
                                    <select name="safra" id="safra" class="form-control" required>
                                        <option value="">Selecione...</option>
                                        <?php

                                            while($dados2 = $sqlBusca2->fetch(PDO::FETCH_ASSOC)){

                                        ?>
                                            <option value="<?php echo $dados2['id']?>"><?php echo $dados2['Descricao']?></option>
                                        <?php
                                            }
                                        ?>
                                    </select>
                                </div>  
                                <div class="form-group">
                                    <label for="arquivo">Arquivo CSV</label>
                                    <input type="file" name="arquivo[]" id="arquivo" class="form-control" required>
                                </div>                                   
                            </div>
                            
                        
                    </div>   
                    <div class="row">
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-primary">Carregar</button>
                            </div>
                        </div> 
                        </form>               
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

</body>

</html>