<?php
session_start();
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
    <title>Tarefas - FarmSmart OS</title>
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
                    <button class="menu-toggle" id="menuToggle"><i class="fa-solid fa-bars"></i></button>
                    <h1 class="page-title">Gestão de Tarefas</h1>
                </div>
            </header>

            <main class="content-body">
                <div class="dashboard-content">
                
                    <div class="light-card" style="margin-bottom: 30px; border-top: 4px solid #10b981;">
                        <h3 style="margin-bottom: 15px;"><i class="fa-solid fa-plus"></i> Adicionar Nova Tarefa</h3>
                        <form id="formNovaTarefa" style="display: flex; gap: 15px; flex-wrap: wrap; align-items: flex-end;">
                            
                            <div style="flex-grow: 2; min-width: 250px;">
                                <label style="display: block; font-size: 13px; color: #64748b; margin-bottom: 5px;">Descrição da Tarefa</label>
                                <input type="text" id="nova_desc" required style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px;">
                            </div>

                            <div style="flex-grow: 1; min-width: 120px;">
                                <label style="display: block; font-size: 13px; color: #64748b; margin-bottom: 5px;">Prioridade</label>
                                <select id="nova_prio" style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px;">
                                    <option value="Baixa">Baixa</option>
                                    <option value="Normal" selected>Normal</option>
                                    <option value="Alta">Alta</option>
                                </select>
                            </div>

                            <?php
                            // Abre ligação rápida à BD para preencher os dropdowns
                            $db_form = new SQLite3(__DIR__ . '/bd/FarmOS.db');
                            
                            // Buscar Utilizadores
                            $users = [];
                            $res_users = $db_form->query("SELECT USR_id, USR_nome FROM tblUser ORDER BY USR_nome");
                            while ($u = $res_users->fetchArray(SQLITE3_ASSOC)) { $users[] = $u; }

                            // Buscar Zonas (com proteção caso a tabela ainda não exista)
                            $zonas = [];
                            try {
                                $res_zonas = $db_form->query("SELECT ZON_id, ZON_nome FROM tblZona ORDER BY ZON_nome");
                                if ($res_zonas) {
                                    while ($z = $res_zonas->fetchArray(SQLITE3_ASSOC)) { $zonas[] = $z; }
                                }
                            } catch (Exception $e) {
                                // Fallback temporário se não tiveres tblZona criada
                                $zonas = [['ZON_id' => 1, 'ZON_nome' => 'Estufa 1']];
                            }
                            $db_form->close();
                            ?>

                            <div style="flex-grow: 1; min-width: 150px;">
                                <label style="display: block; font-size: 13px; color: #64748b; margin-bottom: 5px;">Responsável</label>
                                <select id="nova_resp" required style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px;">
                                    <option value="" disabled selected>Selecione um utilizador...</option>
                                    <?php foreach ($users as $u): ?>
                                        <option value="<?php echo $u['USR_id']; ?>"><?php echo htmlspecialchars($u['USR_nome']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div style="flex-grow: 1; min-width: 150px;">
                                <label style="display: block; font-size: 13px; color: #64748b; margin-bottom: 5px;">Zona</label>
                                <select id="nova_zona" required style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px;">
                                    <option value="" disabled selected>Selecione a zona...</option>
                                    <?php foreach ($zonas as $z): ?>
                                        <option value="<?php echo $z['ZON_id']; ?>"><?php echo htmlspecialchars($z['ZON_nome']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <button type="submit" class="btn" style="background: #10b981; color: white; padding: 9px 20px; border:none; border-radius: 4px; cursor: pointer; font-weight: bold;">
                                Gravar
                            </button>
                        </form>
                    </div>

                    <h2 class="page-title" style="margin-bottom: 20px;">Tarefas Pendentes</h2>

                    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px;">
                        
                        <?php
                        try {
                            $db = new SQLite3(__DIR__ . '/bd/FarmOS.db');
                            
                            // Vai buscar as tarefas pendentes, cruzando com o Nome do Utilizador
                            $query = "
                                SELECT T.*, U.USR_nome 
                                FROM tblTarefa T
                                LEFT JOIN tblUser U ON T.TAR_responsavel_id = U.USR_id
                                WHERE T.TAR_estado != 'Concluída'
                                ORDER BY 
                                    CASE T.TAR_prioridade 
                                        WHEN 'Alta' THEN 1 
                                        WHEN 'Normal' THEN 2 
                                        WHEN 'Baixa' THEN 3 
                                        ELSE 4 
                                    END, 
                                    T.TAR_data_criacao DESC
                            ";
                            
                            $resultados = $db->query($query);
                            $temTarefas = false;
                            
                            while ($row = $resultados->fetchArray(SQLITE3_ASSOC)) {
                                $temTarefas = true;
                                $id = $row['TAR_id'];
                                $descricao = htmlspecialchars($row['TAR_descricao']);
                                $prioridade = $row['TAR_prioridade'];
                                $zonaId = $row['TAR_zona_id']; // Se tiveres tblZona, fazemos o JOIN depois!
                                $responsavel = !empty($row['USR_nome']) ? htmlspecialchars($row['USR_nome']) : 'ID: ' . $row['TAR_responsavel_id'];
                                
                                // Definir cores com base na prioridade
                                $corHex = "#3b82f6"; // Default Blue (Normal)
                                $corFundo = "#dbeafe";
                                if ($prioridade === 'Alta') { $corHex = "#ef4444"; $corFundo = "#fee2e2"; }
                                if ($prioridade === 'Baixa') { $corHex = "#64748b"; $corFundo = "#f1f5f9"; }
                                
                                echo "
                                <div class='light-card' style='border-left: 4px solid {$corHex}; position: relative;' id='tarefa_{$id}'>
                                    <div style='position: absolute; top: 15px; right: 15px;'>
                                        <span style='background: {$corFundo}; color: {$corHex}; padding: 3px 8px; border-radius: 12px; font-size: 12px; font-weight: bold;'>{$prioridade}</span>
                                    </div>
                                    <h3 style='margin-bottom: 10px; padding-right: 40px;'>{$descricao}</h3>
                                    <p style='font-size: 14px; color: #64748b; margin-bottom: 15px;'>
                                        <i class='fa-solid fa-location-dot'></i> Zona ID: {$zonaId}
                                    </p>
                                    <div style='display: flex; justify-content: space-between; align-items: center; border-top: 1px solid #e2e8f0; padding-top: 15px;'>
                                        <span style='font-size: 13px; color: #64748b;'><i class='fa-solid fa-user'></i> A: {$responsavel}</span>
                                        
                                        <button onclick='concluirTarefa({$id})' class='btn' style='background: #f1f5f9; color: #334155; padding: 5px 10px; border:none; border-radius: 4px; cursor: pointer; transition: 0.2s;' onmouseover=\"this.style.background='#10b981'; this.style.color='white';\" onmouseout=\"this.style.background='#f1f5f9'; this.style.color='#334155';\">
                                            <i class='fa-solid fa-check'></i> Concluir
                                        </button>
                                    </div>
                                </div>";
                            }
                            
                            if (!$temTarefas) {
                                echo "<div style='grid-column: 1 / -1; color: #64748b;'>Sem tarefas pendentes no momento. Fantástico!</div>";
                            }
                            
                            $db->close();
                        } catch (Exception $e) {
                            echo "Erro: " . $e->getMessage();
                        }
                        ?>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script>
        // Lógica do Menu (Original)
        document.getElementById('menuToggle').addEventListener('click', function() {
            document.getElementById('sidebar').classList.add('open');
            document.getElementById('sidebarOverlay').classList.add('active');
        });
        document.getElementById('sidebarOverlay').addEventListener('click', function() {
            document.getElementById('sidebar').classList.remove('open');
            this.classList.remove('active');
        });

        // -------------------------------------------------------------
        // COMUNICAÇÃO COM O BACKEND (API)
        // -------------------------------------------------------------
        
        // 1. Mudar o Estado para "Concluída"
        function concluirTarefa(idTarefa) {
            fetch('scripts/api_tarefas.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ acao: 'concluir', id: idTarefa })
            })
            .then(response => response.json())
            .then(data => {
                if(data.sucesso) {
                    // Esconde o cartão visualmente de imediato com uma animação
                    let card = document.getElementById('tarefa_' + idTarefa);
                    card.style.opacity = '0';
                    setTimeout(() => { card.remove(); }, 300);
                } else {
                    alert("Erro: " + data.mensagem);
                }
            });
        }

        // 2. Adicionar Nova Tarefa
        document.getElementById('formNovaTarefa').addEventListener('submit', function(e) {
            e.preventDefault(); // Impede a página de recarregar
            
            let dados = {
                acao: 'criar',
                descricao: document.getElementById('nova_desc').value,
                prioridade: document.getElementById('nova_prio').value,
                responsavel_id: document.getElementById('nova_resp').value,
                zona_id: document.getElementById('nova_zona').value
            };

            fetch('scripts/api_tarefas.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(dados)
            })
            .then(response => response.json())
            .then(data => {
                if(data.sucesso) {
                    // Recarrega a página para atualizar a grelha de cartões
                    window.location.reload(); 
                } else {
                    alert("Erro ao criar: " + data.mensagem);
                }
            });
        });
    </script>
</body>
</html>