<?php
    include __DIR__ . '/backend/conexao.php';
    include __DIR__ . '/backend/verificaLog.php';

    $sqlBusca = $conn->query("SELECT * FROM Permissoes WHERE grupo = 'Copasul' ");


    $sqlBuscaFazendas = $conn->query("SELECT * FROM propriedades");
    $sqlBuscaFazendas2 = $conn->query("SELECT * FROM propriedades");
    $sqlBuscaTipos = $conn->query("SELECT * FROM permissoes WHERE grupo = 'Externo'");
    $sqlBuscaTipos2 = $conn->query("SELECT * FROM permissoes WHERE grupo = 'Externo'");
?>

<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <title><?php echo $titulo?> - Novo Usuário</title>

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
                        <h1 class="h3 mb-0 text-gray-800">Adicionar Usuário</h1>
                    </div>
                    <form method="POST" action="backend/salvarUsuario.php" onchange="buscaEmail()">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card shadow mb-4">
                                    <div class="card-header py-3">
                                        <h6 class="m-0 font-weight-bold text-primary">Dados do Usuário</h6>
                                    </div>
                                    <div class="card-body">  
                                            <div class="form-group">
                                                <label for="nome">Nome*</label>
                                                <input type="text" class="form-control" id="nome" name="nome" required>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="email">E-mail*</label>  
                                                        <input type="email" class="form-control" id="email" name="email" required >
                                                        <small id="emailHelp" class="form-text text-danger" style="display: none;">E-mail já utilizado</small>

                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="telefone">Telefone *</label>
                                                        <input type="tel" class="form-control" id="telefone" name="telefone" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="tipo">Tipo de Cadastro *</label>
                                                        <select class="form-control" id="tipo" name="TipoUser" required onchange="relation()">
                                                            <option value="">Selecione...</option>
                                                                <optgroup label="Interno">
                                                                    <?php
                                                                        while($dados = $sqlBusca->fetch(PDO::FETCH_ASSOC)){
                                                                    ?>
                                                                        <option value="<?php echo $dados['id']?>" tipo="<?php echo $dados['grupo']?>"><?php echo $dados['tipo']?></option>
                                                                    <?php
                                                                        }
                                                                    ?>
                                                                </optgroup>
                                                             <option value="null">Externo</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                    </div>
                                </div>    
                            </div>    
                        </div>
                        <div class="row" id="relacaoUser" style="display: none;">
                            <div class="col-md-12">
                                <div class="card shadow mb-4">
                                    <div class="card-header py-3">
                                        <h6 class="m-0 font-weight-bold text-primary">Relação Usuário X Propriedade</h6>
                                    </div>
                                    <div class="card-body" id="lista-talhao">
                                        <table id="relacao">
                                            <tr>
                                                <td> 
                                                    <div class="form-group">
                                                        <label for="propriedade">Propriedade</label>
                                                        <select class="form-control" id="propriedade" name="propriedade[]" onchange="relation()">
                                                            <option value="">Selecione...</option>
                                                                
                                                                <?php
                                                                   while($dadosFazenda = $sqlBuscaFazendas->fetch(PDO::FETCH_ASSOC)){
                                                                ?>
                                                                    <option value="<?php echo $dadosFazenda['id']?>"><?php echo $dadosFazenda['nome']?></option>
                                                                <?php
                                                                   }
                                                                ?>
                                                        </select>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-group">
                                                        <label for="tipo">Tipo de Cadastro</label>
                                                        <select class="form-control" id="tipo" name="tipo[]" onchange="relation()">
                                                            <option value="">Selecione...</option>
                                                                
                                                                <?php
                                                                    while($dadosTipos2 = $sqlBuscaTipos2->fetch(PDO::FETCH_ASSOC)){
                                                                ?>
                                                                    <option value="<?php echo $dadosTipos2['id']?>"><?php echo $dadosTipos2['tipo']?></option>
                                                                <?php
                                                                    }
                                                                ?>
                                                        </select>
                                                    </div>
                                                </td>
                                    
                                                <td>
                                                    <div class="opcoes-inf-maquinas">
                                                        <div class="opcoes-inf-maquinas2">
                                                            <i class="fas fa-plus text-primary" style="cursor:pointer; margin-right:20px;" onclick="addLinhaRelacao()"></i>
                                                                <!-- <i class="fas fa-minus-circle text-danger" style="cursor:pointer;" onclick="addLinhaMaquinas()"></i> -->
                                                        </div>                                             
                                                    </div>
                                                </td>
                                                
                                            </tr>
                                        </table>
                                        
                                    </div>
                                </div>  
                            </div>      
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-primary" id="btn-enviar">Salvar</button>
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
        function relation(){
            var box = document.getElementById('tipo');
            var relacao = document.getElementById('relacaoUser');
            group = box.options[box.selectedIndex].getAttribute("tipo");
            console.log(group);
            if(group == null){
                relacao.setAttribute('style', 'display:block');
            }else{
                relacao.setAttribute('style', 'display:none');
            }
        }

        function addLinhaRelacao() {
            var table = document.getElementById("relacao");
            var qnt = table.rows.length;
            var cell = table.insertRow(qnt);
            cell.innerHTML += '<tr><td><div class="form-group"><label for="propriedade">Propriedade*</label><select class="form-control" id="propriedade" name="propriedade[]" required onchange="relation()"><option value="">Selecione...</option><?php while($dadosFazenda2 = $sqlBuscaFazendas2->fetch(PDO::FETCH_ASSOC)){?><option value="<?php echo $dadosFazenda2['id']?>"><?php echo $dadosFazenda2['nome']?></option><?php } ?></select> </div> </td> <td> <div class="form-group"> <label for="tipo">Tipo de Cadastro*</label> <select class="form-control" id="tipo" name="tipo[]" required onchange="relation()"> <option value="">Selecione...</option> <?php while($dadosTipos = $sqlBuscaTipos->fetch(PDO::FETCH_ASSOC)){ ?> <option value="<?php echo $dadosTipos['id']?>"><?php echo $dadosTipos['tipo']?></option> <?php } ?> </select> </div> </td> </tr>';
            
        }
        function buscaEmail(){
        var email = document.getElementById("email").value;
            var formData = new FormData();
            formData.append('email', email);
           
            //chamada assíncrona (Ajax)
            var xhr = new XMLHttpRequest();

            //a página php que processará os dados
            xhr.open('POST', 'backend/buscaEmail.php', true);

            //se nao der pra abrir mostra um erro
            xhr.onload = function () {
                if (xhr.status != 200) {
                    alert('An error occurred!');
                }
                
            };
            
            //envia
            xhr.send(formData);
            
            xhr.onreadystatechange = function () {
			    if (xhr.readyState < 4)                             // está à espera de resposta
			        console.log('A carregar...');
			    else if (xhr.readyState === 4) {                    // 4 = A resposta do servidor está carregada
			        if (xhr.status == 200 && xhr.status < 300)  // http status entre 200 e 299 quer dizer sucesso
			        	var resposta = xhr.responseText;
                        console.log(resposta);
			        	if(resposta == "yes"){
			        	    document.getElementById("btn-enviar").removeAttribute('disabled');
			        	    document.getElementById("emailHelp").setAttribute('style', 'display:none');
			        	}else{
			        	    document.getElementById("btn-enviar").setAttribute('disabled', '');
			        	    document.getElementById("emailHelp").setAttribute('style', 'display:block');
			        	}
			                       // ou usa o xhr.responseText de outra maneira
			    }
			}
        }
    </script>

</body>

</html>