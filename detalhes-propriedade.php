<?php
    include __DIR__ . '/backend/conexao.php';
    include __DIR__ . '/backend/verificaLog.php';

    if(empty($_GET['id'])){
        $id = "";
    }else{
        $id = base64_decode($_GET['id']);
    }


    $sqlBusca = $conn->prepare('SELECT * FROM propriedades WHERE id = :id AND status = 1');
    $sqlBusca->bindParam(':id', $id);
    $sqlBusca->execute();
    $dados = $sqlBusca->fetch(PDO::FETCH_ASSOC);

    if(empty($dados)){ header("Location: propriedades.php");}
?>


<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <title><?php echo $titulo?> - Detalhes Propriedade</title>

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
                        <h1 class="h3 mb-0 text-gray-800"><?php echo $dados['nome']?></h1>
                    </div>
                    <form method="POST" action="backend/editarPropriedade.php">
                        <input type="hidden" name="idPropriedade" value="<?php echo base64_encode($id);?>">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card shadow mb-4">
                                    <div class="card-header py-3">
                                        <h6 class="m-0 font-weight-bold text-primary">Dados da propriedade</h6>
                                    </div>
                                    <div class="card-body">
                                        
                                            <div class="form-group">
                                                <label for="nomeDaPropriedade">Nome da Propriedade *</label>
                                                <input type="text" class="form-control" id="nomeDaPropriedade" aria-describedby="nomeDaPropriedadeHelp" placeholder="Digite o nome da propriedade" name="nomePropriedade" required value="<?php echo $dados['nome']?>">
                                                <!-- <small id="nomeDaPropriedadeHelp" class="form-text text-muted">Será o nome que irá identificar a propriedade</small> -->
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="latitudeDaPropriedade">Latitude da Propriedade</label>
                                                        <input type="text" class="form-control" id="latitudeDaPropriedade" aria-describedby="latitudeDaPropriedadeHelp" placeholder="Digite a latitude da propriedade" name="latitudePropriedade" value="<?php echo $dados['latitude']?>">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="longitudeDaPropriedade">Longitude da Propriedade</label>
                                                        <input type="text" class="form-control" id="longitudeDaPropriedade" aria-describedby="longitudeDaPropriedadeHelp" placeholder="Digite a longitude da propriedade" name="longitudePropriedade" value="<?php echo $dados['longitude']?>">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="cidadeDaPropriedade">Cidade da Propriedade *</label>
                                                        <input type="text" class="form-control" id="cidadeDaPropriedade" aria-describedby="cidadeDaPropriedadeHelp" placeholder="" name="cidade" required value="<?php echo $dados['cidade']?>">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="estadoDaPropriedade">Estado da Propriedade *</label>
                                                        <input type="text" class="form-control" id="estadoDaPropriedade" aria-describedby="estadoDaPropriedadeHelp" placeholder="" name="estado" required value="<?php echo $dados['estado']?>">
                                                    </div>
                                                </div>
                                            </div>
                                        
                                    </div>
                                </div>    
                            </div>    
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card shadow mb-4">
                                    <div class="card-header py-3">
                                        <h6 class="m-0 font-weight-bold text-primary">Máquinas</h6>
                                    </div>
                                    <div class="card-body" id="lista-maquinas">
                                        <table id="maquinas">
                                            <?php 
                                                $n = 0;
                                                $idPropriedade = $dados['id'];
                                                $sqlbuscaMaquinasCount = $conn->query("SELECT COUNT(*) AS NUM FROM maquina WHERE id_propriedade = '$idPropriedade' AND status = 1");
                                                $countMaquinas = $sqlbuscaMaquinasCount->fetch(PDO::FETCH_ASSOC);

                                                $sqlbuscaMaquinas = $conn->query("SELECT * FROM maquina WHERE id_propriedade = '$idPropriedade' AND status = 1");

                                                while($dadosMaquinas = $sqlbuscaMaquinas->fetch(PDO::FETCH_ASSOC)){
                                            ?>
                                            <tr>
                                                <input type="hidden" name="idMaquina[]" value="<?php echo base64_encode($dadosMaquinas['id']);?>">
                                                <td> 
                                                    <div class="form-group">
                                                        <label for="nomeDaMaquina">Nome</label>
                                                        <input type="text" class="form-control" id="nomeDaMaquina" name="nomeMaquina[]" value="<?php echo $dadosMaquinas['nome']?>">
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-group">
                                                        <label for="modeloDaMaquina">Modelo</label>
                                                        <input type="text" class="form-control" id="modeloDaMaquina" name="modeloMaquina[]" value="<?php echo $dadosMaquinas['modelo']?>">
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-group">
                                                        <label for="marcaDaMaquina">Marca</label>
                                                        <input type="text" class="form-control" id="marcaDaMaquina" name="marcaMaquina[]"  value="<?php echo $dadosMaquinas['marca']?>">
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-group">
                                                        <label for="anoDaMaquina">Ano de Fabricação</label>
                                                        <input type="text" class="form-control" id="anoDaMaquina" name="anoMaquina[]"  value="<?php echo $dadosMaquinas['ano_fabricacao']?>">
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-group">
                                                        <label for="TipoProprietario">Tipo Proprietário</label>
                                                        <select name="proprietario[]" id="TipoProprietario" class="form-control" >
                                                            <option value="">Selecione...</option>                                           
                                                            <option value="proprio" <?php if( $dadosMaquinas['tipo_proprietario'] == "proprio"){ echo "selected";}?>>Próprio</option>
                                                            <option value="terceiro" <?php if( $dadosMaquinas['tipo_proprietario'] == "terceiro"){ echo "selected";}?>>Terceiro</option>
                                                        </select>
                                                    </div>
                                                </td>
                                                <?php
                                                    if($n == 0){
                                                ?>
                                                
                                                <td>
                                                    <div class="form-group">
                                                        <i class="fas fa-plus text-primary" style="cursor:pointer; margin-right:20px;" onclick="addLinhaMaquinas()"></i>                                          
                                                    </div>
                                                </td>
                                                <?php
                                                    }
                                                    $n +=1;
                                                ?>
                                                <td>
                                                    <div class="form-group">
                                                        <i class="fas fa-trash text-danger" style="cursor:pointer; " onclick="deleteMaquina('<?php echo base64_encode($dadosMaquinas['id'])?>', '<?php echo md5($dadosMaquinas['id']."bazinga123");?>')"></i>
                                                    </div>
                                                </td>   
                                            </tr>
                                            <?php
                                                }
                                                if($countMaquinas['num'] == 0){
                                            ?>
                                            <tr>
                                            <input type="hidden" name="idMaquina[]" value="">
                                                <td> 
                                                    <div class="form-group">
                                                        <label for="nomeDaMaquina">Nome</label>
                                                        <input type="text" class="form-control" id="nomeDaMaquina" name="nomeMaquina[]">
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-group">
                                                        <label for="modeloDaMaquina">Modelo</label>
                                                        <input type="text" class="form-control" id="modeloDaMaquina" name="modeloMaquina[]">
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-group">
                                                        <label for="marcaDaMaquina">Marca</label>
                                                        <input type="text" class="form-control" id="marcaDaMaquina" name="marcaMaquina[]">
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-group">
                                                        <label for="anoDaMaquina">Ano de Fabricação</label>
                                                        <input type="text" class="form-control" id="anoDaMaquina" name="anoMaquina[]">
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-group">
                                                        <label for="TipoProprietario">Tipo Proprietário</label>
                                                        <select name="proprietario[]" id="TipoProprietario" class="form-control">
                                                            <option value="">Selecione...</option>
                                                            <option value="proprio">Próprio</option>
                                                            <option value="terceiro">Terceiro</option>
                                                        </select>
                                                    </div>
                                                </td>

                                                <td>
                                                <div class="opcoes-inf-maquinas">
                                                            <div class="opcoes-inf-maquinas2">
                                                                <i class="fas fa-plus text-primary" style="cursor:pointer; margin-right:20px;" onclick="addLinhaMaquinas()"></i>
                                                                <!-- <i class="fas fa-minus-circle text-danger" style="cursor:pointer;" onclick="addLinhaMaquinas()"></i> -->
                                                            </div>                                             
                                                        </div>
                                                </td>
                                                
                                            </tr>
                                            <?php

                                                }
                                            ?>
                                        </table>
                                        
                                    </div>
                                </div>  
                            </div>      
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card shadow mb-4">
                                    <div class="card-header py-3">
                                        <h6 class="m-0 font-weight-bold text-primary">Cadastrar Talhão</h6>
                                    </div>
                                    <div class="card-body" id="lista-talhao">
                                        <table id="talhao">
                                        <?php 
                                                $n = 0;
                                                $idPropriedade = $dados['id'];
                                                $sqlbuscaTalhaoCount = $conn->query("SELECT COUNT(*) AS NUM FROM talhao WHERE id_propriedade = '$idPropriedade' AND status = 1");
                                                $countTalhao =   $sqlbuscaTalhaoCount->fetch(PDO::FETCH_ASSOC);

                                                $sqlbuscaTalhao = $conn->query("SELECT * FROM talhao WHERE id_propriedade = '$idPropriedade' AND status = 1");

                                                while($dadosTalhao = $sqlbuscaTalhao->fetch(PDO::FETCH_ASSOC)){
                                            ?>
                                            <tr>
                                                <input type="hidden" name="idTalhao[]" value="<?php echo $dadosTalhao['id'];?>">
                                                <td> 
                                                    <div class="form-group">
                                                        <label for="nomeDoTalhao">Nome</label>
                                                        <input type="text" class="form-control" id="nomeDoTalhao" name="nomeTalhao[]" value="<?php echo $dadosTalhao['nome'];?>">
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-group">    
                                                        <label for="areaTalhão">Área</label>
                                                        <input type="text" class="form-control" id="areaTalhão" name="areaTalhao[]" value="<?php echo number_format(floatval($dadosTalhao['area']), 2, ',', '.');?>">
                                                    </div>
                                                </td>
                                    

                                                
                                                <?php
                                                    if($n == 0){
                                                ?>
                                                
                                                <td>
                                                    <div class="form-group">
                                                        <i class="fas fa-plus text-primary" style="cursor:pointer; margin-right:20px;" onclick="addLinhaTalhao()"></i>                                          
                                                    </div>
                                                </td>
                                                <?php
                                                    }
                                                    $n +=1;
                                                ?>
                                                <td>
                                                    <div class="form-group">
                                                        <i class="fas fa-trash text-danger" style="cursor:pointer; " onclick="deleteTalhao('<?php echo base64_encode($dadosTalhao['id'])?>', '<?php echo md5($dadosTalhao['id']."bazinga123");?>')"></i>
                                                    </div>
                                                </td>   
                                            </tr>
                                            <?php
                                                }
                                                if($countTalhao['num'] == 0){
                                            ?>
                                           <tr>
                                            <input type="hidden" name="idTalhao[]" value="">
                                                <td> 
                                                    <div class="form-group">
                                                        <label for="nomeDoTalhao">Nome</label>
                                                        <input type="text" class="form-control" id="nomeDoTalhao" name="nomeTalhao[]">
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-group">
                                                        <label for="areaTalhão">Área</label>
                                                        <input type="text" class="form-control" id="areaTalhão" name="areaTalhao[]">
                                                    </div>
                                                </td>
                                    
                                                <td>
                                                    <div class="opcoes-inf-maquinas">
                                                        <div class="opcoes-inf-maquinas2">
                                                            <i class="fas fa-plus text-primary" style="cursor:pointer; margin-right:20px;" onclick="addLinhaTalhao()"></i>
                                                                <!-- <i class="fas fa-minus-circle text-danger" style="cursor:pointer;" onclick="addLinhaMaquinas()"></i> -->
                                                            </div>                                             
                                                        </div>
                                                    </div>
                                                </td>
                                                
                                            </tr>   
                                            <?php

                                                }
                                            ?>
                                            
                                        </table>
                                        
                                    </div>
                                </div>  
                            </div>      
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-primary">Editar</button>
                                <a href="backend/deletePropriedade.php?id=<?php echo base64_encode($id)?>" class="btn btn-danger">Apagar</a>
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

            cell.innerHTML += '<tr id="linha-talhao-'+qnt+'"><input type="hidden" name="idTalhao[]" value=""><td> <div class="form-group"><label for="nomeDoTalhao">Nome</label> <input type="text" class="form-control" id="nomeDoTalhao" name="nomeTalhao[]"> </div> </td> <td> <div class="form-group"> <label for="areaTalhão">Área</label> <input type="text" class="form-control" id="areaTalhão" name="areaTalhao[]"> </div> </td> <td> <div class="opcoes-inf-maquinas"> <div class="opcoes-inf-maquinas2"> </div> </div> </div> </td></tr>';
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

    <!-- Vercel Speed Insights -->
    <script>
      window.si = window.si || function () { (window.siq = window.siq || []).push(arguments); };
    </script>
    <script defer src="/_vercel/speed-insights/script.js"></script>

</body>

</html>