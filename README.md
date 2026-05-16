# DEAPC-Agricola
Projeto DEAPC - P43 - Agrícola

1)

a) A aplicação tem como objetivo:

- Receber informação em tempo real das condições atmosféricas.
- Receber input de sensores para processamento de dados.
- Automatizar o sistema de rega de uma produção.

b) Perfis de Utilizador da aplicação:

- Admin 
- Gestor Agrícola 
- Operário Agrícola 

c) Funcionalidades para os diferentes utilizadores:

Admin - Todos os privilégios
Gestor Agrícola - Tem acesso a todas as produções
Operário Agrícola - Ver informação de uma determinada produção 

2)
Administração do Sistema (Admin)

    US1.1: Acesso Total

        Como Administrador,

        Quero ter acesso irrestrito a todos os módulos e funcionalidades do sistema,

        Para que possa gerir a plataforma e resolver qualquer problema de configuração.

    US1.2: Gestão de Utilizadores

        Como Administrador,

        Quero poder criar, editar, suspender e apagar contas de Gestores Agrícolas e Operários,

        Para que garanta que apenas pessoas autorizadas utilizam o software com as permissões corretas.

    US1.3: Auditoria do Sistema

        Como Administrador,

        Quero visualizar um registo (log) de atividades de todos os utilizadores,

        Para que possa monitorizar a segurança do sistema e identificar quem fez alterações importantes.

Gestão de Produções (Gestor Agrícola)

    US2.1: Visão Global

        Como Gestor Agrícola,

        Quero visualizar uma lista ou painel (dashboard) com todas as produções agrícolas ativas na quinta,

        Para que consiga monitorizar o estado geral das culturas.

    US2.2: Gestão do Ciclo de Produção

        Como Gestor Agrícola,

        Quero criar, editar e finalizar produções agrícolas,

        Para que o sistema reflita o planeamento real do campo.

    US2.3: Alocação de Equipas

        Como Gestor Agrícola,

        Quero atribuir Operários Agrícolas a produções específicas,

        Para que cada trabalhador saiba onde deve focar o seu esforço.

    US2.4: Acesso a Relatórios

        Como Gestor Agrícola,

        Quero extrair dados consolidados de todas as produções (custos, colheitas, tempos),

        Para que possa tomar decisões estratégicas para a próxima época.

Operações de Campo (Operário Agrícola)

    US3.1: Acesso Restrito à sua Produção

        Como Operário Agrícola,

        Quero visualizar os detalhes e tarefas apenas da produção à qual fui atribuído,

        Para que saiba exatamente o que fazer sem ser sobrecarregado com informação irrelevante de outros campos.

    US3.2: Limitação de Confidencialidade

        Como Operário Agrícola,

        Quero ser bloqueado de aceder a dados financeiros ou ao painel geral de gestão,

        Para que a confidencialidade dos dados estratégicos da quinta seja mantida.

    US3.3: Registo de Progresso (Opcional, mas recomendado)

        Como Operário Agrícola,

        Quero poder marcar as minhas tarefas como concluídas ou inserir notas simples (ex: "aplicado adubo") na minha produção atribuída,

        Para que o Gestor Agrícola saiba o progresso no campo em tempo real.