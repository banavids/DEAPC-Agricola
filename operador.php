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
    <title>Painel do Operador - FarmSmart OS</title>
    <link rel="stylesheet" href="styles/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    
    <div class="app-layout">

        <?php include 'scripts/sidebar.php'; ?>

        <div class="sidebar-overlay" id="sidebarOverlay"></div>

        <div class="main-wrapper">
            
            <header class="topbar">
                <div class="topbar-left">
                    <button class="menu-toggle" id="menuToggle">
                        <i class="fa-solid fa-bars"></i>
                    </button>
                    <h1 class="page-title">Painel do Operador</h1>
                </div>
                
                <div class="topbar-right">
                    <div class="notifications">
                        <i class="fa-solid fa-bell"></i>
                        <span class="badge"></span>
                    </div>
                    <div class="user-profile">
                        <div class="avatar"><i class="fa-solid fa-user"></i></div>
                        <span class="user-name">João Operário</span> 
                    </div>
                </div>
            </header>

            <main class="content-body">
                <div class="dashboard-content dashboard-operador">
                    
                    <div class="welcome-banner">
                        <h2>Bom dia, João!</h2>
                        <p>Tens 2 tarefas pendentes para hoje.</p>
                        
                        <div class="production-badge">
                            <span class="badge-label">PRODUÇÃO ATUAL</span>
                            <span class="badge-value">Estufa 2 - Alfaces</span>
                        </div>
                    </div>

                    <div class="light-card">
                        <h3 class="light-card-title"><i class="fa-regular fa-square-check"></i> As Minhas Tarefas</h3>
                        
                        <div class="task-list">
                            <div class="task-item">
                                <div class="task-icon"><i class="fa-regular fa-clock"></i></div>
                                <div class="task-info">
                                    <h4>Colheita de Alfaces - Estufa 2</h4>
                                    <span class="task-time">Hoje, 08:00</span>
                                </div>
                                <button class="btn-complete">Marcar como Concluída</button>
                            </div>

                            <div class="task-item">
                                <div class="task-icon"><i class="fa-regular fa-clock"></i></div>
                                <div class="task-info">
                                    <h4>Verificar sistema de rega gotejamento</h4>
                                    <span class="task-time">Hoje, 14:00</span>
                                </div>
                                <button class="btn-complete">Marcar como Concluída</button>
                            </div>

                            <div class="task-item task-done">
                                <div class="task-icon text-green"><i class="fa-regular fa-circle-check"></i></div>
                                <div class="task-info">
                                    <h4 style="text-decoration: line-through; color: #94a3b8;">Aplicação de fertilizante</h4>
                                    <span class="task-time">Ontem, 16:30</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="light-card">
                        <h3 class="light-card-title"><i class="fa-regular fa-comment"></i> Notas Recentes</h3>
                        
                        <div class="note-box">
                            <span class="note-author">Aviso do Gestor (Maria) - Hoje, 07:30</span>
                            <p class="note-text">Cuidado com a válvula V4 na Estufa 2, está a gotejar mais do que o esperado. Reportar se piorar.</p>
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