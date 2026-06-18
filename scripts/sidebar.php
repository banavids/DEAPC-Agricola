<?php 

$grupo_id = $_SESSION['user_group'] ?? 3; 
?>
<aside class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <div class="logo-icon">
            <i class="fa-solid fa-leaf"></i>
        </div>
        <h2>FarmSmart<span class="highlight"> OS</span></h2>
    </div>
    
    <nav class="sidebar-nav">
        <div class="nav-section">Navegação</div>
        <ul>
            <?php if ($grupo_id == 1): ?>
            <li class="nav-item">
                <a href="admin.php"><i class="fa-solid fa-user-shield"></i>Dashboard Admin</a>
            </li>
            <?php endif; ?>

            <li class="nav-item">
                <a href="operador.php"><i class="fa-solid fa-tractor"></i>Dashboard Operador</a>
            </li>

            <?php if ($grupo_id == 1 || $grupo_id == 2): ?>
            <li class="nav-item">
                <a href="dashboard.php"><i class="fa-solid fa-chart-pie"></i>Dashboard Gestor</a>
            </li>
            <li class="nav-item">
                <a href="zonas.php"><i class="fa-solid fa-map-location-dot"></i>Zonas</a>
            </li>
            <li class="nav-item">
                <a href="producoes.php"><i class="fa-solid fa-seedling"></i>Produções</a>
            </li>
            <li class="nav-item">
                <a href="atuadores.php"><i class="fa-solid fa-gears"></i>Atuadores</a>
            </li>
            <?php endif; ?>
            
            <li class="nav-item">
                <a href="sensores.php"><i class="fa-solid fa-tower-broadcast"></i>Sensores</a>
            </li>
            <li class="nav-item">
                <a href="tarefas.php"><i class="fa-solid fa-clipboard-list"></i>Tarefas</a>
            </li>

            <?php if ($grupo_id == 1 || $grupo_id == 2): ?>
            <li class="nav-item">
                <a href="utilizadores.php"><i class="fa-solid fa-users"></i>Utilizadores</a>
            </li>
            <?php endif; ?>
            
            <li class="nav-item">
                <a href="relatorios.php"><i class="fa-solid fa-chart-column"></i>Relatórios</a>
            </li>

            <?php if ($grupo_id == 1 || $grupo_id == 2): ?>
            <li class="nav-item">
                <a href="logs.php"><i class="fa-solid fa-clock-rotate-left"></i>Auditoria</a>
            </li>
            <?php endif; ?>
        </ul>

        <?php if ($grupo_id == 1 || $grupo_id == 2): ?>
        <div class="nav-section">Sistema</div>
        <ul>
            <li class="nav-item">
                <a href="configuracoes.php"><i class="fa-solid fa-gear"></i>Configurações</a>
            </li>
        </ul>
        <?php endif; ?>
    </nav>
    
    <div class="sidebar-footer">
        <a href="scripts/logout.php" class="btn-logout">
            <i class="fa-solid fa-right-from-bracket"></i>Terminar Sessão
        </a>
    </div>
    
    <div class="sobre-projeto-container" style="padding: 15px; margin-top: auto; border-top: 1px solid var(--border-color, #334155);">
        <button id="btnSobreProjeto" style="width: 100%; justify-content: center; background: transparent; border: 1px solid #cbd5e1; padding: 8px 15px; border-radius: 8px; font-size: 13px; font-weight: 600; color: #cbd5e1; cursor: pointer; display: flex; align-items: center; gap: 8px; transition: 0.2s;">
            <i class="fa-solid fa-circle-info"></i>Sobre o Projeto
        </button>
    </div>
</aside>

<style>
    .modal-projeto-overlay {
        position: fixed;
        top: 0; left: 0; width: 100vw; height: 100vh;
        background-color: rgba(0, 0, 0, 0.85);
        backdrop-filter: blur(5px);
        z-index: 99999; 
        display: none; 
        align-items: center;
        justify-content: center;
    }
    .modal-projeto-overlay.active { display: flex !important; }
    .modal-projeto-card {
        background-color: #1e293b;
        border: 1px solid #334155;
        border-radius: 12px;
        padding: 30px;
        width: 90%;
        max-width: 450px;
        position: relative;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.8);
        color: #f8fafc;
        animation: deslizarCima 0.3s ease-out;
    }
    @keyframes deslizarCima {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .btn-fechar-x {
        position: absolute; top: 15px; right: 20px;
        background: none; border: none; color: #94a3b8;
        font-size: 20px; cursor: pointer; transition: 0.2s;
    }
    .btn-fechar-x:hover { color: #ef4444; }
    .grupo-lista { list-style: none; padding: 0; margin-top: 15px; }
    .grupo-lista li {
        background: #0b1120; border: 1px solid #334155;
        padding: 12px 15px; border-radius: 8px; margin-bottom: 10px;
        font-size: 14px; display: flex; gap: 15px; align-items: center;
    }
    .num-aluno {
        background: rgba(16, 185, 129, 0.15); color: #10b981;
        padding: 4px 8px; border-radius: 6px; font-weight: bold; font-family: monospace;
    }
</style>

<div id="modalProjetoDiv" class="modal-projeto-overlay">
    <div class="modal-projeto-card">
        <button id="btnFecharX" class="btn-fechar-x"><i class="fa-solid fa-xmark"></i></button>
        
        <h3 style="font-size: 18px; margin-bottom: 10px; color: #f8fafc; display: flex; align-items: center; gap: 10px;">
            <i class="fa-solid fa-graduation-cap" style="color: #10b981;"></i> DEAPC - P43 - Agrícola
        </h3>
        
        <p style="color: #94a3b8; font-size: 13px; line-height: 1.6; margin-bottom: 20px; padding-bottom: 20px; border-bottom: 1px solid #334155;">
            Sistema de Monitorização, Automação de Rega e Gestão de Produções Agrícolas Baseado em IoT.
        </p>
        
        <div>
            <strong style="color: #cbd5e1; font-size: 14px;"><i class="fa-solid fa-users"></i> Elementos do Grupo:</strong>
            <ul class="grupo-lista">
                <li><span class="num-aluno">1242043</span> Ângelo Veiga</li>
                <li><span class="num-aluno">1242090</span> Vítor Reppen</li>
                <li><span class="num-aluno">1221991</span> Bernardo Lima</li>
            </ul>
        </div>
        
        <button id="btnFecharModal" style="width: 100%; background: #10b981; color: white; border: none; padding: 12px; border-radius: 8px; font-weight: bold; margin-top: 20px; cursor: pointer; transition: 0.2s;">
            Fechar
        </button>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const btnAbrir = document.getElementById('btnSobreProjeto');
        const modalDiv = document.getElementById('modalProjetoDiv');
        const btnX = document.getElementById('btnFecharX');
        const btnFechar = document.getElementById('btnFecharModal');

        if (btnAbrir && modalDiv) {
            btnAbrir.addEventListener('click', function() { modalDiv.classList.add('active'); });
            function fechar() { modalDiv.classList.remove('active'); }
            if(btnX) btnX.addEventListener('click', fechar);
            if(btnFechar) btnFechar.addEventListener('click', fechar);
            modalDiv.addEventListener('click', function(e) { if (e.target === modalDiv) fechar(); });
        }
        
        if(btnAbrir) {
            btnAbrir.addEventListener('mouseover', () => { btnAbrir.style.background = 'rgba(255,255,255,0.05)'; btnAbrir.style.color = 'white'; });
            btnAbrir.addEventListener('mouseout', () => { btnAbrir.style.background = 'transparent'; btnAbrir.style.color = '#cbd5e1'; });
        }
    });
</script>