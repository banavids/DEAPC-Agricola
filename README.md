# DEAPC-Agricola
> **Projeto DEAPC - P43 - Agrícola**  
> Sistema de Monitorização, Automação de Rega e Gestão de Produções Agrícolas Baseado em IoT.

---

## 1. Introdução e Objetivos

O projeto **DEAPC-Agricola** é uma plataforma de gestão e automação agrícola que integra tecnologias IoT (Internet of Things) para otimizar o consumo de recursos, monitorizar produções em tempo real e mitigar falhas no terreno.

### Objetivos Principais
* **Telemetria em Tempo Real:** Receber e processar continuamente informações das condições atmosféricas e dados enviados por sensores de campo.
* **Automação Inteligente:** Automatizar o sistema de rega de uma produção com base em regras de negócio e limites (*thresholds*) de humidade predefinidos.
* **Gestão Operacional:** Disponibilizar interfaces adaptadas para os diferentes níveis de responsabilidade e perfis de utilizador no terreno.

---

## 2. Perfis de Utilizador e Matriz de Permissões

O sistema divide-se em três perfis de acesso, garantindo a segurança da plataforma e a estrita confidencialidade dos dados estratégicos da exploração agrícola.

| Perfil                    | Descrição Geral                                   | Escopo de Acesso |

| **Administrador (Admin)** | Gestão técnica da plataforma e infraestrutura.    | Acesso total e irrestrito a todos os módulos e configurações. |
| **Gestor Agrícola**       | Planeamento, análise de dados e coordenação.      | Visão global da quinta, relatórios, gestão de sensores e atuadores. |
| **Operário Agrícola**     | Execução de tarefas práticas no campo.            | Restrito à produção e às tarefas que lhe foram explicitamente atribuídas. |

---

## 3. Especificação de Requisitos (User Stories)

### Módulo 1: Administração do Sistema (Admin)
* **US1.1 [Acesso Total]:** Como Administrador, quero ter acesso irrestrito a todos os módulos e funcionalidades do sistema, para que possa gerir a plataforma e resolver qualquer problema de configuração.
* **US1.2 [Gestão de Utilizadores]:** Como Administrador, quero poder criar, editar, suspender e apagar contas de Gestores Agrícolas e Operários, para garantir que apenas pessoas autorizadas utilizam o software com as permissões corretas.
* **US1.3 [Auditoria do Sistema]:** Como Administrador, quero visualizar um registo (*log*) de atividades de todos os utilizadores, para que possa monitorizar a segurança do sistema e identificar quem fez alterações importantes.

### Módulo 2: Gestão de Produções (Gestor Agrícola)
* **US2.1 [Visão Global]:** Como Gestor Agrícola, quero visualizar uma lista ou painel (*dashboard*) com todas as produções agrícolas ativas na quinta, para que consiga monitorizar o estado geral das culturas.
* **US2.2 [Gestão do Ciclo de Produção]:** Como Gestor Agrícola, quero criar, editar e finalizar produções agrícolas, para que o sistema reflita o planeamento real do campo.
* **US2.3 [Alocação de Equipas]:** Como Gestor Agrícola, quero atribuir Operários Agrícolas a produções específicas, para que cada trabalhador saiba onde deve focar o seu esforço.
* **US2.4 [Acesso a Relatórios]:** Como Gestor Agrícola, quero extrair dados consolidados de todas as produções (custos, colheitas, tempos), para que possa tomar decisões estratégicas para a próxima época.

### Módulo 3: Operações de Campo (Operário Agrícola)
* **US3.1 [Acesso Restrito à sua Produção]:** Como Operário Agrícola, quero visualizar os detalhes e tarefas apenas da produção à qual fui atribuído, para que saiba exatamente o que fazer sem ser sobrecarregado com informação irrelevante de outros campos.
* **US3.2 [Limitação de Confidencialidade]:** Como Operário Agrícola, quero ser bloqueado de aceder a dados financeiros ou ao painel geral de gestão, para que a confidencialidade dos dados estratégicos da quinta seja mantida.
* **US3.3 [Registo de Progresso]:** Como Operário Agrícola, quero poder marcar as minhas tarefas como concluídas ou inserir notas simples (ex: "aplicado adubo") na minha produção atribuída, para que o Gestor Agrícola saiba o progresso no campo em tempo real.

### Módulo 4: IoT, Sensores e Alertas
* **US4.1 [Alertas Automáticos]:** Como Gestor Agrícola, quero receber alertas quando a humidade do solo estiver abaixo do nível recomendado, para que possa agir rapidamente e evitar danos na cultura.
* **US4.2 [Monitorização em Tempo Real]:** Como Gestor Agrícola, quero visualizar dados dos sensores em tempo real, para que acompanhe as condições da produção continuamente.
* **US7.1 [Histórico de Sensores]:** Como Gestor Agrícola, quero consultar o histórico dos sensores, para analisar padrões climáticos e produtividade.

### Módulo 5: Automação de Irrigação
* **US5.1 [Rega Automática]:** Como Gestor Agrícola, quero que o sistema ative automaticamente a irrigação quando a humidade estiver baixa, para reduzir a intervenção manual.
* **US5.2 [Programação de Rega]:** Como Gestor Agrícola, quero definir horários automáticos para irrigação, para otimizar o consumo de água.

### Módulo 6: Segurança e Transversalidade
* **US6.1 [Autenticação Segura]:** Como Utilizador, quero iniciar sessão com credenciais seguras, para proteger os dados da plataforma.

---

## 4. Arquitetura de Informação e Interfaces (Wireframes)

A aplicação está estruturada em 14 ecrãs/componentes lógicos, desenhados para responder de forma modular aos requisitos do projeto.

### 4.1 Autenticação e Entrada
* **1. Página de Login:** Formulário de autenticação (email/password), recuperação de palavra-passe e indicadores de estado técnicos (estado do servidor, API e Broker MQTT).

### 4.2 Dashboards Customizados
* **2. Dashboard Principal:** A homepage adapta-se dinamicamente ao perfil que faz login:
  * **Admin Dashboard:** Focado na saúde do sistema. Apresenta cartões de utilizadores ativos, sensores online, alertas críticos e gráficos de atividade/logs.
  * **Gestor Dashboard:** Focado na operação. Apresenta cartões de culturas ativas, regas a decorrer, tarefas pendentes, últimas leituras e botões de ação rápida (iniciar rega, desligar bomba).
  * **Operário Dashboard:** Focado na execução. Apresenta apenas as tarefas atribuídas, estado da produção atual e notas recentes.

### 4.3 Gestão de Produções e Trabalho
* **3. Página de Produções Agrícolas:** Lista de todas as produções (cultura, estado, humidade, temperatura, operários associados). Contém uma **Vista Detalhada** dividida em secções: Informações Gerais, Dados de Sensores, Histórico de Irrigação, Operários Alocados e Notas de Campo (fertilização, observações).
* **9. Página de Tarefas:** Listagem de ordens de trabalho para os operários com triagem por prioridade, estado e ações rápidas para concluir ou adicionar notas.
* **10. Página de Utilizadores (Admin Only):** Interface CRUD para gestão de utilizadores (criar, editar, suspender, apagar).

### 4.4 Infraestrutura IoT e Telemetria
* **4. Página de Sensores:** Listagem do hardware (tipo, estado online/offline, último valor recebido) e ecrã de detalhe com gráficos de médias e histórico.
* **5. Página de Atuadores:** Controlo manual e automático de relés (bomba de água, fertilização, ventilação simulada) acompanhado por um histórico de acionamentos (*ex: 14:32 bomba ligada*).
* **6. Página de Monitorização Live:** Painel dinâmico em tempo real enriquecido com *widgets* de ponteiro (*gauges* para humidade/temperatura) e gráficos de séries temporais.
* **7. Página MQTT / IoT:** Consola de diagnóstico que exibe o estado do *broker*, os dispositivos ligados, os tópicos ativos e o fluxo das últimas mensagens (*ex: farm/sensor/humidade -> 22%*).

### 4.5 Dados, Logs e Configurações
* **8. Página de Relatórios:** Módulo de inteligência de dados para exportação de dados consolidados (produção, consumo de água diário/mensal, alertas) em formatos estruturados (PDF/CSV).
* **11. Página de Logs / Auditoria (Admin Only):** Histórico detalhado e imutável de ações executadas na plataforma com filtros por utilizador, data e ação.
* **12. Página de Configurações:** Parametrização global do sistema (IP do Broker MQTT, thresholds de humidade mínima, agendamentos cronometrados de rega e alertas de email).
* **13. Página de Alertas:** Central visual de notificações críticas (ex: *Sensor offline*, *Humidade crítica*), organizadas por severidade e origem.
* **14. Página Histórico:** Motor de busca retroativo que unifica os dados passados de sensores, regas, fertilizações e tarefas num único local com filtros avançados.

---

## 5. Arquitetura Técnica

* **Frontend:** Interface Web Responsivo com gráficos dinâmicos integrados.
* **Backend:** Php.
* **Mensajaria/IoT:** Protocolo MQTT com Broker Eclipse Mosquitto.
* **Base de Dados:** Relacional (MySQL) para entidades e dados operacionais.