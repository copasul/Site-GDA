<?php
    include __DIR__ . '/backend/conexao.php';
    include __DIR__ . '/backend/verificaLog.php';

    if(empty($_GET['id'])){
        
    }else{
        $id = base64_decode($_GET['id']);
    }

    $sqlBuscaUsuario = $conn->query("SELECT * FROM usuarios WHERE id = '$id'");
    $usuario = $sqlBuscaUsuario->fetch(PDO::FETCH_ASSOC);


    $sqlBusca = $conn->query("SELECT * FROM permissoes WHERE grupo = 'Copasul' ");


    $sqlBuscaFazendas = $conn->query("SELECT * FROM propriedades");
    $sqlBuscaFazendas2 = $conn->query("SELECT * FROM propriedades");


    $sqlBuscaTipos = $conn->query("SELECT * FROM permissoes WHERE grupo = 'Externo'");
    $sqlBuscaTipos2 = $conn->query("SELECT * FROM permissoes WHERE grupo = 'Externo'");
    
    
    $sqlBuscaAtivado = $conn->query("SELECT * FROM usuarios WHERE id = '$id' AND token_inicial is NOT NULL");
    $ativacao = $sqlBuscaAtivado->fetch(PDO::FETCH_ASSOC);

   

    $sqlBuscaRelacao = $conn->query("SELECT * FROM relacao_usuario_propriedade WHERE id_usuario = $id AND status = 1");
    // echo "SELECT * FROM relacao_usuario_propriedade WHERE id_usuario = $id AND status = 1";

    $sqlBuscaRelacaoCount = $conn->query("SELECT Count(*) AS NUM FROM relacao_usuario_propriedade WHERE id_usuario = $id  AND status = 1");
    $usuarioCount = $sqlBuscaRelacaoCount->fetch(PDO::FETCH_ASSOC);
    
?>

<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <title><?php echo $titulo?> - Detalhes do Usuário</title>

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
                        <h1 class="h3 mb-0 text-gray-800">Detalhes do Usuário </h1>
                        <?php if(!empty($ativacao)){?>
                    
                        <div class="d-sm-flex align-items-center justify-content-between mb-4">
                             <!--<a href="add-usuario.php" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i-->
                             <!--   class="fas fa-download fa-sm text-white-50"></i> Reenviar e-mail de ativação</a>-->
                            
                            <button onclick="copiarTexto()" id="btn-copy" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm" style="margin-left:10px;" aria-describedby="info"><i
                                class="fas fa-copy fa-sm text-white-50"></i> Copiar link de ativação</button>
                        </div>
                       
                        <?php }?>
                    </div>
                    <form method="POST" action="backend/editarUsuario.php?id=<?php echo base64_encode($id)?>" onchange="buscaEmail()">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card shadow mb-4">
                                    <div class="card-header py-3">
                                        <h6 class="m-0 font-weight-bold text-primary">Dados do Usuário</h6>
                                    </div>
                                    <div class="card-body">  
                                            <div class="form-group">
                                                <label for="nome">Nome*</label>
                                                <input type="text" class="form-control" id="nome" name="nome" required value="<?php echo $usuario['nome']?>">
                                            </div>
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="email">E-mail*</label>  
                                                        <input type="email" class="form-control" id="email" name="email" required value="<?php echo $usuario['email']?>">
                                                        <small id="emailHelp" class="form-text text-danger" style="display: none;">E-mail já utilizado</small>

                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="telefone">Telefone *</label>
                                                        <input type="tel" class="form-control" id="telefone" name="telefone" required value="<?php echo $usuario['telefone']?>">
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
                                                                        <option value="<?php echo $dados['id']?>" tipo="<?php echo $dados['grupo']?>" <?php if($dados['id'] == $usuario['tipo']){echo "selected";}?>><?php echo $dados['tipo']?></option>
                                                                    <?php
                                                                        }
                                                                    ?>
                                                                </optgroup>
                                                             <option value="null" <?php if(NULL == $usuario['tipo']){echo "selected";}?>>Externo</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                    </div>
                                </div>    
                            </div>    
                        </div>
                        <div class="row" id="relacaoUser" <?php if($usuario['tipo'] <> NULL ){echo 'style="display: none;"';}?> >
                            <div class="col-md-12">
                                <div class="card shadow mb-4">
                                    <div class="card-header py-3">
                                        <h6 class="m-0 font-weight-bold text-primary">Relação Usuário X Propriedade</h6>
                                    </div>
                                    <div class="card-body" id="lista-talhao">
                                        <table id="relacao">
                                            <?php
                                            $n = 0;
                                                while($relacao = $sqlBuscaRelacao->fetch(PDO::FETCH_ASSOC)){
                                                    
                                            ?>
                                            <tr>
                                                <input type="hidden" name="idRelacao[]" value="<?php echo base64_encode($relacao['id']);?>">
                                                <td> 
                                                    <div class="form-group">
                                                        <label for="propriedade">Propriedade</label>
                                                        <select class="form-control" id="propriedade" name="propriedade[]" onchange="relation()">
                                                            <option value="">Selecione...</option>
                                                                
                                                                <?php
                                                                 $sqlBuscaFazendas3 = $conn->query("SELECT * FROM propriedades");
                                                                   while($dadosFazenda = $sqlBuscaFazendas3->fetch(PDO::FETCH_ASSOC)){
                                                                ?>
                                                                    <option value="<?php echo $dadosFazenda['id']?>" <?php if($dadosFazenda['id'] == $relacao['id_propriedade']){echo "selected";}?> ><?php echo $dadosFazenda['nome']?></option>
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
                                                                $sqlBuscaTipos3 = $conn->query("SELECT * FROM permissoes WHERE grupo = 'Externo'");
                                                                    while($dadosTipos2 = $sqlBuscaTipos3->fetch(PDO::FETCH_ASSOC)){
                                                                ?>
                                                                    <option value="<?php echo $dadosTipos2['id']?>" <?php if($dadosTipos2['id'] == $relacao['id_tipo']){echo "selected";}?>><?php echo $dadosTipos2['tipo']?></option>
                                                                <?php
                                                                    }
                                                                ?>
                                                        </select>
                                                    </div>
                                                </td>
                                                <?php
                                                    if($n == 0){
                                                ?>  
                                                <td>
                                                    <div class="form-group">
                                                        <i class="fas fa-plus text-primary" style="cursor:pointer; margin-right:20px;" onclick="addLinhaRelacao()"></i>
                                                                <!-- <i class="fas fa-minus-circle text-danger" style="cursor:pointer;" onclick="addLinhaMaquinas()"></i> -->
                                                    </div>                                             
                                                </td>
                                                <?php
                                                    }
                                                ?>
                                                <td>
                                                    <div class="form-group">
                                                        <i class="fas fa-trash text-danger" style="cursor:pointer; " onclick="deleteRelacao('<?php echo base64_encode($relacao['id'])?>', '<?php echo md5($relacao['id'].'bazinga123');?>')"></i>
                                                    </div>
                                                </td>  
                                                
                                            </tr>
                                            <?php
                                               $n += 1;     
                                            }
                                                if($usuarioCount['NUM'] == 0){
                                            ?>
                                            <tr>
                                            <input type="hidden" name="idRelacao[]" value="">
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
                                <button type="submit" class="btn btn-primary" id="btn-enviar">Salvar</button>
                                <a href="backend/deleteUser.php?id=<?php echo base64_encode($id) ?>" class="btn btn-danger" id="btn-enviar">Excluir</a>
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
    <div id="span-copied">
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
            cell.innerHTML += '<tr> <input type="hidden" name="idRelacao[]" value=""><td><div class="form-group"><label for="propriedade">Propriedade*</label><select class="form-control" id="propriedade" name="propriedade[]" required onchange="relation()"><option value="">Selecione...</option><?php while($dadosFazenda2 = $sqlBuscaFazendas2->fetch(PDO::FETCH_ASSOC)){?><option value="<?php echo $dadosFazenda2['id']?>"><?php echo $dadosFazenda2['nome']?></option><?php } ?></select> </div> </td> <td> <div class="form-group"> <label for="tipo">Tipo de Cadastro*</label> <select class="form-control" id="tipo" name="tipo[]" required onchange="relation()"> <option value="">Selecione...</option> <?php while($dadosTipos = $sqlBuscaTipos->fetch(PDO::FETCH_ASSOC)){ ?> <option value="<?php echo $dadosTipos['id']?>"><?php echo $dadosTipos['tipo']?></option> <?php } ?> </select> </div> </td> </tr>';
            
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
                            if(email != "<?php echo $usuario['email'];?>"){
                                document.getElementById("btn-enviar").setAttribute('disabled', '');
                                document.getElementById("emailHelp").setAttribute('style', 'display:block');
                            }
			        	}
			                       // ou usa o xhr.responseText de outra maneira
			    }
			}
        }

        function deleteRelacao(id, token){
            var resultado = confirm("Deseja excluir essa relacao?");
            if (resultado == true) {
                window.location.href = "backend/deleteRelacao.php?id="+id+"&token="+token+"&usuario=<?php echo $_GET['id']; ?>";
            }
        }
    </script>
    <script>
        function copiarTexto(){
            //O texto que será copiado
            const texto = "<?php echo $urlBase.'novo-cadastro.php?token='.$ativacao['token_inicial'];?>";
            //Cria um elemento input (pode ser um textarea)
            let inputTest = document.createElement("input");
            inputTest.value = texto;
            //Anexa o elemento ao body
            document.body.appendChild(inputTest);
            //seleciona todo o texto do elemento
            inputTest.select();
            //executa o comando copy
            //aqui é feito o ato de copiar para a area de trabalho com base na seleção
            document.execCommand('copy');
            //remove o elemento
            document.body.removeChild(inputTest);
            
            
            let elem = document.getElementById('btn-copy');
            let rect = elem.getBoundingClientRect();
            console.log("x: "+ rect.x);
            console.log("y: "+ rect.y);
            
            var x = rect.x + (elem.offsetWidth /2) - 24;
            var y = rect.y - (elem.offsetHeight) - (0.5*parseFloat(getComputedStyle(document.documentElement).fontSize));
            
            document.getElementById('span-copied').innerHTML = '<div class="tooltip fade bs-tooltip-top show" role="tooltip" id="tooltip366236" style="will-change: transform; position: absolute; transform: translate3d('+x+'px, '+y+'px, 0px); top: 0px; left: 0px;" x-placement="top"><div class="arrow" style="left: 26px;"></div><div class="tooltip-inner">Copiado!</div></div>';
        };
    </script>
  

    <!-- Vercel Speed Insights -->
    <script>
      window.si = window.si || function () { (window.siq = window.siq || []).push(arguments); };
    </script>
    <script defer src="/_vercel/speed-insights/script.js"></script>

</body>

</html>