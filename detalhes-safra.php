<?php
    include __DIR__ . '/backend/conexao.php';
    include __DIR__ . '/backend/verificaLog.php';

    $titulo = $titulo ?? 'Copasul';

    $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_SPECIAL_CHARS);
    if(empty($_GET['id'])){
        $id = "";
    }else{
        $id = base64_decode($_GET['id']);
    }


    $sqlBusca = $conn->prepare('SELECT * FROM safra WHERE id = :id');
    $sqlBusca->bindParam(':id', $id);
    $sqlBusca->execute();
    $dados = $sqlBusca->fetch(PDO::FETCH_ASSOC);

    $sqlBuscas = $conn->query('SELECT * FROM culturas');

?>


<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <title><?php echo $titulo?> - Detalhes Safra</title>

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
                        <h1 class="h3 mb-0 text-gray-800"><?php echo $dados['descricao']?></h1>
                    </div>
                    <form method="POST" action="backend/editarSafra.php">
                        <input type="hidden" name="idSafra" value="<?php echo base64_encode($id);?>">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card shadow mb-4">
                                    <div class="card-header py-3">
                                        <h6 class="m-0 font-weight-bold text-primary">Dados da safra</h6>
                                    </div>
                                    <div class="card-body">
                                            <div class="form-group">
                                                <label for="descricaoSafra">Descrição*</label>
                                                <input type="text" class="form-control" id="descricaoSafra" aria-describedby="descricaoSafraHelp" name="descricaoSafra" required value="<?php echo $dados['descricao'];?>">
                                                <small id="descricaoSafraHelp" class="form-text text-muted">Será o nome que irá identificar a safra</small>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="dataInicio">Data de inicio *</label>
                                                        <input type="date" class="form-control" id="dataInicio" name="dataInicio" required value="<?php echo $dados['data_inicio'];?>">
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="dataFinal">Data Final*</label>
                                                        <input type="date" class="form-control" id="dataFinal" name="dataFinal" required value="<?php echo $dados['data_fim'];?>">
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="safra">Safra*</label>
                                                        <select class="form-control" id="cultura" name="cultura" required>
                                                            <option value="">Selecione...</option>
                                                            <?php
                                                                while($dadoss = $sqlBuscas->fetch(PDO::FETCH_ASSOC)){
                                                            ?>
                                                                <option value="<?php echo $dadoss['id']?>" <?php if($dados['id_cultura'] == $dadoss['id']){ echo "selected";} ?>  ><?php echo $dadoss['cultura']?></option>
                                                            <?php
                                                                }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        
                                    </div>
                                </div>    
                            </div>    
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-primary">Editar</button>
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
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

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
        function addLinhaMaquinas() {
            var table = document.getElementById("maquinas");
            var qnt = table.rows.length;
            var cell = table.insertRow(qnt);

            cell.innerHTML += '<input type="hidden" name="idMaquina[]" value=""><td><div class="form-group"><label for="nomeDaMaquina">Nome</label><input type="text" class="form-control" id="nomeDaMaquina" name="nomeMaquina[]"></div></td><td> <div class="form-group"> <label for="modeloDaMaquina">Modelo</label> <input type="text" class="form-control" id="modeloDaMaquina" name="modeloMaquina[]"> </div></td><td> <div class="form-group"> <label for="marcaDaMaquina">Marca</label> <input type="text" class="form-control" id="marcaDaMaquina" name="marcaMaquina[]"> </div></td><td> <div class="form-group"> <label for="anoDaMaquina">Ano de Fabricação</label> <input type="text" class="form-control" id="anoDaMaquina" name="anoMaquina[]"></div></td><td> <div class="form-group"> <label for="TipoProprietario">Tipo Proprietário</label> <select name="proprietario[]" id="TipoProprietario" class="form-control"> <option value="">Selecione...</option> <option value="proprio">Próprio</option> <option value="terceiro">Terceiro</option> </select> </div></td><td> <div class="opcoes-inf-maquinas"> <div class="opcoes-inf-maquinas2"> </div> </div></td>';
        }    
        function addLinhaTalhao() {
            var table = document.getElementById("talhao");
            var qnt = table.rows.length;
            var cell = table.insertRow(qnt);

            cell.innerHTML += '<tr id="linha-talhao-'+qnt+'"><input type="hidden" name="idTalhao[]" value=""><td> <div class="form-group"><label for="nomeDoTalhao">Nome</label> <input type="text" class="form-control" id="nomeDoTalhao" name="nomeTalhao[]"> </div> </td> <td> <div class="form-group"> <label for="areaTalhão">Área</label> <input type="number" class="form-control" id="areaTalhão" name="areaTalhao[]"> </div> </td> <td> <div class="opcoes-inf-maquinas"> <div class="opcoes-inf-maquinas2"> </div> </div> </div> </td></tr>';
        }
        function deleteMaquina(id, token){
            var resultado = confirm("Deseja excluir a máquina ?");
            if (resultado == true) {
                window.location.href = "backend/deleteMaquina.php?id="+id+"&token="+token+"&propriedade=<?php echo $_GET['id']; ?>";
            }
        }
        function deleteTalhao(id, token){
            var resultado = confirm("Deseja excluir esse talhão?");
            if (resultado == true) {
                window.location.href = "backend/deleteTalhao.php?id="+id+"&token="+token+"&propriedade=<?php echo $_GET['id']; ?>";
            }
        }
       
    </script>
</body>

</html>