<?php 
    include __DIR__ . '/backend/conexao.php';
    include __DIR__ . '/backend/verificaLog.php';

?>
<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <title><?php echo $titulo?> - Perfil</title>

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
                        <h1 class="h3 mb-0 text-gray-800">Meu perfil</h1>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Meus dados</h6>
                                </div>
                                <div class="card-body">
                                   
                                        <div class="form-group">
                                            <label for="nomePropriedade">Nome</label>
                                            <input type="text" class="form-control" id="nomePropriedade"  required value="<?php echo $User['nome']?>">
                                            </div>
                                        <div class="form-row ">
                                            <div class="form-group col-md-6">
                                                <label for="inputAddress">E-mail</label>
                                                <input type="email" class="form-control" id="inputAddress" placeholder=""  required value="<?php echo $User['email']?>">
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label for="inputAddress">Tel</label>
                                                <input type="tel" class="form-control" id="inputAddress" placeholder=""  required value="<?php echo $User['telefone']?>">
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
                                    <h6 class="m-0 font-weight-bold text-primary">Alterar senha</h6>
                                </div>
                                <div class="card-body">
                                    <form class="form-cadas" action="backend/salvarSenha.php" method="POST" id="form-novo-usuario">
                                        <div class="form-row ">
                                            <div class="form-group col-md-6">
                                                <label for="inputAddress">Senha</label>
                                                <input type="password" class="form-control" id="senha" placeholder="**********" name="senha" onkeyup="validarSenha()" required>
                                            </div>
                                        
                                            <div class="form-group col-md-6">
                                                <label for="inputAddress">Confirmar Senha</label>
                                                <input type="password" class="form-control" id="senha2" placeholder="**********" name="senha2"  onkeyup="blockbtn(2)" required >
                                                <small id="errorSenha" class="form-text text-danger" >Senhas diferentes</small>
                                            </div>
                                        </div>
                                        <div class="form-row">
                                            <div  class="btn-cadastrar">
                                                <button type="submit" class="btn btn-primary" form="form-novo-usuario" disabled id="btn-enviar">Editar</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                
                                    <div class="text-left">
                                        <ul>
                                            <li id="tamanho">entre 8 a 12 caracteres</li>
                                            <li id="maiuscula">Letras Maiúsculas</li>
                                            <li id="minuscula">Letras Minúsculas</li>
                                            <li id="numero">Números</li>
                                        </ul>
                                    </div>
                            </div>
                        </div>
                    </div>
                                        
                                        
                                        
                                       
                                
      

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

        function blockbtn(){
            if(document.getElementById('senha').value == document.getElementById('senha2').value){
                document.getElementById('errorSenha').removeAttribute('style');
                var response = validarSenha();
                if(response == false){
                    document.getElementById('btn-enviar').setAttribute('disabled', '');
                }else if (response == true){
                    document.getElementById('btn-enviar').removeAttribute('disabled');
                }
            }else{
                validarSenha();
                document.getElementById('errorSenha').setAttribute('style', 'display:block !important');
                document.getElementById('btn-enviar').setAttribute('disabled', '');
            }
        }


        function validarSenha(){
            p = document.getElementById('senha').value;

            var retorno = false;
            var letrasMaiusculas = /[A-Z]/;
            var letrasMinusculas = /[a-z]/; 
            var numeros = /[0-9]/;
            if(p.length < 8 | p.length > 12){
                document.getElementById('tamanho').setAttribute('style', 'color:red');  
               return false;
            }else{
                document.getElementById('tamanho').removeAttribute('style');
            }
        
            var auxMaiuscula = 0;
            var auxMinuscula = 0;
            var auxNumero = 0;
            for(var i=0; i<p.length; i++){
                if(letrasMaiusculas.test(p[i])){
                    auxMaiuscula++;
                }else if(letrasMinusculas.test(p[i])){
                    auxMinuscula++;
                }else if(numeros.test(p[i])){
                    auxNumero++;
                }
                if (auxMaiuscula > 0){
                    document.getElementById('maiuscula').removeAttribute('style');
                    if (auxMinuscula > 0){
                        document.getElementById('minuscula').removeAttribute('style');
                        if (auxNumero > 0){
                            document.getElementById('numero').removeAttribute('style');
                            return true;
                        }else{
                            document.getElementById('numero').setAttribute('style', 'color:red'); 
                        }
                    }else{
                        document.getElementById('minuscula').setAttribute('style', 'color:red');   
                    }
                }else{
                    document.getElementById('maiuscula').setAttribute('style', 'color:red');   
                }
                
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