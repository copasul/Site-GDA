<?php
session_start();

if (!isset($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(32));
}

$action = $_GET['action'] ?? 'login';
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title><?= htmlspecialchars($titulo ?? 'Sistema', ENT_QUOTES, 'UTF-8') ?> - Login</title>

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
                                        <h1 class="h4 text-gray-900 mb-4">
                                            <?php 
                                                if($action == "login"){
                                                    echo "Login";
                                                }elseif($action == "esqueceu-a-senha"){
                                                    echo "Esqueceu a senha";
                                                }
                                            ?>
                                        </h1>
                                    </div>
                                    <?php 
                                        if($action == "login"){
                                    ?>
                                    <form class="user" method="POST" action="backend/login.php">
                                        <input type="hidden" name="csrf" value="<?= $_SESSION['csrf'] ?>">

                                        <div class="form-group">
                                            <input type="email" name="email" class="form-control form-control-user"
                                                id="exampleInputEmail" aria-describedby="emailHelp"
                                                placeholder="E-mail">
                                        </div>
                                        <div class="form-group">
                                            <input type="password" name="senha" class="form-control form-control-user"
                                                id="exampleInputPassword" placeholder="Senha">
                                                <?php
                                                    if (($_GET['status'] ?? '') === 'error') {
                                                ?>
                                                <small class="form-text text-danger" >E-mail ou senha incorretos</small>
                                                <?php
                                                    }
                                                ?>
                                        </div>
                                        
                                        <button class="btn btn-primary btn-user btn-block">
                                            Login
                                        </button>
                                       
                                    </form>
                                    <hr>
                                    <div class="text-center">
                                        <a class="small" href="login.php?action=esqueceu-a-senha">Esqueceu a senha?</a>
                                    </div>
                                    <?php
                                    }elseif($action == "esqueceu-a-senha"){
                                    ?>
                                        <form class="user" method="POST" action="backend/esqueceu-a-senha.php">
                                            <input type="hidden" name="csrf" value="<?= $_SESSION['csrf'] ?>">

                                            <div class="form-group">
                                                <input type="email" name="email" class="form-control form-control-user"
                                                    id="exampleInputEmail" aria-describedby="emailHelp"
                                                    placeholder="E-mail">
                                            </div>
                                            
                                           
                                            <button class="btn btn-primary btn-user btn-block">
                                                Enviar
                                            </button>
                                           
                                        </form>
                                         <br>
                                            <br>
                                            <br>
                                        <hr>
                                    <div class="text-center">
                                        <a class="small" href="login.php">Voltar</a>
                                    </div>
                                    <?php
                                    }elseif($action == "enviado"){
                                    ?>
                                   
                                    <form class="user" method="POST" action="backend/esqueceu-a-senha.php">
                                            <div class="form-group">
                                                 <small class="form-text text-danger" >E-mail enviado! Verifique sua caixa de email, spam ou lixeira</small>
                                                <input type="email" name="email" class="form-control form-control-user"
                                                    id="exampleInputEmail" aria-describedby="emailHelp"
                                                    placeholder="E-mail">
                                            </div>
                                            
                                           
                                            <button class="btn btn-primary btn-user btn-block">
                                                Enviar
                                            </button>
                                           
                                        </form>
                                         <br>
                                            <br>
                                            <br>
                                        <hr>
                                    <div class="text-center">
                                        <a class="small" href="login.php">Voltar</a>
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

        </div>

    </div>

    <!-- Bootstrap core JavaScript-->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="js/sb-admin-2.min.js"></script>

</body>

</html>