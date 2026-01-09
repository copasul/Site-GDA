<?php
    include __DIR__ . '/backend/conexao.php';
    date_default_timezone_set('America/Sao_paulo');
    $data = date("Y-m-d H:i:s");


    if(empty($_GET['token'])){
        header('Location: index.php');
    }else{
        $token = $_GET['token']; 
    }
    
    if(empty($_GET['action'])){
        $sqlBuscaToken = $conn->query("SELECT token_inicial FROM Usuarios WHERE token_inicial = '$token' LIMIT 1");
        $respostaToken = $sqlBuscaToken->fetch(PDO::FETCH_ASSOC);
    
        if(empty($respostaToken['token_inicial'])){
            header('Location: index.php');
        }
    }else{
        $sqlBuscaToken = $conn->query("SELECT * FROM esqueceu_senha WHERE token = '$token' AND data_validade > date('$data') LIMIT 1");
        $respostaToken = $sqlBuscaToken->fetch(PDO::FETCH_ASSOC);
    
        if(empty($respostaToken['token'])){
            header('Location: index.php');
        }
       
    }

?>
<!DOCTYPE html>
<html lang="pt-br">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title><?php echo $titulo?> - Novo Cadastro</title>

    <!-- Custom fonts for this template-->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="css/sb-admin-2.min.css" rel="stylesheet">

</head>

<body class="bg-gradient-verde-copasul">

    <div class="container">

        <!-- Outer Row -->
        <div class="row justify-content-center">

            <div class="col-xl-10 col-lg-12 col-md-9">

                <div class="card o-hidden border-0 shadow-lg my-5">
                    <div class="card-body p-0">
                        <!-- Nested Row within Card Body -->
                        <div class="row">
                            <div class="col-lg-6 d-none d-lg-block bg-login-image"></div>
                            <div class="col-lg-6">
                                <div class="p-5">
                                    <div class="text-center">
                                        <img src="img/logo.png" alt="" width="70%">
                                    </div>
                                    <br>
                                    <div class="text-center">
                                        <h1 class="h4 text-gray-900 mb-4">Nova senha</h1>
                                    </div>
                                    <form class="user" action="backend/salvarSenha.php?token=<?php echo $token;?>" method="POST">
                                        <div class="form-group">
                                            <input type="password" class="form-control form-control-user"
                                                id="senha" name="senha" aria-describedby="emailHelp"
                                                placeholder="Senha" onkeyup="validarSenha()" required>
                                        </div>
                                        <div class="form-group">
                                            <input type="password" class="form-control form-control-user"
                                                id="senha2" name="senha2" placeholder="Confirmar Senha" onkeyup="blockbtn(2)" required >
                                                <small id="errorSenha" class="form-text text-danger" >Senhas diferentes</small>

                                        </div>
                                        <button class="btn btn-primary btn-user btn-block" disabled id="btn-enviar">
                                            Enviar
                                        </button>
                                       
                                    </form>
                                    <hr>
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
    <script>

        function blockbtn(){
            if(document.getElementById('senha').value == document.getElementById('senha2').value){
                document.getElementById('errorSenha').removeAttribute('style');
                var response = validarSenha();
                if(response == false){
                    document.getElementById('btn-enviar').setAttribute('disabled','');
                }else if (response == true){
                    document.getElementById('btn-enviar').removeAttribute('disabled');
                }
            }else{
                validarSenha();
                document.getElementById('errorSenha').setAttribute('style', 'display:block !important');
                document.getElementById('btn-enviar').setAttribute('disabled','');
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
</body>

</html>