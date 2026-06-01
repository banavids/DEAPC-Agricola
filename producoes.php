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
    <title>Produções Agrícolas - FarmSmart OS</title>
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
                        <i class="fas fa-bars"></i>
                    </button>
                    <h1 class="page-title">Produções Agrícolas</h1>
                </div>
                
                <div class="topbar-right">
                    <div class="notifications">
                        <i class="fas fa-bell"></i>
                        <span class="badge"></span>
                    </div>
                    <div class="user-profile">
                        <div class="avatar"><i class="fas fa-user"></i></div>
                        <span class="user-name">Gestor</span>
                    </div>
                </div>
            </header>

            <main class="content-body">
                <div class="dashboard-content">
                    
                    <div class="page-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
                        <div class="header-left">
                            <h2 class="page-title">Lista de Produções</h2>
                            <p class="header-subtitle" style="color: var(--text-muted); font-size: 13px;">Gerencie todas as culturas e produções ativas</p>
                        </div>
                        <button class="btn btn-green" id="btnNovaProducao">
                            <i class="fas fa-plus"></i> Nova Produção
                        </button>
                    </div>

                    <div class="light-card full-width">
                        
                        <div class="toolbar" style="margin-bottom: 25px;">
                            <div class="search-box" style="flex: 1; max-width: 300px; margin-right: 15px;">
                                <i class="fas fa-search"></i>
                                <input type="text" id="searchInput" placeholder="Procurar produções...">
                            </div>
                            
                            <div style="display: flex; gap: 15px;">
                                <div class="filter-group">
                                    <select id="filterEstado" class="form-control">
                                        <option value="">Todos os Estados</option>
                                        <option value="ativo">Ativo</option>
                                        <option value="atraso">Atraso</option>
                                        <option value="planejado">Planejado</option>
                                        <option value="concluido">Concluído</option>
                                    </select>
                                </div>

                                <div class="filter-group">
                                    <select id="filterCultura" class="form-control">
                                        <option value="">Todas as Culturas</option>
                                        <option value="tomates">Tomates</option>
                                        <option value="alfaces">Alfaces</option>
                                        <option value="macas">Maçãs</option>
                                        <option value="morangos">Morangos</option>
                                        <option value="cenoura">Cenoura</option>
                                    </select>
                                </div>

                                <button class="btn-outline" id="btnLimparFiltros">
                                    <i class="fas fa-times"></i> Limpar
                                </button>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="modern-table productions-table">
                                <thead>
                                    <tr>
                                        <th>Nome / Cultura</th>
                                        <th>Estado</th>
                                        <th>Humidade / Temp</th>
                                        <th>Responsável</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody id="producoesList">
                                    <tr>
                                        <td>
                                            <span class="td-title text-sm">Estufa 1</span>
                                            <span class="td-sub">Tomates</span>
                                        </td>
                                        <td><span class="status-badge badge-info">Ativo</span></td>
                                        <td>
                                            <span class="td-title text-sm">65% / 24°C</span>
                                        </td>
                                        <td><span class="td-sub text-dark">João Silva</span></td>
                                        <td>
                                            <button class="btn-icon"><i class="fas fa-pen"></i></button>
                                            <button class="btn-icon text-red"><i class="fas fa-trash"></i></button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="empty-state" id="emptyState" style="display: none; text-align: center; padding: 40px;">
                            <i class="fas fa-inbox" style="font-size: 40px; color: var(--text-muted); margin-bottom: 15px;"></i>
                            <h3 style="color: var(--text-dark); margin-bottom: 5px;">Nenhuma produção encontrada</h3>
                            <p style="color: var(--text-muted); font-size: 13px;">Tente ajustar os filtros ou criar uma nova produção</p>
                        </div>

                    </div>
                </div>
                </main>
        </div>
    </div>

    <div class="modal" id="modalNovaProducao" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: 1000; align-items: center; justify-content: center;">
        <div class="modal-overlay" id="modalOverlay" style="position: absolute; width: 100%; height: 100%; background: rgba(0,0,0,0.6);"></div>
        <div class="modal-content light-card" style="position: relative; width: 90%; max-width: 600px; z-index: 1001; max-height: 90vh; overflow-y: auto;">
            
            <div class="modal-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: 1px solid #e2e8f0; padding-bottom: 15px;">
                <h2 style="color: var(--text-dark); font-size: 18px;">Nova Produção</h2>
                <button class="btn-icon modal-close" id="btnFecharModal" style="font-size: 18px;">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form class="producao-form" id="formNovaProducao">
                <div class="form-row">
                    <div class="form-group flex-1">
                        <label>Nome da Produção *</label>
                        <input type="text" name="nome" class="form-control" placeholder="Ex: Estufa 1" required>
                    </div>
                    <div class="form-group flex-1">
                        <label>Tipo de Cultura *</label>
                        <select name="cultura" class="form-control" required>
                            <option value="">Selecione uma cultura</option>
                            <option value="Tomates">Tomates</option>
                            <option value="Alfaces">Alfaces</option>
                            <option value="Maçãs">Maçãs</option>
                            <option value="Morangos">Morangos</option>
                            <option value="Cenoura">Cenoura</option>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group flex-1">
                        <label>Localização *</label>
                        <input type="text" name="localizacao" class="form-control" placeholder="Ex: Setor Norte" required>
                    </div>
                    <div class="form-group flex-1">
                        <label>Data de Plantio *</label>
                        <input type="date" name="dataPlantio" class="form-control" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group flex-1">
                        <label>Área (m²) *</label>
                        <input type="number" name="area" class="form-control" placeholder="Ex: 500" step="0.01" required>
                    </div>
                    <div class="form-group flex-1">
                        <label>Quantidade de Plantas *</label>
                        <input type="number" name="quantidade" class="form-control" placeholder="Ex: 1000" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group flex-1">
                        <label>Responsável *</label>
                        <input type="text" name="responsavel" class="form-control" placeholder="Ex: João Silva" required>
                    </div>
                    <div class="form-group flex-1">
                        <label>Estado *</label>
                        <select name="estado" class="form-control" required>
                            <option value="">Selecione o estado</option>
                            <option value="planejado">Planejado</option>
                            <option value="ativo">Ativo</option>
                            <option value="atraso">Atraso</option>
                        </select>
                    </div>
                </div>

                <div class="form-group full-width" style="margin-bottom: 20px;">
                    <label>Observações</label>
                    <textarea name="observacoes" class="form-control" placeholder="Notas adicionais sobre a produção..." rows="3" style="width: 100%;"></textarea>
                </div>

                <div class="action-bar" style="justify-content: flex-end;">
                    <button type="button" class="btn-outline" id="btnCancelarModal">Cancelar</button>
                    <button type="submit" class="btn btn-green">Criar Produção</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Lógica do Menu Mobile
        document.getElementById('menuToggle').addEventListener('click', function() {
            document.getElementById('sidebar').classList.add('open');
            document.getElementById('sidebarOverlay').classList.add('active');
        });

        document.getElementById('sidebarOverlay').addEventListener('click', function() {
            document.getElementById('sidebar').classList.remove('open');
            this.classList.remove('active');
        });

        // Lógica simples do Modal
        const modal = document.getElementById('modalNovaProducao');
        const btnNova = document.getElementById('btnNovaProducao');
        const btnFechar = document.getElementById('btnFecharModal');
        const btnCancelar = document.getElementById('btnCancelarModal');
        const overlay = document.getElementById('modalOverlay');

        function openModal() { modal.style.display = 'flex'; }
        function closeModal() { modal.style.display = 'none'; }

        btnNova.addEventListener('click', openModal);
        btnFechar.addEventListener('click', closeModal);
        btnCancelar.addEventListener('click', closeModal);
        overlay.addEventListener('click', closeModal);
    </script>
</body>
</html>