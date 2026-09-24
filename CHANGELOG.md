# CHANGELOG.md

Formato baseado em [Keep a Changelog](https://keepachangelog.com/). Versões do tema em `style.css`.

## [Unreleased]

### Added
- Portal: banner «Instale o app Método OCD» → instruções em https://app.saulocoelho.com/?install=1 («Ver como instalar»). Tema **1.3.47**.
- Portal: banner «Instale o app Método OCD» (substitui «Instale o Portal do Aluno») → https://app.saulocoelho.com/. Tema **1.3.46**.
- Issue [#25](https://github.com/esvianna/saulocoelho.com/issues/25) **Alumni na hub**: galeria «Fotos da turma» + upload pelos alunos; tema **1.3.45** mostra galeria se houver fotos (mesmo sem checkbox). Ama **1.0.54**.
- Issue [#25](https://github.com/esvianna/saulocoelho.com/issues/25) **Alumni**: fotos na turma (`ama_course` `_alumni_fotos`); produto Woo só selecciona turmas + textos; migração em Ferramentas → Alumni. Tema **1.3.43**. ADR-019.
- Issue [#24](https://github.com/esvianna/saulocoelho.com/issues/24) **Noite 3**: Líder que preciso me tornar + Meta Master + MAPA (vtis-quiz **1.3.40** + Ama **1.0.48**).
- Issue [#23](https://github.com/esvianna/saulocoelho.com/issues/23) **Mentor: PDF de apoio**: escolher **módulo** no envio (Ama **1.0.47** + tema **1.3.42**).
- Issue [#23](https://github.com/esvianna/saulocoelho.com/issues/23) **Mentor: PDF de apoio**: `sc_portal_material_notify_send()` + Ama **1.0.46** (upload no Painel da turma). Tema **1.3.41**.
- Issue [#22](https://github.com/esvianna/saulocoelho.com/issues/22) **Notificar alunos**: metabox admin + **Painel da turma** (mentores) — botão por aula. Tema **1.3.40** + Ama **1.0.45**. `sc_portal_lesson_notify_send()`.
- Issue [#22](https://github.com/esvianna/saulocoelho.com/issues/22) **Notificar alunos ao publicar aula**: metabox na aula LMS (`ama_lesson`) — aviso Portal `course:{id}` + push/e-mail opt-in (#16). `inc/module-portal-lesson-notify.php` + `sc_portal_notice_create()`. Tema **1.3.39**.
- Issue [#19](https://github.com/esvianna/saulocoelho.com/issues/19) **Noite 2 — MAPA**: entregável final `leadership-mapa-pressao` (vtis-quiz **1.3.39** + Ama **1.0.44**).
- Issue [#21](https://github.com/esvianna/saulocoelho.com/issues/21) **Portal PWA — sessão persistente**: cookie remember forçado no login Minha Conta / checkout gate; checkbox «Manter-me ligado neste aparelho» por omissão. `inc/module-portal-auth-persist.php`. Tema **1.3.32**.
- Issue [#20](https://github.com/esvianna/saulocoelho.com/issues/20) **Painel do mentor**: Ama **1.0.37** + tema **1.3.35** (contraste dark); secções contraídas; modal/Editar/Remover. ADR-013.
- Construtor do curso: «Adicionar exercício (vtis-quiz)» na grade (Ama **1.0.38**).
- Issue [#20](https://github.com/esvianna/saulocoelho.com/issues/20) **Painel do mentor**: AmaEducacional **1.0.33** (`_ama_course_mentors`, hub «Painel da turma», REST mentor/dashboard). ADR-013.
- Issue [#19](https://github.com/esvianna/saulocoelho.com/issues/19) **Noite 2**: RPSP + Radar + Decidir Antes + CMV no `vtis-quiz` **1.3.34**; grade no AmaEducacional **1.0.32**.
- Issue [#16](https://github.com/esvianna/saulocoelho.com/issues/16) **Fase C**: fila `sc_portal_push_jobs` + WP-Cron (lotes), e-mail opcional, silenciar curso, coluna Envio no admin. `inc/module-portal-push-queue.php`. Tema **1.3.31**.
- Issue [#16](https://github.com/esvianna/saulocoelho.com/issues/16) **Fase B**: Web Push (VAPID) — opt-in na tab Conta, tabela `sc_portal_push_subs`, REST `saulocoelho/v1/push/*`, SW `push`/`notificationclick`, checkbox «Enviar push agora» no admin. Lib `minishlink/web-push` em `themes/saulocoelho/vendor/`. Tema **1.3.29**.
- Issue [#16](https://github.com/esvianna/saulocoelho.com/issues/16) **Fase A**: inbox + sininho no Portal — tabelas `sc_portal_notices` / `sc_portal_notice_reads`, admin **Avisos Portal**, REST `saulocoelho/v1/notices`, badge na topbar. Tema **1.3.26**. Web Push = Fase B.

### Fixed
- Painel da turma: mentor pode definir nova senha do aluno (Ama **1.0.43** + tema **1.3.38**).
- Convite LMS: campos de senha alinhados + ícone olho (Ama **1.0.42** + tema **1.3.37**).
- Convite LMS: contraste do banner «Bem-vindo à turma» no Portal dark; senha no formulário de inscrição (Ama **1.0.41** + tema **1.3.36**) — deixa de depender do e-mail.
- RPSP (vtis-quiz **1.3.38**): mapa sem `_tiebreak` / `_tiebreak_final`; CTA com botão **Revisar Resultado** após perguntas pós-perfil.
- Painel da turma: contraste no Portal dark (cartões/modal ilegíveis) — Ama **1.0.37** + tema **1.3.35**.
- Player LMS: «Aula anterior» / «Próxima aula» sempre na mesma linha no mobile/PWA (Ama **1.0.34** + skin tema **1.3.33**).
- Portal PWA: ao reabrir a app, retoma a última página (aula/curso) em vez de ir sempre a Minha Conta. Manifest `start_url` + `localStorage` em `portal.js`. Tema **1.3.34**.
- **Convite LMS (handoff):** após cadastro em `/inscricao/{slug}/` o aluno entra na hora (cookie), vai à sala e vê como definir senha — sem depender do e-mail. `inc/module-lms-invite-handoff.php`. Tema **1.3.25**. ADR-014 · [#17](https://github.com/esvianna/saulocoelho.com/pull/17) merged.
- Issue [#15](https://github.com/esvianna/saulocoelho.com/issues/15): **Portal do Aluno** — app-shell Minha Conta (tabs Cursos · Certificados · Conta) + PWA (`manifest`/`sw` em `/portal-aluno/`, CTA instalar só na tab Conta). Tema **1.3.22**. ADR-011.
- Spec **avisos / sininho / Web Push** (ADR-013): issue [#16](https://github.com/esvianna/saulocoelho.com/issues/16) no Project #6 (**Backlog**).
- Issue [#13](https://github.com/esvianna/saulocoelho.com/issues/13): Noite 1 digital — `vtis-quiz` **1.3.24** no ar (modo reflection + seeds PDPA / Espelho / Conselho).
- Página modelo **Aviso de privacidade** (`/privacidade/`, template Legal). Ligada à política WP, ao rodapé e ao convite da turma. Texto editável no admin — revisão da equipe Saulo.
- Skin e página de **inscrição por convite** do AmaEducacional (`/inscricao/{slug}/`): identidade Playfair / `#C5A059`.
- **Leads da palestra** (Teresópolis): página `/palestra/`, cadastro obrigatório (nome, e-mail, WhatsApp, LGPD), PDF tokenizado + e-mail, admin CSV e QR. Tabela `{prefix}sc_palestra_leads`. ADR-009.
- **CRUD de palestras** (`sc_palestra`): cada evento tem data, local, textos, campos extra, ficheiros privados e imagem de fundo. `/palestra/` continua a ser a palestra principal; outras em `/palestra/{slug}/`.
- E-mail da palestra: remetente `From`/`Reply-To` = e-mail de contato do tema (`contato@saulocoelho.com`), em vez do default `wordpress@`.
- Quiz **8 Códigos de Reação** no `vtis-quiz` **1.3.23**, publicado: https://saulocoelho.com/quiz/8-codigos-de-reacao/
- Issue [#12](https://github.com/esvianna/saulocoelho.com/issues/12): Avaliação Final Neuropsicanálise — **Done**; `vtis-quiz` **1.3.22** em prod (`/quiz/neuropsicanalise/`); D37.
- Documentação do Quiz marketing multi-cliente (plugin `vtis-quiz`): ADR-008, `docs/vtis-quiz-spec.md`; issue [#6](https://github.com/esvianna/saulocoelho.com/issues/6).
- Skin do quiz no tema (`inc/module-vtis-quiz-skin.php`) — CSS variables Playfair/`#C5A059` via hook `vtis_quiz_enqueue_assets`.
- Plano issue [#7](https://github.com/esvianna/saulocoelho.com/issues/7): `docs/PLAN-vtis-quiz-escalas-faixas-aberta.md` (A escalas, B faixas dimensão, C texto aberto).
- Rascunho issue MAPA tipológico (2022) no Saulo: `docs/vtis-quiz-mapa-issue-draft.md` (D35–D36).
- Issue [#9](https://github.com/esvianna/saulocoelho.com/issues/9): MAPA tipológico no Project (In progress); implementação no plugin `vtis-quiz` **1.3.0** (local).

### Fixed
- Minha Conta: mensagens de erro de login em **pt-BR** (prefixo ERRO duplicado e “attempt(s) left” do Limit Login Attempts) e faixa de aviso no visual navy/dourado.
- Minha Conta logada: não exibir aviso de bloqueio/tentativas de login (o plugin de limite reenvia a mensagem mesmo após autenticação).
- Minha Conta: aviso WooCommerce de senha temporária em **pt-BR** (botão Reenviar).
- Landing/player AmaEducacional: o CSS global de títulos do tema já não parte o layout; paleta navy/dourado.
- Minha Conta: linhas de sublinhado por baixo dos ícones do menu lateral.
- Minha Conta: ícone WooCommerce sobreposto ao texto dos avisos (erro/info).
- Minha Conta **mobile**: layout em coluna — o conteúdo (Meus Cursos) deixava de aparecer e gerava vazio enorme (flex em linha sem `flex-direction: column`).
- Minha Conta login: botão **mostrar/ocultar senha** (ícone olho dourado) — o toggle nativo do Woo ficava invisível no dark.
- Header **mobile**: ícone Entrar / Área do Cliente ao lado do carrinho; no menu hamburger, bloco Conta (+ Sair se logado); drawer com altura `calc(100dvh − header)` para não cortar itens.
- Header **mobile** (1.3.11): overlay do menu em `position: fixed` a cobrir o ecrã — o `absolute` + `height: auto` dentro do header encolhia o drawer e a página (ex. «Minha Conta») parecia item do menu.
- Header **mobile** (1.3.12): drawer `#sc-mobile-drawer` **fora** do `<header>` — `sticky` + `backdrop-blur` anulavam o `fixed` e só o 1.º item aparecia sobre a página.
- Noite 1 (plugin `vtis-quiz` **1.3.31** + AmaEducacional **1.0.29**): Ex. 1 matriz 6×4; formulário completo (`all_at_once`); lacunas no Espelho; concluir exercício marca a aula.

### Changed
- Header logado: rótulo **Área do Aluno** (antes «Área do Cliente»).
- Portal do Aluno (1.3.24): textos do banner PWA em **pt-BR** (tela / celular / app).
- Portal do Aluno (1.3.23): banner «Instale o Portal» **acima das boas-vindas** (tab Cursos); oculto se já aberto como PWA (`standalone`) ou após «Agora não» / instalação.
- Portal do Aluno (1.3.14): ícone PWA/topbar = **ícone do site** (Customizer); hero «Portal do Aluno / Minha Conta» oculto no shell.
- Portal do Aluno (1.3.15→1.3.22): KPIs com ícone; shell no LMS; hub em 3 colunas; player sem link «Catálogo de cursos».
- Portal do Aluno (1.3.31): Web Push Fase C (fila/cron + e-mail + silêncio por curso + relatório).
- Portal do Aluno (1.3.30): copy push/avisos em **pt-BR** (Ativar/Desativar, etc.).
- Portal do Aluno (1.3.29): Web Push Fase B (opt-in Conta + envio no admin).
- Portal do Aluno (1.3.28): painel do sininho alinhado à direita no mobile/PWA; ícones Material Symbols forçados no shell (sem texto «notifications» / «school»).
- Portal do Aluno (1.3.27): ícones PWA = ícone do site (Customizer) com cache-bust `?v=`; fallbacks PNG deixam de ser o «P» placeholder; manifest com `maskable`.
- Tema **v1.3.5 → v1.3.31**.
- Landing/player AmaEducacional: sala do curso (hub) + porta de convite; login da Minha Conta devolve ao curso quando `redirect_to` vem na URL.
- Noite 1: exercícios em modo **uma resposta por aluno** (`vtis-quiz` 1.3.32) + política de refazer no curso (`AmaEducacional` 1.0.31).
- Conselho dos Três Tempos (`vtis-quiz` **1.3.33**): 5 respostas + guias (pedido Saulo 15/09); **no ar** (FTP 2026-09-15).

### Changed
- Issue #6 implementada no plugin externo https://github.com/esvianna/vtis-quiz (v1.0.0).
- Issue #7: plugin `vtis-quiz` **v1.1.0** — A+B+C + seed `codigo-da-lideranca` (DB 3).
- Decisão produto: MAPA versão **2022** destino **saulocoelho.com** (extensão tipológica do `vtis-quiz`).

## [1.3.0] — 2026-06-15

### Added (issue #4)
- **CRUD** de formulários pós-inscrição: WooCommerce → Formulários pós-inscrição (`administrator`).
- Tabelas `{prefix}sc_forms`, `sc_form_sections`, `sc_form_fields`.
- Metabox no produto: vínculo opcional `sc_post_order_form_id` (online e presencial).
- Migração automática do schema `coaching-terapia-2026-07` para o banco + vínculo em produtos presenciais.
- Snapshot `form_snapshot_json` por inscrição (versão imutável das perguntas no envio).
- Aluno pode **editar** respostas após envio; CSV e painel com colunas dinâmicas por pergunta.

### Changed
- Runtime do questionário lê BD/snapshot em vez de `form-schema.php` fixo.
- Inscrições também para produtos **online** com formulário vinculado.
- Painel WooCommerce → **Inscrições** (antes «Inscrições presenciais»); presença só em produtos presenciais.
- Tema v1.2.0 → v1.3.0; `SC_PRESENCIAL_DB_VERSION` 2.0.0.

## [1.2.0] — 2026-06-15

### Added (issue #3)
- Módulo **inscrições presenciais**: gateway WooCommerce «Pagamento direto com o Saulo», questionário pós-pedido (22 campos), endpoint Minha Conta, painel admin (inscritos/pagos/presença), export CSV.
- Tabela `{prefix}sc_presencial_enrollments`; e-mails à equipe (pedido on-hold e questionário completo).

### Changed
- Tema v1.1.0 → v1.2.0.

## [1.1.0] — 2026-06-10

### Added
- Estrutura de governança técnica (issue #2): `AGENTS.md`, `PROJECT_STATUS.md`, `ROADMAP.md`, etc.
- Regras do Cursor em `.cursor/rules/`.
- Linhas decorativas douradas nos cantos (referência Carta Pública).

### Changed
- Nova identidade visual (issue #1): paleta navy `#050A14` + dourado `#C5A059`.
- Playfair Display nos títulos; Inter mantida no corpo e UI.
- Hero com títulos em CAPS; demais seções em title-case normal.
- Tema atualizado de v1.0.9 → v1.1.0.

### Removed
- Cor primária azul `#137fec` substituída em todo o tema.

Versão atual declarada em `wp-content/themes/saulocoelho/style.css`. Histórico anterior não documentado neste arquivo.
