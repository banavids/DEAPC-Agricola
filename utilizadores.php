<?php
session_start();
require_once 'scripts/database.php';

// Proteção de página
if (!isset($_SESSION['user_id'])) {
    header("Location: login/login-farmsmart.html");
    exit;
}

// =========================================================================
// 1. DESCOBRIR QUEM ESTÁ LOGADO (Para aplicar as regras de segurança)
// =========================================================================
$idLogado = $_SESSION['user_id'];
// Vai buscar o grupo do utilizador atual. Se der erro, assume 3 (Operário) por segurança
$dadosLogado = db_select('SELECT USR_group_id FROM tblUser WHERE USR_id = :id', [':id' => $idLogado]);
$grupoLogado = !empty($dadosLogado) ? (int)$dadosLogado[0]['USR_group_id'] : 3;


// =========================================================================
// 2. LÓGICA DE CRIAÇÃO & EDIÇÃO DO UTILIZADOR
// =========================================================================
$erro = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    
    // --- CRIAR UTILIZADOR ---
    if ($_POST['action'] === 'criar_user' && $grupoLogado <= 2) { // Apenas Admin e Gestor
        $nome = $_POST['nome'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $estado = $_POST['estado'];
        
        // Regra de Ouro do Gestor: Se for Gestor (2), o grupo criado é SEMPRE 3 (Operário)
        $grupo = ($grupoLogado == 2) ? 3 : (int)$_POST['grupo'];

        if (!empty($nome) && !empty($email) && !empty($password)) {
            try {
                db_insert('INSERT INTO tblUser (USR_nome, USR_email, USR_password, USR_group_id, USR_estado) 
                          VALUES (:nome, :email, :password, :grupo, :estado)', [
                    ':nome' => $nome, ':email' => $email, ':password' => $password, 
                    ':grupo' => $grupo, ':estado' => $estado
                ]);
                header("Location: utilizadores.php?sucesso=criado");
                exit;
            } catch (Exception $e) {
                if (strpos($e->getMessage(), 'UNIQUE constraint failed: tblUser.USR_email') !== false) {
                    $erro = "Já existe um utilizador registado com o email: " . htmlspecialchars($email);
                } else { $erro = "Erro: " . $e->getMessage(); }
            }
        }
    }
    
    // --- EDITAR UTILIZADOR ---
    elseif ($_POST['action'] === 'editar_user' && $grupoLogado <= 2) { // Apenas Admin e Gestor
        $editId = (int)$_POST['edit_id'];
        $nome = $_POST['nome'] ?? '';
        $estado = $_POST['estado'];
        
        try {
            // Regra do Admin: Pode editar os roles (cargos)
            if ($grupoLogado == 1 && isset($_POST['grupo'])) {
                $grupo = (int)$_POST['grupo'];
                db_update('UPDATE tblUser SET USR_nome = :nome, USR_group_id = :grupo, USR_estado = :estado WHERE USR_id = :id', [
                    ':nome' => $nome, ':grupo' => $grupo, ':estado' => $estado, ':id' => $editId
                ]);
            } 
            // Regra do Gestor: Só edita o nome e o estado (não o role)
            else {
                db_update('UPDATE tblUser SET USR_nome = :nome, USR_estado = :estado WHERE USR_id = :id', [
                    ':nome' => $nome, ':estado' => $estado, ':id' => $editId
                ]);
            }
            header("Location: utilizadores.php?sucesso=editado");
            exit;
        } catch (Exception $e) {
            $erro = "Erro ao atualizar utilizador: " . $e->getMessage();
        }
    }
}

// Buscar utilizadores para mostrar na tabela
$utilizadores = db_select('SELECT * FROM tblUser ORDER BY USR_group_id ASC, USR_nome ASC');
?>
<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Utilizadores do Sistema - FarmSmart OS</title>
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
                    <button class="menu-toggle" id="menuToggle"><i class="fa-solid fa-bars"></i></button>
                    <h1 class="page-title">Controlo de Acessos</h1>
                </div>
                <div class="topbar-right">
                    <div class="notifications"><i class="fa-solid fa-bell"></i><span class="badge"></span></div>
                    <div class="user-profile">
                        <div class="avatar"><i class="fa-solid fa-user"></i></div>
                        <span class="user-name">Perfil</span>
                    </div>
                </div>
            </header>

            <main class="content-body">
                <div class="dashboard-content">
                    
                    <div class="page-header" style="margin-bottom: 20px; display: flex; justify-content: space-between;">
                        <h2 class="page-title">Utilizadores do Sistema</h2>
                        
                        <?php if ($grupoLogado < 3): ?>
                            <button class="btn btn-green" id="btnNovoUtilizador"><i class="fa-solid fa-user-plus"></i> Criar Utilizador</button>
                        <?php endif; ?>
                    </div>

                    <?php if (isset($_GET['sucesso'])): ?>
                        <div style="background-color: #d1fae5; color: #059669; padding: 15px; border-radius: 8px; margin-bottom: 20px; font-weight: 600;">
                            <i class="fa-solid fa-check-circle"></i> Operação realizada com sucesso!
                        </div>
                    <?php endif; ?>

                    <?php if ($erro): ?>
                        <div style="background-color: #fee2e2; color: #b91c1c; padding: 15px; border-radius: 8px; margin-bottom: 20px; font-weight: 600;">
                            <i class="fa-solid fa-circle-exclamation"></i> <?php echo $erro; ?>
                        </div>
                    <?php endif; ?>

                    <div class="light-card full-width">
                        <div class="table-responsive">
                            <table class="modern-table align-middle">
                                <thead>
                                    <tr>
                                        <th>Nome</th>
                                        <th>Email</th>
                                        <th>Cargo</th>
                                        <th>Estado</th>
                                        <?php if ($grupoLogado < 3): ?><th>Ações</th><?php endif; ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($utilizadores as $user): ?>
                                        <?php
                                        $primeiraLetra = strtoupper(substr($user['USR_nome'], 0, 1));
                                        
                                        if ($user['USR_group_id'] == 1) {
                                            $cargo = 'Administrador'; $icon = 'fa-star'; $cor = 'text-purple'; $avCor = 'avatar-purple';
                                        } elseif ($user['USR_group_id'] == 2) {
                                            $cargo = 'Gestor'; $icon = 'fa-shield-halved'; $cor = 'text-blue'; $avCor = 'avatar-blue';
                                        } else {
                                            $cargo = 'Operário'; $icon = 'fa-wrench'; $cor = 'text-grey'; $avCor = 'avatar-grey';
                                        }

                                        $estadoCls = (strtolower($user['USR_estado']) == 'ativo') ? 'bg-green-light text-green' : 'bg-red-light text-red';
                                        ?>
                                        <tr>
                                            <td>
                                                <div class="user-cell">
                                                    <div class="avatar-circle <?php echo $avCor; ?>"><?php echo $primeiraLetra; ?></div>
                                                    <span class="td-title"><?php echo htmlspecialchars($user['USR_nome']); ?></span>
                                                </div>
                                            </td>
                                            <td><span class="td-sub"><i class="fa-regular fa-envelope"></i> <?php echo htmlspecialchars($user['USR_email']); ?></span></td>
                                            <td><span class="role-tag <?php echo $cor; ?>"><i class="fa-solid <?php echo $icon; ?>"></i> <?php echo $cargo; ?></span></td>
                                            <td><span class="pill-badge <?php echo $estadoCls; ?>"><?php echo htmlspecialchars($user['USR_estado']); ?></span></td>
                                            
                                            <?php if ($grupoLogado < 3): ?>
                                                <td class="text-right">
                                                    <button class="btn-icon" onclick="abrirModalEdicao(<?php echo $user['USR_id']; ?>, '<?php echo addslashes($user['USR_nome']); ?>', '<?php echo addslashes($user['USR_email']); ?>', <?php echo $user['USR_group_id']; ?>, '<?php echo $user['USR_estado']; ?>')">
                                                        <i class="fa-solid fa-ellipsis-vertical"></i>
                                                    </button>
                                                </td>
                                            <?php endif; ?>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <div class="modal" id="modalUser" style="display: none; position: fixed; top:0; left:0; width:100%; height:100%; z-index:1000; align-items:center; justify-content:center;">
        <div class="modal-overlay" onclick="fecharModais()" style="position: absolute; width:100%; height:100%; background:rgba(0,0,0,0.6);"></div>
        <div class="modal-content light-card" style="position: relative; width: 90%; max-width: 500px; z-index: 1001;">
            <div class="modal-header" style="display:flex; justify-content:space-between; margin-bottom:20px; border-bottom:1px solid #e2e8f0; padding-bottom:15px;">
                <h2 style="font-size: 18px;">Criar Novo Utilizador</h2>
                <button class="btn-icon modal-close" onclick="fecharModais()"><i class="fas fa-times"></i></button>
            </div>
            <form method="POST">
                <input type="hidden" name="action" value="criar_user">
                <div class="form-group" style="margin-bottom: 15px;">
                    <label style="font-size: 12px; font-weight: 600;">Nome Completo *</label>
                    <input type="text" name="nome" class="form-control" style="width: 100%;" required>
                </div>
                <div class="form-group" style="margin-bottom: 15px;">
                    <label style="font-size: 12px; font-weight: 600;">Email *</label>
                    <input type="email" name="email" class="form-control" style="width: 100%;" required>
                </div>
                <div class="form-group" style="margin-bottom: 15px;">
                    <label style="font-size: 12px; font-weight: 600;">Password *</label>
                    <input type="password" name="password" class="form-control" style="width: 100%;" required>
                </div>
                <div class="form-row" style="display: flex; gap: 15px; margin-bottom: 20px;">
                    <div class="form-group flex-1" style="flex: 1;">
                        <label style="font-size: 12px; font-weight: 600;">Cargo *</label>
                        <select name="grupo" class="form-control" style="width: 100%;" <?php echo ($grupoLogado == 2) ? 'disabled' : ''; ?>>
                            <?php if ($grupoLogado == 1): ?>
                                <option value="1">Administrador</option>
                                <option value="2">Gestor</option>
                            <?php endif; ?>
                            <option value="3" selected>Operário</option>
                        </select>
                    </div>
                    <div class="form-group flex-1" style="flex: 1;">
                        <label style="font-size: 12px; font-weight: 600;">Estado *</label>
                        <select name="estado" class="form-control" style="width: 100%;">
                            <option value="Ativo" selected>Ativo</option>
                            <option value="Suspenso">Suspenso</option>
                        </select>
                    </div>
                </div>
                <div class="action-bar" style="display: flex; justify-content: flex-end; gap: 10px;">
                    <button type="button" class="btn-outline" onclick="fecharModais()">Cancelar</button>
                    <button type="submit" class="btn btn-green">Gravar Utilizador</button>
                </div>
            </form>
        </div>
    </div>

    <div class="modal" id="modalEditUser" style="display: none; position: fixed; top:0; left:0; width:100%; height:100%; z-index:1000; align-items:center; justify-content:center;">
        <div class="modal-overlay" onclick="fecharModais()" style="position: absolute; width:100%; height:100%; background:rgba(0,0,0,0.6);"></div>
        <div class="modal-content light-card" style="position: relative; width: 90%; max-width: 500px; z-index: 1001;">
            <div class="modal-header" style="display:flex; justify-content:space-between; margin-bottom:20px; border-bottom:1px solid #e2e8f0; padding-bottom:15px;">
                <h2 style="font-size: 18px;">Editar Utilizador</h2>
                <button class="btn-icon modal-close" onclick="fecharModais()"><i class="fas fa-times"></i></button>
            </div>
            <form method="POST">
                <input type="hidden" name="action" value="editar_user">
                <input type="hidden" name="edit_id" id="edit_id"> <div class="form-group" style="margin-bottom: 15px;">
                    <label style="font-size: 12px; font-weight: 600;">Nome Completo *</label>
                    <input type="text" name="nome" id="edit_nome" class="form-control" style="width: 100%;" required>
                </div>
                
                <div class="form-group" style="margin-bottom: 15px;">
                    <label style="font-size: 12px; font-weight: 600;">Email (Não Editável)</label>
                    <input type="email" id="edit_email" class="form-control" style="width: 100%; background-color:#f1f5f9;" readonly>
                </div>

                <div class="form-row" style="display: flex; gap: 15px; margin-bottom: 20px;">
                    <div class="form-group flex-1" style="flex: 1;">
                        <label style="font-size: 12px; font-weight: 600;">Cargo *</label>
                        <select name="grupo" id="edit_grupo" class="form-control" style="width: 100%;" <?php echo ($grupoLogado == 2) ? 'disabled' : ''; ?>>
                            <option value="1">Administrador</option>
                            <option value="2">Gestor</option>
                            <option value="3">Operário</option>
                        </select>
                    </div>
                    <div class="form-group flex-1" style="flex: 1;">
                        <label style="font-size: 12px; font-weight: 600;">Estado *</label>
                        <select name="estado" id="edit_estado" class="form-control" style="width: 100%;" required>
                            <option value="Ativo">Ativo</option>
                            <option value="Suspenso">Suspenso</option>
                        </select>
                    </div>
                </div>
                <div class="action-bar" style="display: flex; justify-content: flex-end; gap: 10px;">
                    <button type="button" class="btn-outline" onclick="fecharModais()">Cancelar</button>
                    <button type="submit" class="btn btn-blue">Atualizar Dados</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Menu Lateral Mobile
        document.getElementById('menuToggle').addEventListener('click', function() {
            document.getElementById('sidebar').classList.add('open');
            document.getElementById('sidebarOverlay').classList.add('active');
        });
        document.getElementById('sidebarOverlay').addEventListener('click', function() {
            document.getElementById('sidebar').classList.remove('open');
            this.classList.remove('active');
        });

        // Controlo de Modais
        const modalCreate = document.getElementById('modalUser');
        const modalEdit = document.getElementById('modalEditUser');
        
        // Se o botão Criar existir (Admin/Gestor), liga o clique
        const btnCriar = document.getElementById('btnNovoUtilizador');
        if(btnCriar) {
            btnCriar.addEventListener('click', () => { modalCreate.style.display = 'flex'; });
        }

        function fecharModais() { 
            modalCreate.style.display = 'none'; 
            modalEdit.style.display = 'none'; 
        }

        // Função chamada quando se clica nos 3 pontinhos para preencher o formulário
        function abrirModalEdicao(id, nome, email, grupo, estado) {
            document.getElementById('edit_id').value = id;
            document.getElementById('edit_nome').value = nome;
            document.getElementById('edit_email').value = email;
            document.getElementById('edit_grupo').value = grupo;
            
            // Oculta Maiúsculas/Minúsculas garantindo que seleciona a opção correta
            let estadoSelect = document.getElementById('edit_estado');
            for(let i = 0; i < estadoSelect.options.length; i++) {
                if(estadoSelect.options[i].value.toLowerCase() === estado.toLowerCase()) {
                    estadoSelect.selectedIndex = i;
                    break;
                }
            }
            
            modalEdit.style.display = 'flex';
        }
    </script>
</body>
</html>