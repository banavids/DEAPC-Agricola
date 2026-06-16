document.addEventListener('DOMContentLoaded', function() {
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
});

// --------------------------------------------------------
// LÓGICA DINÂMICA DOS ATUADORES
// --------------------------------------------------------
function toggleAtuador(btnElement) {
    const tipo = btnElement.getAttribute('data-tipo');
    const estadoAtual = btnElement.getAttribute('data-estado'); // Será SEMPRE 'ligado' ou 'desligado'
    const textoOriginal = btnElement.innerHTML;
    let comandoDesejado = "";

    // 1. Determinar o comando MQTT com base num estado universal
    if (tipo === 'rega') {
        comandoDesejado = (estadoAtual === 'desligado') ? 'LIGAR_REGA' : 'DESLIGAR_REGA';
    } else if (tipo === 'porta') {
        comandoDesejado = (estadoAtual === 'desligado') ? 'ABRIR' : 'FECHAR';
    }

    // 2. Feedback visual
    btnElement.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> A enviar...';
    btnElement.disabled = true;

    // 3. Comunicação
    fetch('scripts/enviar_comando.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        credentials: 'same-origin',
        body: JSON.stringify({ comando: comandoDesejado })
    })
    .then(response => response.json())
    .then(data => {
        btnElement.disabled = false;
        if(data.sucesso) {
            atualizarUIAfterSuccess(btnElement, tipo, estadoAtual);
        } else {
            alert("❌ Erro: " + data.mensagem);
            btnElement.innerHTML = textoOriginal;
        }
    })
    .catch(error => {
        console.error('Erro de rede:', error);
        alert("❌ Erro crítico de comunicação com o servidor.");
        btnElement.disabled = false;
        btnElement.innerHTML = textoOriginal;
    });
}

// 4. Tratamento Universal da UI
function atualizarUIAfterSuccess(btnElement, tipo, estadoAntigo) {
    const iconElement = document.getElementById('icon-' + tipo);

    if (estadoAntigo === 'desligado') {
        // MUDAR PARA LIGADO (Ativo)
        btnElement.setAttribute('data-estado', 'ligado');
        btnElement.className = 'btn-toggle btn-on';
        
        if (tipo === 'rega') {
            btnElement.innerHTML = '<i class="fa-solid fa-power-off"></i> Desligar Rega';
            iconElement.className = 'fa-solid fa-droplet icon-on';
        } else if (tipo === 'porta') {
            btnElement.innerHTML = '<i class="fa-solid fa-door-closed"></i> Fechar Portas';
            iconElement.className = 'fa-solid fa-door-open icon-on';
        }
    } else {
        // MUDAR PARA DESLIGADO (Inativo)
        btnElement.setAttribute('data-estado', 'desligado');
        btnElement.className = 'btn-toggle btn-off';
        
        if (tipo === 'rega') {
            btnElement.innerHTML = '<i class="fa-solid fa-power-off"></i> Ligar Rega';
            iconElement.className = 'fa-solid fa-droplet icon-off';
        } else if (tipo === 'porta') {
            btnElement.innerHTML = '<i class="fa-solid fa-door-open"></i> Abrir Portas';
            iconElement.className = 'fa-solid fa-door-closed icon-off';
        }
    }
}