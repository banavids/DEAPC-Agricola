document.addEventListener('DOMContentLoaded', function() {
    // 1. Lógica do Menu Mobile
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

    // 2. Lógica do Modal de Nova Produção
    const modal = document.getElementById('modalNovaProducao');
    const btnNova = document.getElementById('btnNovaProducao');
    const btnFechar = document.getElementById('btnFecharModal');
    const btnCancelar = document.getElementById('btnCancelarModal');
    const overlay = document.getElementById('modalOverlay');

    const openModal = () => { if(modal) modal.style.display = 'flex'; };
    const closeModal = () => { if(modal) modal.style.display = 'none'; };

    if(btnNova) btnNova.addEventListener('click', openModal);
    if(btnFechar) btnFechar.addEventListener('click', closeModal);
    if(btnCancelar) btnCancelar.addEventListener('click', closeModal);
    if(overlay) overlay.addEventListener('click', closeModal);

    // 3. Validação Visual Obrigatória (Guião W3)
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.setAttribute('novalidate', true); // Desativa validação nativa do browser
        
        form.addEventListener('submit', function(e) {
            let formValido = true;
            
            // Limpa erros antigos
            form.querySelectorAll('.msg-erro-validacao').forEach(msg => msg.remove());
            form.querySelectorAll('.input-erro').forEach(input => input.classList.remove('input-erro'));

            // Verifica campos obrigatórios
            const camposObrigatorios = form.querySelectorAll('[required]');
            camposObrigatorios.forEach(campo => {
                if (!campo.value.trim()) {
                    formValido = false;
                    
                    // Destaca o input com erro
                    campo.classList.add('input-erro');
                    
                    // Insere a mensagem
                    const msgErro = document.createElement('span');
                    msgErro.classList.add('msg-erro-validacao');
                    msgErro.innerHTML = '<i class="fa-solid fa-triangle-exclamation"></i> Este campo é obrigatório.';
                    campo.parentNode.insertBefore(msgErro, campo.nextSibling);
                }
            });

            // Impede o envio para o PHP se faltarem dados
            if (!formValido) {
                e.preventDefault();
            }
        });
    });
});