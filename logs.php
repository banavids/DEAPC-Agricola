<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login/login-farmsmart.html");
    exit;
}

if (!isset($_SESSION['user_id'])) {
    header("Location: login/login-farmsmart.html");
    exit;
}


if ($_SESSION['user_group'] == 3) {
    header("Location: operador.php");
    exit;
}
$termoPesquisa = $_GET['search'] ?? '';
?>
<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Auditoria - FarmSmart OS</title>
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
                    <h1 class="page-title">Auditoria e Logs</h1>
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
                        <h2 class="page-title">Logs e Auditoria</h2>
                    </div>

                    <div class="light-card full-width">
                        
                        <form method="GET" action="" class="toolbar" style="margin-bottom: 20px; display: flex; gap: 10px;">
                            <div class="search-box" style="flex-grow: 1;">
                                <i class="fa-solid fa-magnifying-glass"></i>
                                <input type="text" name="search" placeholder="Procurar por utilizador, ação ou detalhes..." value="<?php echo htmlspecialchars($termoPesquisa); ?>">
                            </div>
                            <button type="submit" class="btn" style="background: #3b82f6; color: white; padding: 10px 20px; border:none; border-radius: 5px; cursor: pointer;">
                                Procurar
                            </button>
                            <?php if (!empty($termoPesquisa)): ?>
                                <a href="<?php echo htmlspecialchars(basename($_SERVER['PHP_SELF'])); ?>" class="btn" style="background: #ef4444; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;">Limpar</a>
                            <?php endif; ?>
                        </form>

                        <div class="table-responsive">
                            <table class="modern-table">
                                <thead>
                                    <tr>
                                        <th>Data / Hora</th>
                                        <th>Utilizador</th> <th>Ação Realizada</th>
                                        <th>Detalhes</th> </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    try {
                                        $db_logs = new SQLite3(__DIR__ . '/bd/FarmOS.db');
                                        
                                        // Prepara a query base
                                        $query = "
                                            SELECT 
                                                L.SLG_data_hora, 
                                                L.SLG_acao, 
                                                L.SLG_detalhes, 
                                                U.USR_nome 
                                            FROM tblSystemLog L
                                            LEFT JOIN tblUser U ON L.SLG_user_id = U.USR_id
                                        ";
                                        
                                        if (!empty($termoPesquisa)) {
                                            $query .= " WHERE L.SLG_acao LIKE :search OR L.SLG_detalhes LIKE :search OR U.USR_nome LIKE :search ";
                                        }
                                        
                                        $query .= " ORDER BY L.SLG_data_hora DESC LIMIT 100";
                                        
                                        $stmt = $db_logs->prepare($query);
                                        
                                        if (!empty($termoPesquisa)) {
                                            $stmt->bindValue(':search', '%' . $termoPesquisa . '%', SQLITE3_TEXT);
                                        }
                                        
                                        $resultados = $stmt->execute();
                                        $temResultados = false;
                                        
                                        while ($row = $resultados->fetchArray(SQLITE3_ASSOC)) {
                                            $temResultados = true;
                                            $dataFormatada = date('d M Y H:i:s', strtotime($row['SLG_data_hora']));
                                            $nomeUtilizador = !empty($row['USR_nome']) ? htmlspecialchars($row['USR_nome']) : 'Sistema Automático';
                                            $acao = htmlspecialchars($row['SLG_acao']);
                                            $detalhes = htmlspecialchars($row['SLG_detalhes']);
                                            
                                            echo "<tr>";
                                            echo "<td><span class='td-sub text-dark'>{$dataFormatada}</span></td>";
                                            echo "<td><span class='td-title text-sm'>{$nomeUtilizador}</span></td>";
                                            echo "<td><span class='td-sub text-dark'>{$acao}</span></td>";
                                            echo "<td><span class='td-sub'>{$detalhes}</span></td>";
                                            echo "</tr>";
                                        }
                                        
                                        if (!$temResultados) {
                                            echo "<tr><td colspan='4' style='text-align: center; padding: 20px;'>Nenhum log encontrado.</td></tr>";
                                        }
                                        
                                        $db_logs->close();
                                        
                                    } catch (Exception $e) {
                                        echo "<tr><td colspan='4' style='color:red;'>Erro ao carregar logs: " . $e->getMessage() . "</td></tr>";
                                    }
                                    ?>
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