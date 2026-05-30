/* ========================================================================== */
/* FarmSmart OS - Produções Script */
/* ========================================================================== */

// Dados simulados de produções
let producoes = [
    {
        id: 1,
        nome: 'Estufa 1',
        cultura: 'Tomates',
        localizacao: 'Setor Estufa',
        estado: 'ativo',
        humidade: 65,
        temperatura: 24,
        dataPlantio: '2026-04-15',
        area: 250,
        quantidade: 1000,
        responsavel: 'João Silva',
        operarios: 3,
        observacoes: 'Crescimento normal, sem problemas detectados'
    },
    {
        id: 2,
        nome: 'Setor Sul',
        cultura: 'Alfaces',
        localizacao: 'Setor Sul',
        estado: 'ativo',
        humidade: 72,
        temperatura: 21,
        dataPlantio: '2026-05-01',
        area: 180,
        quantidade: 2000,
        responsavel: 'Maria Costa',
        operarios: 2,
        observacoes: 'Rega automática funcionando corretamente'
    },
    {
        id: 3,
        nome: 'Pomar',
        cultura: 'Maçãs',
        localizacao: 'Pomar Leste',
        estado: 'atraso',
        humidade: 45,
        temperatura: 26,
        dataPlantio: '2025-03-20',
        area: 500,
        quantidade: 800,
        responsavel: 'Carlos Teixeira',
        operarios: 4,
        observacoes: 'Fertilizante necessário na próxima semana'
    }
];

/* ========================================================================== */
/* Menu Toggle */
/* ========================================================================== */

const menuToggle = document.getElementById('menuToggle');
const sidebar = document.querySelector('.sidebar');

if (menuToggle) {
    menuToggle.addEventListener('click', function() {
        sidebar.classList.toggle('active');
    });

    document.addEventListener('click', function(e) {
        if (!sidebar.contains(e.target) && !menuToggle.contains(e.target)) {
            sidebar.classList.remove('active');
        }
    });
}

/* ========================================================================== */
/* Modal - Nova Produção */
/* ========================================================================== */

const modalNovaProducao = document.getElementById('modalNovaProducao');
const btnNovaProducao = document.getElementById('btnNovaProducao');
const btnFecharModal = document.getElementById('btnFecharModal');
const btnCancelarModal = document.getElementById('btnCancelarModal');
const formNovaProducao = document.getElementById('formNovaProducao');
const modalOverlay = document.getElementById('modalOverlay');

// Abrir Modal
if (btnNovaProducao) {
    btnNovaProducao.addEventListener('click', function() {
        modalNovaProducao.classList.add('active');
        document.body.style.overflow = 'hidden';
    });
}

// Fechar Modal
function fecharModal() {
    modalNovaProducao.classList.remove('active');
    document.body.style.overflow = 'auto';
    formNovaProducao.reset();
}

if (btnFecharModal) {
    btnFecharModal.addEventListener('click', fecharModal);
}

if (btnCancelarModal) {
    btnCancelarModal.addEventListener('click', fecharModal);
}

// Fechar ao clicar no overlay
if (modalOverlay) {
    modalOverlay.addEventListener('click', fecharModal);
}

// Submeter Formulário
if (formNovaProducao) {
    formNovaProducao.addEventListener('submit', function(e) {
        e.preventDefault();

        // Recuperar dados do formulário
        const formData = new FormData(this);
        const novaProducao = {
            id: producoes.length + 1,
            nome: formData.get('nome'),
            cultura: formData.get('cultura'),
            localizacao: formData.get('localizacao'),
            estado: formData.get('estado'),
            dataPlantio: formData.get('dataPlantio'),
            area: parseFloat(formData.get('area')),
            quantidade: parseInt(formData.get('quantidade')),
            responsavel: formData.get('responsavel'),
            operarios: 1,
            humidade: 0,
            temperatura: 0,
            observacoes: formData.get('observacoes')
        };

        // Adicionar à lista
        producoes.push(novaProducao);

        // Atualizar tabela
        renderizarProducoes();

        // Fechar modal
        fecharModal();

        // Mostrar mensagem de sucesso
        mostrarNotificacao('Produção criada com sucesso!', 'sucesso');
    });
}

/* ========================================================================== */
/* Renderizar Produções */
/* ========================================================================== */

function renderizarProducoes() {
    const producoesList = document.getElementById('producoesList');
    const emptyState = document.getElementById('emptyState');

    // Limpar lista
    producoesList.innerHTML = '';

    if (producoes.length === 0) {
        emptyState.style.display = 'block';
        return;
    }

    emptyState.style.display = 'none';

    producoes.forEach(producao => {
        const row = document.createElement('tr');

        // Determinar classe de humidade
        const classeHumidade = producao.humidade >= 60 ? 'value-positive' : 
                              producao.humidade >= 40 ? 'value-positive' : 
                              'value-negative';

        row.innerHTML = `
            <td>
                <div class="producao-nome">
                    <span class="producao-nome-main">${producao.nome}</span>
                    <span class="producao-cultura">${producao.cultura}</span>
                </div>
            </td>
            <td>
                <span class="status-badge ${producao.estado}">
                    <span class="status-dot"></span>
                    ${capitalize(producao.estado)}
                </span>
            </td>
            <td>
                <div class="humidade-temp">
                    <div class="humidade-item">
                        <i class="fas fa-droplet"></i>
                        <span class="${classeHumidade}">${producao.humidade}%</span>
                    </div>
                    <div class="temp-item">
                        <i class="fas fa-thermometer-half"></i>
                        <span>${producao.temperatura}°C</span>
                    </div>
                </div>
            </td>
            <td>
                <div class="operarios-count">
                    <i class="fas fa-users"></i>
                    ${producao.operarios}
                </div>
            </td>
            <td>
                <div class="acoes-cell">
                    <button class="btn-action" title="Ver detalhes" onclick="abrirDetalhes(${producao.id})">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button class="btn-action" title="Editar" onclick="editarProducao(${producao.id})">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn-action danger" title="Eliminar" onclick="eliminarProducao(${producao.id})">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </td>
        `;

        producoesList.appendChild(row);
    });
}

/* ========================================================================== */
/* Filtros */
/* ========================================================================== */

const searchInput = document.getElementById('searchInput');
const filterEstado = document.getElementById('filterEstado');
const filterCultura = document.getElementById('filterCultura');
const btnLimparFiltros = document.getElementById('btnLimparFiltros');

function filtrarProducoes() {
    const searchTerm = searchInput.value.toLowerCase();
    const estadoFiltro = filterEstado.value;
    const culturaFiltro = filterCultura.value;

    const producoesFiltradas = producoes.filter(producao => {
        const matchSearch = 
            producao.nome.toLowerCase().includes(searchTerm) ||
            producao.cultura.toLowerCase().includes(searchTerm) ||
            producao.localizacao.toLowerCase().includes(searchTerm);

        const matchEstado = !estadoFiltro || producao.estado === estadoFiltro;
        const matchCultura = !culturaFiltro || producao.cultura.toLowerCase() === culturaFiltro;

        return matchSearch && matchEstado && matchCultura;
    });

    // Atualizar lista
    const producoesList = document.getElementById('producoesList');
    producoesList.innerHTML = '';

    if (producoesFiltradas.length === 0) {
        document.getElementById('emptyState').style.display = 'block';
        return;
    }

    document.getElementById('emptyState').style.display = 'none';

    producoesFiltradas.forEach(producao => {
        const row = document.createElement('tr');

        const classeHumidade = producao.humidade >= 60 ? 'value-positive' : 
                              producao.humidade >= 40 ? 'value-positive' : 
                              'value-negative';

        row.innerHTML = `
            <td>
                <div class="producao-nome">
                    <span class="producao-nome-main">${producao.nome}</span>
                    <span class="producao-cultura">${producao.cultura}</span>
                </div>
            </td>
            <td>
                <span class="status-badge ${producao.estado}">
                    <span class="status-dot"></span>
                    ${capitalize(producao.estado)}
                </span>
            </td>
            <td>
                <div class="humidade-temp">
                    <div class="humidade-item">
                        <i class="fas fa-droplet"></i>
                        <span class="${classeHumidade}">${producao.humidade}%</span>
                    </div>
                    <div class="temp-item">
                        <i class="fas fa-thermometer-half"></i>
                        <span>${producao.temperatura}°C</span>
                    </div>
                </div>
            </td>
            <td>
                <div class="operarios-count">
                    <i class="fas fa-users"></i>
                    ${producao.operarios}
                </div>
            </td>
            <td>
                <div class="acoes-cell">
                    <button class="btn-action" title="Ver detalhes" onclick="abrirDetalhes(${producao.id})">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button class="btn-action" title="Editar" onclick="editarProducao(${producao.id})">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn-action danger" title="Eliminar" onclick="eliminarProducao(${producao.id})">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </td>
        `;

        producoesList.appendChild(row);
    });
}

if (searchInput) searchInput.addEventListener('input', filtrarProducoes);
if (filterEstado) filterEstado.addEventListener('change', filtrarProducoes);
if (filterCultura) filterCultura.addEventListener('change', filtrarProducoes);

// Limpar Filtros
if (btnLimparFiltros) {
    btnLimparFiltros.addEventListener('click', function() {
        searchInput.value = '';
        filterEstado.value = '';
        filterCultura.value = '';
        filtrarProducoes();
    });
}

/* ========================================================================== */
/* Detalhes da Produção */
/* ========================================================================== */

const modalDetalhesProducao = document.getElementById('modalDetalhesProducao');
const btnFecharDetalhes = document.getElementById('btnFecharDetalhes');

function abrirDetalhes(id) {
    const producao = producoes.find(p => p.id === id);
    if (!producao) return;

    // Preencher detalhes
    document.getElementById('detalhesNome').textContent = producao.nome;

    const dataPlantio = new Date(producao.dataPlantio);
    const diasDecorridos = Math.floor((Date.now() - dataPlantio) / (1000 * 60 * 60 * 24));

    const detalhesHTML = `
        <div class="detalhes-grid">
            <div class="detalhes-item">
                <span class="detalhes-label">Nome da Produção</span>
                <span class="detalhes-value">${producao.nome}</span>
            </div>
            <div class="detalhes-item">
                <span class="detalhes-label">Tipo de Cultura</span>
                <span class="detalhes-value">${producao.cultura}</span>
            </div>
            <div class="detalhes-item">
                <span class="detalhes-label">Localização</span>
                <span class="detalhes-value">${producao.localizacao}</span>
            </div>
            <div class="detalhes-item">
                <span class="detalhes-label">Estado</span>
                <span class="status-badge ${producao.estado}">
                    <span class="status-dot"></span>
                    ${capitalize(producao.estado)}
                </span>
            </div>
            <div class="detalhes-item">
                <span class="detalhes-label">Data de Plantio</span>
                <span class="detalhes-value">${dataPlantio.toLocaleDateString('pt-PT')}</span>
            </div>
            <div class="detalhes-item">
                <span class="detalhes-label">Dias Decorridos</span>
                <span class="detalhes-value">${diasDecorridos} dias</span>
            </div>
            <div class="detalhes-item">
                <span class="detalhes-label">Área</span>
                <span class="detalhes-value">${producao.area} m²</span>
            </div>
            <div class="detalhes-item">
                <span class="detalhes-label">Quantidade de Plantas</span>
                <span class="detalhes-value">${producao.quantidade.toLocaleString()}</span>
            </div>
            <div class="detalhes-item">
                <span class="detalhes-label">Responsável</span>
                <span class="detalhes-value">${producao.responsavel}</span>
            </div>
            <div class="detalhes-item">
                <span class="detalhes-label">Operários</span>
                <span class="detalhes-value">${producao.operarios}</span>
            </div>
        </div>

        <div class="detalhes-section-title">Condições Ambientais</div>
        <div class="detalhes-grid">
            <div class="detalhes-item">
                <span class="detalhes-label">Humidade</span>
                <span class="detalhes-value">${producao.humidade}%</span>
            </div>
            <div class="detalhes-item">
                <span class="detalhes-label">Temperatura</span>
                <span class="detalhes-value">${producao.temperatura}°C</span>
            </div>
        </div>

        <div class="detalhes-section-title">Observações</div>
        <p style="font-size: 13px; line-height: 1.6; color: var(--text-muted);">
            ${producao.observacoes || 'Sem observações adicionais'}
        </p>
    `;

    document.getElementById('detalhesContainer').innerHTML = detalhesHTML;
    modalDetalhesProducao.classList.add('active');
    document.body.style.overflow = 'hidden';
}

if (btnFecharDetalhes) {
    btnFecharDetalhes.addEventListener('click', function() {
        modalDetalhesProducao.classList.remove('active');
        document.body.style.overflow = 'auto';
    });
}

// Fechar detalhes ao clicar no overlay
document.querySelectorAll('.modal-overlay-2').forEach(overlay => {
    overlay.addEventListener('click', function() {
        modalDetalhesProducao.classList.remove('active');
        document.body.style.overflow = 'auto';
    });
});

/* ========================================================================== */
/* Ações */
/* ========================================================================== */

function editarProducao(id) {
    const producao = producoes.find(p => p.id === id);
    if (!producao) return;

    alert('Edição de produção: ' + producao.nome + '\n(Esta funcionalidade será implementada)');
}

function eliminarProducao(id) {
    if (confirm('Tem a certeza que deseja eliminar esta produção?')) {
        producoes = producoes.filter(p => p.id !== id);
        renderizarProducoes();
        mostrarNotificacao('Produção eliminada com sucesso!', 'aviso');
    }
}

/* ========================================================================== */
/* Funções Auxiliares */
/* ========================================================================== */

function capitalize(str) {
    return str.charAt(0).toUpperCase() + str.slice(1);
}

function mostrarNotificacao(mensagem, tipo = 'info') {
    // Criar notificação
    const notif = document.createElement('div');
    notif.style.cssText = `
        position: fixed;
        bottom: 20px;
        right: 20px;
        background: ${tipo === 'sucesso' ? '#10b981' : tipo === 'aviso' ? '#f59e0b' : '#3b82f6'};
        color: white;
        padding: 16px 24px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 600;
        z-index: 2000;
        animation: slideIn 0.3s ease-out;
    `;

    notif.textContent = mensagem;
    document.body.appendChild(notif);

    // Remover após 3 segundos
    setTimeout(() => {
        notif.style.animation = 'slideOut 0.3s ease-out';
        setTimeout(() => notif.remove(), 300);
    }, 3000);
}

// Adicionar animações ao CSS dinamicamente
if (!document.querySelector('style[data-notif]')) {
    const style = document.createElement('style');
    style.dataset.notif = 'true';
    style.textContent = `
        @keyframes slideIn {
            from { 
                opacity: 0;
                transform: translateX(100px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes slideOut {
            from {
                opacity: 1;
                transform: translateX(0);
            }
            to {
                opacity: 0;
                transform: translateX(100px);
            }
        }
    `;
    document.head.appendChild(style);
}

/* ========================================================================== */
/* Inicializar */
/* ========================================================================== */

document.addEventListener('DOMContentLoaded', function() {
    // Renderizar produções iniciais
    renderizarProducoes();

    console.log('FarmSmart OS - Produções Carregado');
    console.log('Total de produções:', producoes.length);
});
