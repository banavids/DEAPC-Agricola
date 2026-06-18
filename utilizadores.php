<?php
session_start();
require_once 'scripts/database.php';

// Proteção de página
if (!isset($_SESSION['user_id'])) {
    header("Location: login/login-farmsmart.html");
    exit;
}
if ($_SESSION['user_group'] == 3) {
    header("Location: operador.php");
    exit;
}


$idLogado = $_SESSION['user_id'];
$dadosLogado = db_select('SELECT USR_group_id FROM tblUser WHERE USR_id = :id', [':id' => $idLogado]);
$grupoLogado = !empty($dadosLogado) ? (int)$dadosLogado[0]['USR_group_id'] : 3;


$erro = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    
    if ($_POST['action'] === 'criar_user' && $grupoLogado <= 2) { 
        $nome = trim($_POST['nome'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $estado = $_POST['estado'];
        
        $grupo = ($grupoLogado == 2) ? 3 : (int)$_POST['grupo'];

        if (!empty($nome) && !empty($email) && !empty($password)) {
            
            $nomeExiste = db_select('SELECT USR_id FROM tblUser WHERE LOWER(USR_nome) = LOWER(:nome)', [':nome' => $nome]);
            
            $emailExiste = db_select('SELECT USR_id FROM tblUser WHERE LOWER(USR_email) = LOWER(:email)', [':email' => $email]);

            if (!empty($nomeExiste)) {
                $erro = "Já existe um utilizador registado com o nome: " . htmlspecialchars($nome);
            } 
            elseif (!empty($emailExiste)) {
                $erro = "Já existe um utilizador registado com o email: " . htmlspecialchars($email);
            } 
            else {
                try {
                    db_insert('INSERT INTO tblUser (USR_nome, USR_email, USR_password, USR_group_id, USR_estado) 
                              VALUES (:nome, :email, :password, :grupo, :estado)', [
                        ':nome' => $nome, ':email' => $email, ':password' => $password, 
                        ':grupo' => $grupo, ':estado' => $estado
                    ]);
                    header("Location: utilizadores.php?sucesso=criado");
                    exit;
                } catch (Exception $e) {
                    $erro = "Erro: " . $e->getMessage();
                }
            }
        }
    }
    
    elseif ($_POST['action'] === 'editar_user' && $grupoLogado <= 2) { 
        $editId = (int)$_POST['edit_id'];
        $nome = $_POST['nome'] ?? '';
        $estado = $_POST['estado'];
        
        try {
            if ($grupoLogado == 1 && isset($_POST['grupo'])) {
                $grupo = (int)$_POST['grupo'];
                db_update('UPDATE tblUser SET USR_nome = :nome, USR_group_id = :grupo, USR_estado = :estado WHERE USR_id = :id', [
                    ':nome' => $nome, ':grupo' => $grupo, ':estado' => $estado, ':id' => $editId
                ]);
            } else {
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
    
    elseif ($_POST['action'] === 'mudar_estado' && $grupoLogado <= 2) {
        $idTarget = (int)$_POST['user_id'];
        $novoEstado = $_POST['novo_estado'];
        

        if ($idTarget === $idLogado) {
            $erro = "Não podes suspender a tua própria conta!";
        } else {
            try {
                db_update('UPDATE tblUser SET USR_estado = :estado WHERE USR_id = :id', [
                    ':estado' => $novoEstado, ':id' => $idTarget
                ]);
                header("Location: utilizadores.php?sucesso=estado_alterado");
                exit;
            } catch (Exception $e) {
                $erro = "Erro ao alterar estado: " . $e->getMessage();
            }
        }
    }
}

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

                                        $estadoAtual = $user['USR_estado'] ?? 'Ativo';
                                        $estadoCls = (strtolower($estadoAtual) == 'ativo') ? 'bg-green-light text-green' : 'bg-red-light text-red';
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
                                            <td><span class="pill-badge <?php echo $estadoCls; ?>"><?php echo htmlspecialchars($estadoAtual); ?></span></td>
                                            
                                            <?php if ($grupoLogado < 3): ?>
                                                <td class="text-right" style="display: flex; gap: 10px; justify-content: flex-end; align-items: center;">
                                                    
                                                    <?php if ($user['USR_id'] !== $idLogado): ?>
                                                        <?php
                                                        $corBotaoToggle = (strtolower($estadoAtual) === 'ativo') ? '#ef4444' : '#10b981';
                                                        $iconBotaoToggle = (strtolower($estadoAtual) === 'ativo') ? 'fa-ban' : 'fa-check';
                                                        $textoAcao = (strtolower($estadoAtual) === 'ativo') ? 'Suspender' : 'Ativar';
                                                        ?>
                                                        <button onclick="mudarEstadoUser(<?php echo $user['USR_id']; ?>, '<?php echo htmlspecialchars($estadoAtual); ?>')" class="btn" style="background: <?php echo $corBotaoToggle; ?>; color: white; padding: 6px 12px; border:none; border-radius: 4px; cursor: pointer; font-size: 12px; display: flex; align-items: center; gap: 5px;">
                                                            <i class="fa-solid <?php echo $iconBotaoToggle; ?>"></i> <?php echo $textoAcao; ?>
                                                        </button>
                                                    <?php endif; ?>

                                                    <button class="btn-icon" onclick="abrirModalEdicao(<?php echo $user['USR_id']; ?>, '<?php echo addslashes($user['USR_nome']); ?>', '<?php echo addslashes($user['USR_email']); ?>', <?php echo $user['USR_group_id']; ?>, '<?php echo $estadoAtual; ?>')">
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
                <input type="hidden" name="edit_id" id="edit_id"> 
                
                <div class="form-group" style="margin-bottom: 15px;">
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
        
        const btnCriar = document.getElementById('btnNovoUtilizador');
        if(btnCriar) {
            btnCriar.addEventListener('click', () => { modalCreate.style.display = 'flex'; });
        }

        function fecharModais() { 
            modalCreate.style.display = 'none'; 
            modalEdit.style.display = 'none'; 
        }

        function abrirModalEdicao(id, nome, email, grupo, estado) {
            document.getElementById('edit_id').value = id;
            document.getElementById('edit_nome').value = nome;
            document.getElementById('edit_email').value = email;
            document.getElementById('edit_grupo').value = grupo;
            
            let estadoSelect = document.getElementById('edit_estado');
            for(let i = 0; i < estadoSelect.options.length; i++) {
                if(estadoSelect.options[i].value.toLowerCase() === estado.toLowerCase()) {
                    estadoSelect.selectedIndex = i;
                    break;
                }
            }
            
            modalEdit.style.display = 'flex';
        }
        function mudarEstadoUser(idUser, estadoAtual) {
            let novoEstado = estadoAtual.toLowerCase() === 'ativo' ? 'Suspenso' : 'Ativo';
            let textoConfirmacao = estadoAtual.toLowerCase() === 'ativo' ? 'suspender' : 'ativar';

            if(confirm(`Tens a certeza que queres ${textoConfirmacao} este utilizador?`)) {
                let form = document.createElement('form');
                form.method = 'POST';
                form.action = 'utilizadores.php';

                let inputAction = document.createElement('input');
                inputAction.type = 'hidden';
                inputAction.name = 'action';
                inputAction.value = 'mudar_estado';
                form.appendChild(inputAction);

                let inputId = document.createElement('input');
                inputId.type = 'hidden';
                inputId.name = 'user_id';
                inputId.value = idUser;
                form.appendChild(inputId);

                let inputEstado = document.createElement('input');
                inputEstado.type = 'hidden';
                inputEstado.name = 'novo_estado';
                inputEstado.value = novoEstado;
                form.appendChild(inputEstado);

                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>
</body>
</html>