<?php
session_start();
// Proteção de página: Se não tiver logado, volta para o login
if (!isset($_SESSION['user_id'])) {
    header("Location: login/login-farmsmart.html");
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Utilizadores - FarmSmart OS</title>
    <link rel="stylesheet" href="styles/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    
    <div class="app-layout">

        <?php 
            require_once 'scripts/sidebar.php'; 
            require_once 'scripts/database.php';
        ?>

        <div class="sidebar-overlay" id="sidebarOverlay"></div>

        <div class="main-wrapper">
            
            <header class="topbar">
                <div class="topbar-left">
                    <button class="menu-toggle" id="menuToggle">
                        <i class="fa-solid fa-bars"></i>
                    </button>
                    <h1 class="page-title">Controlo de Acessos</h1>
                </div>
                
                <div class="topbar-right">
                    <div class="notifications">
                        <i class="fa-solid fa-bell"></i>
                        <span class="badge"></span>
                    </div>
                    <div class="user-profile">
                        <div class="avatar"><i class="fa-solid fa-user"></i></div>
                        <span class="user-name">Gestor</span>
                    </div>
                </div>
            </header>

            <main class="content-body">
                <div class="dashboard-content">
                    
                    <div class="page-header" style="margin-bottom: 20px;">
                        <h2 class="page-title">Utilizadores do Sistema</h2>
                        <button class="btn btn-green"><i class="fa-solid fa-user-plus"></i> Criar Utilizador</button>
                    </div>

                    <div class="light-card full-width">
                        <div class="table-responsive">
                            <table class="modern-table align-middle">
                                <thead>
                                    <tr>
                                        <th>Nome</th>
                                        <th>Email</th>
                                        <th>Cargo (Role)</th>
                                        <th>Estado</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            <div class="user-cell">
                                                <div class="avatar-circle avatar-purple">A</div>
                                                <span class="td-title">Admin Silva</span>
                                            </div>
                                        </td>
                                        <td><span class="td-sub"><i class="fa-regular fa-envelope"></i> admin@farmsmart.pt</span></td>
                                        <td><span class="role-tag text-purple"><i class="fa-regular fa-star"></i> Administrador</span></td>
                                        <td><span class="pill-badge bg-green-light text-green">Ativo</span></td>
                                        <td class="text-right"><button class="btn-icon"><i class="fa-solid fa-ellipsis"></i></button></td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="user-cell">
                                                <div class="avatar-circle avatar-blue">M</div>
                                                <span class="td-title">Maria Costa</span>
                                            </div>
                                        </td>
                                        <td><span class="td-sub"><i class="fa-regular fa-envelope"></i> maria@farmsmart.pt</span></td>
                                        <td><span class="role-tag text-blue"><i class="fa-solid fa-shield-halved"></i> Gestor</span></td>
                                        <td><span class="pill-badge bg-green-light text-green">Ativo</span></td>
                                        <td class="text-right"><button class="btn-icon"><i class="fa-solid fa-ellipsis"></i></button></td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="user-cell">
                                                <div class="avatar-circle avatar-grey">J</div>
                                                <span class="td-title">João Operário</span>
                                            </div>
                                        </td>
                                        <td><span class="td-sub"><i class="fa-regular fa-envelope"></i> joao@farmsmart.pt</span></td>
                                        <td><span class="role-tag text-grey"><i class="fa-solid fa-wrench"></i> Operário</span></td>
                                        <td><span class="pill-badge bg-green-light text-green">Ativo</span></td>
                                        <td class="text-right"><button class="btn-icon"><i class="fa-solid fa-ellipsis"></i></button></td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="user-cell">
                                                <div class="avatar-circle avatar-grey">C</div>
                                                <span class="td-title">Carlos Técnico</span>
                                            </div>
                                        </td>
                                        <td><span class="td-sub"><i class="fa-regular fa-envelope"></i> carlos@farmsmart.pt</span></td>
                                        <td><span class="role-tag text-grey"><i class="fa-solid fa-wrench"></i> Operário</span></td>
                                        <td><span class="pill-badge bg-red-light text-red">Suspenso</span></td>
                                        <td class="text-right"><button class="btn-icon"><i class="fa-solid fa-ellipsis"></i></button></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
                </main>
        </div>
    </div>

    <script>
        document.getElementById('menuToggle').addEventListener('click', function() {
            document.getElementById('sidebar').classList.add('open');
            document.getElementById('sidebarOverlay').classList.add('active');
        });

        document.getElementById('sidebarOverlay').addEventListener('click', function() {
            document.getElementById('sidebar').classList.remove('open');
            this.classList.remove('active');
        });
    </script>
</body>
</html>