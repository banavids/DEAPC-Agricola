/* ========================================================================== */
/* FarmSmart OS - Guião W3 (Validações e DOM)                                 */
/* ========================================================================== */

document.addEventListener('DOMContentLoaded', function() {
    
    // -------------------------------------------------------------------------
    // 1. Apresentar/Ocultar Informação do Projeto (Exercício 2.b)
    // -------------------------------------------------------------------------
    const btnSobre = document.getElementById('btnSobreProjeto');
    const infoProjeto = document.getElementById('infoProjeto');

    if (btnSobre && infoProjeto) {
        btnSobre.addEventListener('click', function() {
            if (infoProjeto.style.display === 'none') {
                infoProjeto.style.display = 'block';
                btnSobre.innerHTML = '<i class="fa-solid fa-chevron-up"></i> Ocultar Info';
            } else {
                infoProjeto.style.display = 'none';
                btnSobre.innerHTML = '<i class="fa-solid fa-circle-info"></i> Sobre o Projeto';
            }
        });
    }

    // -------------------------------------------------------------------------
    // 2. Validação Visual de Formulários (Exercício 1.b)
    // -------------------------------------------------------------------------
    const forms = document.querySelectorAll('form');

    forms.forEach(form => {
        // Desativa os balões nativos do browser para usarmos a nossa validação
        form.setAttribute('novalidate', true);

        form.addEventListener('submit', function(e) {
            let formValido = true;
            
            // Limpa formatações de erro que tenham ficado de tentativas anteriores
            form.querySelectorAll('.msg-erro-validacao').forEach(msg => msg.remove());
            form.querySelectorAll('.input-erro').forEach(input => input.classList.remove('input-erro'));

            // Apanha todos os inputs e selects que tenham a tag 'required' no HTML
            const camposObrigatorios = form.querySelectorAll('[required]');
            
            camposObrigatorios.forEach(campo => {
                if (!campo.value.trim()) {
                    formValido = false;
                    
                    // Altera a forma do objeto com a classe CSS que vamos criar
                    campo.classList.add('input-erro');
                    
                    // Cria e insere a mensagem de ajuda abaixo do campo
                    const msgErro = document.createElement('span');
                    msgErro.classList.add('msg-erro-validacao');
                    msgErro.innerHTML = '<i class="fa-solid fa-triangle-exclamation"></i> Este campo é de preenchimento obrigatório.';
                    campo.parentNode.insertBefore(msgErro, campo.nextSibling);
                }
            });

            // Se a validação falhar, impede a página de enviar os dados e recarregar
            if (!formValido) {
                e.preventDefault();
            }
        });
    });
});