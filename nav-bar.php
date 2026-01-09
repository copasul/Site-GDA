<ul class="navbar-nav bg-gradient-verde-copasul sidebar sidebar-dark accordion" id="accordionSidebar">

            <!-- Sidebar - Brand -->
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="<?php echo $urlBase?>">

                <div class="sidebar-brand-text mx-3">GDA Copasul Teste</div>
            </a>

            <!-- Divider -->
            <hr class="sidebar-divider my-0">

            <!-- Nav Item - Dashboard -->
            <li class="nav-item active">
                <a class="nav-link" href="<?php echo $urlBase?>">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Dashboard</span></a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider">

            <!-- Heading -->
            <div class="sidebar-heading">
                Gestão
            </div>

            <?php 
                if(!empty($User['tipo'])){
            ?>
            <!-- Nav Item - Utilities Collapse Menu -->
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseUtilities"
                    aria-expanded="true" aria-controls="collapseUtilities">
                    <i class="fas fa-fw fa-wrench"></i>
                    <span>Gerenciamento</span>
                </a>
                <div id="collapseUtilities" class="collapse" aria-labelledby="headingUtilities"
                    data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <h6 class="collapse-header">Gerenciamento</h6>
                        <a class="collapse-item" href="<?php echo $urlBase?>propriedades.php">Propriedades</a>
                        <a class="collapse-item" href="<?php echo $urlBase?>safras.php">Safras</a>
                        <a class="collapse-item" href="<?php echo $urlBase?>usuarios.php">Usuários</a>
                        <!-- <a class="collapse-item" href="http://172.30.100.75/LeanAgro/configuracoes-gerais">Configurações Gerais</a> -->
                    </div>
                </div>
            </li>
            <?php 
                }
           ?>
            <li class="nav-item">
                <a class="nav-link" href="<?php echo $urlBase?>perda-na-colheita/">
                    <i class="fas fa-fw fa-chart-area"></i>
                    <span>Perda na colheita</span></a>
            </li>
            <!-- Divider -->
            <!-- <hr class="sidebar-divider"> -->

            <!-- Heading -->
            <!-- <div class="sidebar-heading">
                Recursos
            </div> -->

           

            <!-- Nav Item - Pages Collapse Menu -->
            <!-- <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapsePages"
                    aria-expanded="true" aria-controls="collapsePages">
                    <i class="fas fa-fw fa-folder"></i>
                    <span>Relatórios</span>
                </a>
                <div id="collapsePages" class="collapse" aria-labelledby="headingPages" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <h6 class="collapse-header">Perda na colheita</h6>
                        <a class="collapse-item" href="login.html">Perda por Propriedade</a>
                        <a class="collapse-item" href="register.html">Perda por Talhão</a>
                        <a class="collapse-item" href="forgot-password.html">Perda por Máquina</a>
                        <a class="collapse-item" href="forgot-password.html">Registros</a>
                        <div class="collapse-divider"></div>
                        <h6 class="collapse-header">Other Pages:</h6>
                        <a class="collapse-item" href="404.html">404 Page</a>
                        <a class="collapse-item" href="blank.html">Blank Page</a>
                    </div>
                </div>
            </li> -->

            <!-- Divider -->
            <hr class="sidebar-divider d-none d-md-block">

            <!-- Sidebar Toggler (Sidebar) -->
            <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
            </div>

        </ul>