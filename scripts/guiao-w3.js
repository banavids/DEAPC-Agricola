/* ========================================================================== */
/* FarmSmart OS - JavaScript Global           */
/* ========================================================================== */

document.addEventListener('DOMContentLoaded', function() {

    // -------------------------------------------------------------------------
    // 1. MENU LATERAL (SIDEBAR)
    // -------------------------------------------------------------------------
    const menuToggle = document.getElementById('menuToggle');
    const sidebar = document.getElementById('sidebar');
    const sidebarOverlay = document.getElementById('sidebarOverlay');

    if (menuToggle && sidebar && sidebarOverlay) {
        menuToggle.addEventListener('click', function() {
            sidebar.classList.add('open');
            sidebarOverlay.classList.add('active');
        });

        sidebarOverlay.addEventListener('click', function() {
            sidebar.classList.remove('open');
            this.classList.remove('active');
        });
    }

    // -------------------------------------------------------------------------
    // INFORMAÇÃO DO PROJETO (Apresentar/Ocultar)
    // -------------------------------------------------------------------------
    const btnSobre = document.getElementById('btnSobreProjeto');
    const infoProjeto = document.getElementById('infoProjeto');

    if (btnSobre && infoProjeto) {
        btnSobre.addEventListener('click', function() {
            if (infoProjeto.style.display === 'none' || infoProjeto.style.display === '') {
                infoProjeto.style.display = 'block';
                btnSobre.innerHTML = '<i class="fa-solid fa-chevron-up"></i> Ocultar Info';
            } else {
                infoProjeto.style.display = 'none';
                btnSobre.innerHTML = '<i class="fa-solid fa-circle-info"></i> Sobre o Projeto';
            }
        });
    }

    // -------------------------------------------------------------------------
    // VALIDAÇÃO VISUAL DE FORMULÁRIOS
    // -------------------------------------------------------------------------
    const forms = document.querySelectorAll('form');

    forms.forEach(form => {
        // Ignora o form de login caso tenha lógica própria noutro ficheiro
        if(form.id === 'loginForm') return; 

        // Desativa balões nativos do browser
        form.setAttribute('novalidate', true);

        form.addEventListener('submit', function(e) {
            let formValido = true;
            
            // Limpa erros anteriores
            form.querySelectorAll('.msg-erro-validacao').forEach(msg => msg.remove());
            form.querySelectorAll('.input-erro').forEach(input => input.classList.remove('input-erro'));

            // Valida campos obrigatórios
            const camposObrigatorios = form.querySelectorAll('[required]');
            
            camposObrigatorios.forEach(campo => {
                if (!campo.value.trim()) {
                    formValido = false;
                    campo.classList.add('input-erro');
                    
                    const msgErro = document.createElement('span');
                    msgErro.classList.add('msg-erro-validacao');
                    msgErro.innerHTML = '<i class="fa-solid fa-triangle-exclamation"></i> Este campo é de preenchimento obrigatório.';
                    campo.parentNode.insertBefore(msgErro, campo.nextSibling);
                }
            });

            // Se o formulário não for válido, bloqueia o envio completamente
            if (!formValido) {
                e.preventDefault();
                e.stopImmediatePropagation(); // Evita que o fetch das tarefas (abaixo) seja disparado com erros
            }
        });
    });

    // -------------------------------------------------------------------------
    // LÓGICA DE TAREFAS (Submissão Assíncrona via Fetch API)
    // -------------------------------------------------------------------------
    const formNovaTarefa = document.getElementById('formNovaTarefa');
    if (formNovaTarefa) {
        formNovaTarefa.addEventListener('submit', function(e) {
            // Impedimos o reload padrão. A validação do Passo 3 já verificou se está vazio.
            e.preventDefault();

            let dados = {
                acao: 'criar',
                descricao: document.getElementById('nova_desc').value,
                prioridade: document.getElementById('nova_prio').value,
                responsavel_id: document.getElementById('nova_resp').value,
                zona_id: document.getElementById('nova_zona').value
            };

            fetch('scripts/api-tarefas.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                credentials: 'same-origin',
                body: JSON.stringify(dados)
            })
            .then(response => response.json())
            .then(data => {
                if(data.sucesso) window.location.reload(); 
                else alert("Erro ao criar: " + data.mensagem);
            })
            .catch(error => alert("Falha na comunicação com api-tarefas.php."));
        });
    }
});

// =============================================================================
// FUNÇÕES GLOBAIS (Disponíveis em todas as páginas, para ações comuns como concluir tarefas, enviar comandos MQTT, e gerir utilizadores)
// =============================================================================

// TAREFAS: Concluir
window.concluirTarefa = function(idTarefa) {
    fetch('scripts/api-tarefas.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ acao: 'concluir', id: idTarefa })
    })
    .then(response => response.json())
    .then(data => {
        if(data.sucesso) {
            let card = document.getElementById('tarefa_' + idTarefa);
            if(card) {
                card.style.transition = "opacity 0.3s ease";
                card.style.opacity = '0';
                setTimeout(() => card.remove(), 300);
            }
        } else {
            alert("Erro: " + data.mensagem);
        }
    });
};

// ATUADORES: Enviar Comando MQTT
window.enviarComandoMQTT = function(comandoDesejado) {
    fetch('scripts/enviar_comando.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        credentials: 'same-origin',
        body: JSON.stringify({ comando: comandoDesejado })
    })
    .then(response => response.json())
    .then(data => {
        if(data.sucesso) alert("📡 Sucesso: Comando '" + comandoDesejado + "' enviado para o Raspberry Pi!");
        else alert("❌ Erro: " + data.mensagem);
    })
    .catch(error => alert("❌ Erro crítico de comunicação com o servidor."));
};

// UTILIZADORES: Fechar Modais
window.fecharModais = function() { 
    const modalCreate = document.getElementById('modalUser');
    const modalEdit = document.getElementById('modalEditUser');
    if(modalCreate) modalCreate.style.display = 'none'; 
    if(modalEdit) modalEdit.style.display = 'none'; 
};

// UTILIZADORES: Abrir Modal Edição
window.abrirModalEdicao = function(id, nome, email, grupo, estado) {
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
    
    const modalEdit = document.getElementById('modalEditUser');
    if(modalEdit) modalEdit.style.display = 'flex';
};

// UTILIZADORES: Mudar Estado Suspenso/Ativo
window.mudarEstadoUser = function(idUser, estadoAtual) {
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
};