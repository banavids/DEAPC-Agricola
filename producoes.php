<?php
session_start();
require_once 'scripts/database.php';

// Proteção de página
if (!isset($_SESSION['user_id'])) {
    header("Location: login/login-farmsmart.html");
    exit;
}

$user_id = $_SESSION['user_id'];

// ---------------------------------------------------------
// 1. PROCESSAR FORMULÁRIO (INSERIR, EDITAR OU ELIMINAR)
// ---------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao'])) {
    $acao = $_POST['acao'];

    // ELIMINAR PRODUÇÃO
    if ($acao === 'eliminar_producao') {
        $id_eliminar = $_POST['prd_id'];
        $query = "DELETE FROM tblProducao WHERE PRD_id = :id";
        db_update($query, [':id' => $id_eliminar]);

        if (function_exists('registar_system_log')) registar_system_log($user_id, "Eliminar Produção", "Eliminou a produção ID $id_eliminar");
    } 
    else {
        // INSERIR OU EDITAR
        $nome = $_POST['nome'];
        $cultura = $_POST['cultura'];
        $zona_id = $_POST['zona_id'];
        $estado = $_POST['estado'];

        if ($acao === 'nova_producao') {
            $query = "INSERT INTO tblProducao (PRD_nome, PRD_cultura, PRD_zona_id, PRD_estado) VALUES (:nome, :cultura, :zona, :estado)";
            db_insert($query, [
                ':nome' => $nome,
                ':cultura' => $cultura,
                ':zona' => $zona_id,
                ':estado' => $estado
            ]);
            if (function_exists('registar_system_log')) registar_system_log($user_id, "Nova Produção", "Criou a produção: $nome");
        } 
        elseif ($acao === 'editar_producao') {
            $id_editar = $_POST['prd_id'];
            $query = "UPDATE tblProducao SET PRD_nome = :nome, PRD_cultura = :cultura, PRD_zona_id = :zona, PRD_estado = :estado WHERE PRD_id = :id";
            db_update($query, [
                ':nome' => $nome,
                ':cultura' => $cultura,
                ':zona' => $zona_id,
                ':estado' => $estado,
                ':id' => $id_editar
            ]);
            if (function_exists('registar_system_log')) registar_system_log($user_id, "Editar Produção", "Atualizou a produção ID $id_editar: $nome");
        }
    }

    // Recarregar a página
    header("Location: producoes.php");
    exit;
}

// ---------------------------------------------------------
// 2. OBTER DADOS PARA A PÁGINA
// ---------------------------------------------------------
$zonas_disponiveis = db_select("SELECT ZON_id, ZON_nome FROM tblZona ORDER BY ZON_nome ASC");

$producoes = db_select("
    SELECT p.PRD_id, p.PRD_nome, p.PRD_cultura, p.PRD_zona_id, p.PRD_estado, z.ZON_nome 
    FROM tblProducao p
    LEFT JOIN tblZona z ON p.PRD_zona_id = z.ZON_id
    ORDER BY p.PRD_id DESC
");

$total_producoes = count($producoes);

function getEstadoBadge($estado) {
    $estado = strtolower(trim($estado));
    if ($estado === 'ativo') return '<span class="status-badge bg-green">Ativo</span>';
    if ($estado === 'atenção' || $estado === 'atencao') return '<span class="status-badge bg-yellow">Atenção</span>';
    if ($estado === 'concluído' || $estado === 'concluido') return '<span class="status-badge" style="background:#f1f5f9; color:#64748b;">Concluído</span>';
    return '<span class="status-badge bg-blue-light">' . ucfirst($estado) . '</span>';
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
        <?php require_once 'scripts/sidebar.php'; ?>
        <div class="sidebar-overlay" id="sidebarOverlay"></div>

        <div class="main-wrapper">
            <header class="topbar">
                <div class="topbar-left">
                    <button class="menu-toggle" id="menuToggle">
                        <i class="fa-solid fa-bars"></i>
                    </button>
                    <h1 class="page-title">Produções Agrícolas</h1>
                </div>
                
                <div class="topbar-right">
                    <div class="user-profile">
                        <div class="avatar"><i class="fa-solid fa-user"></i></div>
                        <span class="user-name">Gestor</span>
                    </div>
                </div>
            </header>

            <main class="content-body">
                <div class="dashboard-content">
                    
                    <div class="page-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
                        <div class="header-left">
                            <h2 class="page-title">Lista de Produções</h2>
                        </div>
                        <button class="btn btn-green" id="btnNovaProducao">
                            <i class="fa-solid fa-plus"></i> Nova Produção
                        </button>
                    </div>

                    <div class="light-card full-width">
                        <div class="table-responsive">
                            <table class="modern-table productions-table">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Nome / Cultura</th>
                                        <th>Zona Alocada</th>
                                        <th>Estado</th>
                                        <th style="text-align: right;">Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($total_producoes > 0): ?>
                                        <?php foreach ($producoes as $prd): ?>
                                            <tr>
                                                <td style="color: var(--text-muted); font-size: 13px;">#<?php echo $prd['PRD_id']; ?></td>
                                                <td>
                                                    <span class="td-title text-sm"><?php echo htmlspecialchars($prd['PRD_nome']); ?></span>
                                                    <span class="td-sub"><?php echo htmlspecialchars($prd['PRD_cultura']); ?></span>
                                                </td>
                                                <td>
                                                    <span class="td-title text-sm" style="color: var(--primary);">
                                                        <i class="fa-solid fa-location-dot"></i> <?php echo htmlspecialchars($prd['ZON_nome'] ?? 'Zona Indefinida'); ?>
                                                    </span>
                                                </td>
                                                <td><?php echo getEstadoBadge($prd['PRD_estado']); ?></td>
                                                <td style="text-align: right;">
                                                    <button class="btn-icon btn-edit" 
                                                        data-id="<?php echo $prd['PRD_id']; ?>"
                                                        data-nome="<?php echo htmlspecialchars($prd['PRD_nome']); ?>"
                                                        data-cultura="<?php echo htmlspecialchars($prd['PRD_cultura']); ?>"
                                                        data-zona="<?php echo $prd['PRD_zona_id']; ?>"
                                                        data-estado="<?php echo htmlspecialchars($prd['PRD_estado']); ?>">
                                                        <i class="fa-solid fa-pen"></i>
                                                    </button>
                                                    
                                                    <button class="btn-icon text-red btn-delete" data-id="<?php echo $prd['PRD_id']; ?>">
                                                        <i class="fa-solid fa-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="5" style="text-align: center; padding: 40px;">
                                                <p style="color: var(--text-muted);">Nenhuma produção encontrada.</p>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <form id="formEliminarProducao" method="POST" action="producoes.php" style="display: none;">
        <input type="hidden" name="acao" value="eliminar_producao">
        <input type="hidden" name="prd_id" id="inputEliminarId" value="">
    </form>

    <div class="modal" id="modalNovaProducao" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: 1000; align-items: center; justify-content: center;">
        <div class="modal-overlay" id="modalOverlay" style="position: absolute; width: 100%; height: 100%; background: rgba(0,0,0,0.7); backdrop-filter: blur(3px);"></div>
        <div class="modal-content light-card" style="position: relative; width: 90%; max-width: 500px; z-index: 1001; max-height: 90vh; overflow-y: auto;">
            
            <div class="modal-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: 1px solid var(--border-color); padding-bottom: 15px;">
                <h2 id="modalTitleText" style="color: var(--text-main); font-size: 18px;"><i class="fa-solid fa-seedling text-green"></i> Nova Produção</h2>
                <button class="btn-icon modal-close" id="btnFecharModal" style="font-size: 18px;">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <form class="producao-form" id="formNovaProducao" method="POST" action="producoes.php">
                <input type="hidden" name="acao" id="inputAcao" value="nova_producao">
                <input type="hidden" name="prd_id" id="inputId" value="">
                
                <div class="form-group full-width" style="margin-bottom: 15px;">
                    <label>Nome da Produção <span class="text-red">*</span></label>
                    <input type="text" name="nome" id="inputNome" class="form-control" required>
                </div>

                <div class="form-group full-width" style="margin-bottom: 15px;">
                    <label>Cultura <span class="text-red">*</span></label>
                    <input type="text" name="cultura" id="inputCultura" class="form-control" required>
                </div>

                <div class="form-group full-width" style="margin-bottom: 15px;">
                    <label>Zona (Localização) <span class="text-red">*</span></label>
                    <select name="zona_id" id="inputZona" class="form-control" required>
                        <option value="">Selecione uma Zona</option>
                        <?php foreach($zonas_disponiveis as $zona): ?>
                            <option value="<?php echo $zona['ZON_id']; ?>"><?php echo htmlspecialchars($zona['ZON_nome']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group full-width" style="margin-bottom: 25px;">
                    <label>Estado Inicial <span class="text-red">*</span></label>
                    <select name="estado" id="inputEstado" class="form-control" required>
                        <option value="Ativo">Ativo</option>
                        <option value="Atenção">Atenção</option>
                        <option value="Concluído">Concluído</option>
                    </select>
                </div>

                <div class="action-bar" style="justify-content: flex-end; border-top: 1px solid var(--border-color); padding-top: 15px;">
                    <button type="button" class="btn-outline" id="btnCancelarModal">Cancelar</button>
                    <button type="submit" class="btn btn-green"><i class="fa-solid fa-floppy-disk"></i> Guardar</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // 1. Menu Mobile
            const menuToggle = document.getElementById('menuToggle');
            const sidebar = document.getElementById('sidebar');
            const sidebarOverlay = document.getElementById('sidebarOverlay');

            if (menuToggle && sidebar && sidebarOverlay) {
                menuToggle.addEventListener('click', () => {
                    sidebar.classList.add('open');
                    sidebarOverlay.classList.add('active');
                });
                sidebarOverlay.addEventListener('click', function() {
                    sidebar.classList.remove('open');
                    this.classList.remove('active');
                });
            }

            // 2. Lógica do Modal
            const modal = document.getElementById('modalNovaProducao');
            const formProducao = document.getElementById('formNovaProducao');
            const inputAcao = document.getElementById('inputAcao');
            const inputId = document.getElementById('inputId');
            const modalTitleText = document.getElementById('modalTitleText');
            
            const openModal = () => { if(modal) modal.style.display = 'flex'; };
            const closeModal = () => { if(modal) modal.style.display = 'none'; };

            document.getElementById('btnFecharModal')?.addEventListener('click', closeModal);
            document.getElementById('btnCancelarModal')?.addEventListener('click', closeModal);
            document.getElementById('modalOverlay')?.addEventListener('click', closeModal);

            // Ação: NOVA Produção
            const btnNova = document.getElementById('btnNovaProducao');
            if(btnNova) {
                btnNova.addEventListener('click', () => {
                    formProducao.reset();
                    inputAcao.value = 'nova_producao';
                    inputId.value = '';
                    modalTitleText.innerHTML = '<i class="fa-solid fa-seedling text-green"></i> Nova Produção';
                    openModal();
                });
            }

            // Ação: EDITAR Produção
            const botoesEditar = document.querySelectorAll('.btn-edit');
            botoesEditar.forEach(btn => {
                btn.addEventListener('click', function() {
                    document.getElementById('inputNome').value = this.dataset.nome;
                    document.getElementById('inputCultura').value = this.dataset.cultura;
                    document.getElementById('inputZona').value = this.dataset.zona;
                    document.getElementById('inputEstado').value = this.dataset.estado;

                    inputAcao.value = 'editar_producao';
                    inputId.value = this.dataset.id;
                    modalTitleText.innerHTML = '<i class="fa-solid fa-pen text-blue"></i> Editar Produção';
                    openModal();
                });
            });

            // Ação: ELIMINAR Produção
            const botoesEliminar = document.querySelectorAll('.btn-delete');
            botoesEliminar.forEach(btn => {
                btn.addEventListener('click', function() {
                    if(confirm('Tem a certeza que deseja eliminar esta produção? Esta ação não pode ser desfeita.')) {
                        document.getElementById('inputEliminarId').value = this.dataset.id;
                        document.getElementById('formEliminarProducao').submit();
                    }
                });
            });

            // Validação Obrigatória W3
            const forms = document.querySelectorAll('form.producao-form');
            forms.forEach(form => {
                form.setAttribute('novalidate', true); 
                form.addEventListener('submit', function(e) {
                    let formValido = true;
                    form.querySelectorAll('.msg-erro-validacao').forEach(msg => msg.remove());
                    form.querySelectorAll('.input-erro').forEach(input => input.classList.remove('input-erro'));

                    const camposObrigatorios = form.querySelectorAll('[required]');
                    camposObrigatorios.forEach(campo => {
                        if (!campo.value.trim()) {
                            formValido = false;
                            campo.classList.add('input-erro');
                            const msgErro = document.createElement('span');
                            msgErro.classList.add('msg-erro-validacao');
                            msgErro.style.color = '#ef4444';
                            msgErro.style.fontSize = '12px';
                            msgErro.style.marginTop = '4px';
                            msgErro.style.display = 'block';
                            msgErro.innerHTML = '<i class="fa-solid fa-triangle-exclamation"></i> Preenchimento obrigatório.';
                            campo.parentNode.insertBefore(msgErro, campo.nextSibling);
                        }
                    });

                    if (!formValido) e.preventDefault();
                });
            });
        });
    </script>
</body>
</html>