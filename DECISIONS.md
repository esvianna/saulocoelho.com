# DECISIONS.md — Registro de decisões técnicas

Formato inspirado em ADR (Architecture Decision Record).

---

## ADR-001 — Governança via GitHub Projects + documentação viva

| Campo | Valor |
|-------|-------|
| **Data** | 2026-06-10 |
| **Status** | Aceita |
| **Contexto** | Projeto mantido em Google Drive com histórico fragmentado; necessidade de retomar trabalho com IA de forma segura e rastreável. |
| **Decisão** | Usar GitHub Projects como fonte de verdade para tarefas; documentação em arquivos na raiz do repo; regras do Cursor em `.cursor/rules/`. Fluxo: Backlog → Ready → In progress → In review → Done. |
| **Motivo** | Separar análise de implementação; evitar mudanças não aprovadas; permitir perguntas de continuidade ("onde paramos?"). |
| **Consequências** | Issues em Backlog não são implementadas sem aprovação; agente deve atualizar docs após mudanças; usuário valida antes de Done. |

---

## ADR-002 — Tema WordPress customizado (não page builder)

| Campo | Valor |
|-------|-------|
| **Data** | (pré-governança, registrado em 2026-06-10) |
| **Status** | Aceita |
| **Contexto** | Site premium com controle fino de UX, checkout e páginas de venda. |
| **Decisão** | Tema `saulocoelho` em PHP + Tailwind; conteúdo editável via metaboxes; módulos em `inc/`. |
| **Motivo** | Performance, identidade visual consistente, integração profunda com WooCommerce. |
| **Consequências** | Edição de conteúdo exige WordPress admin; mudanças visuais passam pelo código do tema. |

---

## ADR-003 — Tailwind via CDN (temporário)

| Campo | Valor |
|-------|-------|
| **Data** | (pré-governança, registrado em 2026-06-10) |
| **Status** | Provisória — ver ROADMAP |
| **Contexto** | `header.php` carrega `cdn.tailwindcss.com` com comentário "Temporary Tailwind CDN for layout fix". Existe pipeline npm (`package.json`) mas `dist/output.css` não está no repositório. |
| **Decisão** | Manter CDN até issue dedicada de migração. |
| **Motivo** | Layout funcionando em produção; build local não finalizado. |
| **Consequências** | Dependência de CDN externo; config Tailwind duplicada; possível impacto em performance e CSP. |

---

## ADR-004 — Checkout gate obrigatório para visitantes

| Campo | Valor |
|-------|-------|
| **Data** | (pré-governança, registrado em 2026-06-10) |
| **Status** | Aceita |
| **Contexto** | Venda de cursos/info-produtos; necessidade de conta antes do pagamento. |
| **Decisão** | `module-checkout-gate.php` redireciona não logados para `/boas-vindas/`. |
| **Motivo** | Captura de lead, experiência premium, dados de faturamento. |
| **Consequências** | Fluxo de compra com etapa extra; página `/boas-vindas/` é dependência crítica. |

---

## ADR-006 — Inscrições presenciais: formulário pós-pedido, pagamento pendente e painel admin

| Campo | Valor |
|-------|-------|
| **Data** | 2026-06-15 |
| **Status** | Implementado no tema v1.2.0 (2026-06-15) — **In Review**; integração AmaEducacional via hook `sc_presencial_enrollment_created` quando plugin disponível |
| **Contexto** | Cliente solicita formulário de cadastro/pesquisa para treinamento presencial ([Coaching\|Terapia](https://saulocoelho.com/produto/formacao-coaching-comportamental/)), opção de pagamento via equipe e painel de inscrições. Issue #3. |
| **Decisão** | (1) Formulário **após** finalização do pedido, **não bloqueante** — pendência em Minha Conta. (2) Pagamento offline via **gateway WooCommerce** com pedido pendente, notificação à equipe e reserva imediata de vaga (sem prazo automático de cancelamento). (3) Confirmação de pagamento manual no WooCommerce; NF fora do sistema. (4) Painel admin para `administrator` com relatório inscritos/pagos/presentes. (5) Formulário **reutilizável** entre produtos. (6) Integração preferencial com plugin **AmaEducacional** (`ama_course`, `lms_enrollments`). (7) E-mails: confirmação, lembrete de formulário, lembrete de evento; material só após pedido confirmado. |
| **Motivo** | Reaproveitar stack WC + LMS existente; evitar Google Forms embed (dados fragmentados); alinhar turmas presenciais ao modelo `ama_course` já usado no Alumni. |
| **Consequências** | Desenvolvimento cross-repo (tema + AmaEducacional); possível extensão de schema de enrollments; check-in/QR ficam para detalhamento de v1. |

---

## Decisões pendentes

- Estratégia de deploy (manual vs CI/CD).
- Adoção de testes automatizados.
- ~~Conteúdo definitivo (perguntas/dimensões) do 1.º quiz Saulo.~~ — *Código da Liderança* (seed) + **MAPA 2022** decidido para Saulo (D35); implementação tipológica pendente.
- ~~Criar repositório GitHub do plugin `vtis-quiz`~~ — **criado** https://github.com/esvianna/vtis-quiz (2026-07-24).
- ~~Ordem checkout vs. formulário~~ — **aprovado:** questionário após finalização do pedido (D14).
- ~~Check-in v1~~ — **lista manual** no painel admin; QR/crachás para v2 se necessário (D17).
- ~~Limite de vagas~~ — **estoque WooCommerce** no produto = número de vagas (D16); sem metabox `course_max_seats`.
- ~~Plugin vs tema para quiz marketing~~ — **aprovado:** plugin separado + lead antes do resultado (ADR-008, D29).
- ~~Nome `vtis-quiz`~~ — **confirmado** (2026-07-24).

### ADR-006 — complementos (mapeamento Google Forms, 2026-06-15)

| ID | Decisão |
|----|---------|
| D9 | E-mail e CPF **fora** do formulário de pesquisa (conta WP + checkout BR). |
| D10 | Campo de pagamento do Google Forms **não** replicado; pagamento só no WooCommerce. |
| D11 | Multiselect com "Outro": slug `other` + campo `*_other` condicional e obrigatório se marcado. |
| D12 | `coaching_use_intent` como select (opções mutuamente exclusivas). |
| D13 | Respostas sensíveis do formulário: painel admin apenas `administrator`. |
| D14 | Questionário **após** finalização do pedido — **aprovado pelo cliente** (2026-06-15). |
| D15 | Checkout com opção **"Pagamento direto com o Saulo"** (ou equivalente): pedido pendente, equipe confirma pagamento no WC, vaga reservada na hora. |
| D16 | **Limite de vagas:** usar **estoque WooCommerce** no produto (`manage_stock` + quantidade = vagas); checkout bloqueia ao esgotar. Pedidos pendentes consomem estoque conforme config WC. |
| D17 | **Check-in v1:** lista manual no painel (presente/ausente); sem QR code na primeira versão. |
| D18 | Questionário pós-inscrição **v1 hardcoded** (`form-schema.php`); evolução com **CRUD + vínculo produto** na issue **#4** (Backlog). |

Schema: `coaching-terapia-2026-07` — 22 campos; detalhe em issue #3. CRUD configurável: issue #4.

---

## ADR-007 — CRUD de formulários pós-inscrição (issue #4)

| Campo | Valor |
|-------|-------|
| **Data** | 2026-06-15 |
| **Status** | Implementada (v1.3.0) — **In Review** |
| **Contexto** | Questionário v1 hardcoded (D18); cliente precisa criar perguntas e associar formulário ao produto. |
| **Decisão** | CRUD no admin (`administrator`); vínculo opcional formulário ↔ produto WC; reutilização do mesmo form em N produtos; online e presencial; edição de respostas pelo aluno; sem condicionais na v1; seções e ordem livres; limites por tipo de campo; respostas versionadas; painel e CSV dinâmicos. |
| **Motivo** | Autonomia da equipe sem deploy; flexibilidade por produto mantendo reuso. |
| **Consequências** | Substituir `form-schema.php` por dados em BD; ampliar enrollments para produtos online; condicionais e versionamento avançado ficam para v2. |

### ADR-007 — complementos

| ID | Decisão |
|----|---------|
| D19 | CRUD de formulários: apenas role **`administrator`**. |
| D20 | Aluno pode **editar** respostas após envio (v1). |
| D21 | Formulário **opcional por produto**; mesmo formulário **reutilizável** em vários produtos. |
| D22 | **Sem campos condicionais** na v1 do CRUD (fluxos «Se sim…», «Outro» → v2). |
| D23 | Limites por tipo: texto curto **254** chars; texto longo **até 4.000** (configurável por campo). |
| D24 | **Ordem livre** de seções e perguntas (não fixar 4 blocos). |
| D25 | Produto **sem** formulário → **sem questionário**; pedido finaliza sem CTA/pendência. |
| D26 | Produtos **online e presencial** podem ter formulário pós-pedido. |
| D27 | Troca de formulário no produto: respostas antigas **permanecem na versão antiga**. |
| D28 | Painel inscrições e **export CSV** com colunas **dinâmicas** por pergunta. |

### ADR-007 — armazenamento técnico (implementado)

| Elemento | Implementação |
|----------|----------------|
| Formulários | Tabelas `sc_forms`, `sc_form_sections`, `sc_form_fields` |
| Vínculo produto | Post meta `sc_post_order_form_id` (metabox lateral do produto) |
| Versionamento | Coluna `version` em `sc_forms`; incremento ao salvar estrutura |
| Respostas versionadas | `form_snapshot_json` + `form_version` em `sc_presencial_enrollments` |
| Legado | `form-schema.php` mantido como seed/fallback; migração automática na ativação |

---

## ADR-005 — Nova identidade visual (navy + dourado + Playfair)

| Campo | Valor |
|-------|-------|
| **Data** | 2026-06-10 |
| **Status** | Aceita |
| **Contexto** | Cliente compartilhou peças de marca (Carta Pública, "O COMPORTAMENTO DECIDE") com paleta escura e dourada. |
| **Decisão** | Primary `#C5A059`, fundo `#050A14`/`#0A0E1A`, Playfair Display nos títulos, Inter no corpo, CAPS só no hero, linhas decorativas douradas incluídas. |
| **Motivo** | Alinhar site à nova marca premium do cliente. |
| **Consequências** | Tema v1.1.0; validação visual necessária em staging. |

---

## ADR-008 — Plugin VTIS Quiz (marketing/avaliação multi-cliente)

| Campo | Valor |
|-------|-------|
| **Data** | 2026-07-24 |
| **Status** | Implementada (plugin v1.0.0) — **In Review** (issue #6) |
| **Contexto** | Cliente Saulo Coelho quer quizzes no estilo [Bylevel Quiz Performance](https://bylevel.com.br/quiz-performance/) (perguntas → score → mapa por dimensões → CTA). O mesmo produto poderá ser reutilizado por AmaMinerais e outros sites WP. Já existem no ecossistema: (a) questionário pós-inscrição `sc_forms*` no tema (sem scoring/wizard); (b) quizzes LMS no AmaEducacional (avaliação de aula). |
| **Decisão** | (1) **Plugin WordPress separado** (prefixo/text domain propostos `vtis-quiz`), em repositório próprio — **não** implementar como módulo do tema `saulocoelho`. (2) Tema host só integra (página/shortcode + skin CSS da marca). (3) **Lead obrigatório antes do resultado** (e-mail e WhatsApp): sem lead válido não exibe score/mapa. (4) Dados em tabelas custom do plugin (`vtis_quiz_*`); **não** reutilizar `sc_*` nem `lms_*`/`ama_*`. (5) Front: shortcode + rewrite `/quiz/{slug}/`; UX multi-step inspirada no modelo Bylevel. (6) MVP sem acoplamento WooCommerce — CTA = URL configurável; CRM/Woo em fases posteriores via hooks. (7) Spec completa em `docs/vtis-quiz-spec.md`. |
| **Motivo** | Reuso multi-cliente; separação clara de produtos (pós-pedido vs marketing vs LMS); white-label sem lock-in de marca Saulo/Ama. |
| **Consequências** | Novo repo/plugin quando for implementar; governança e 1.ª instalação no Project Saulo; skins por tema; LGPD e disclaimer obrigatórios no gate de lead. |

### ADR-008 — complementos

| ID | Decisão |
|----|---------|
| D29 | Captura de lead **antes** do resultado (e-mail + WhatsApp); consentimento LGPD no gate. |
| D30 | Prefixo/repo **`vtis-quiz`** — nome confirmado (2026-07-24); issue [#6](https://github.com/esvianna/saulocoelho.com/issues/6). |
| D31 | Fora de escopo do plugin: questionário pós-inscrição (`sc_forms`) e quizzes LMS AmaEducacional. |
| D32 | MVP: admin do quiz + front wizard + submissions + CSV; sem CRM/Woo. |
| D33 | Skin visual no tema do site (ex.: Playfair/`#C5A059` no Saulo); CSS base neutro no plugin. |
| D34 | Issue #7: A+B+C + seed `codigo-da-lideranca` na v1.1.0 (DB 3). |
| D35 | **MAPA** (Método de Avaliação da Personalidade Ativa) no **saulocoelho.com**: versão canónica **2022** (60 itens Likert, 15 por dicotomia E/I·N/S·T/F·J/P, pontos com sinal, % clareza, código de 4 letras + perfil dos 16 tipos). Fonte: `SCCR/HERO/MAPA` (Teste/Revisão MAPA.xlsx, PDF perguntas, PERSONALIDADES, CAPA). |
| D36 | MAPA **não** cabe só com seed no motor Likert actual — exige extensão do `vtis-quiz` (modo tipológico / typology). Plugin continua multi-cliente; 1.ª entrega + skin no tema Saulo. Posicionamento: método próprio inspirado em teoria de tipos — **não** marcar como MBTI® oficial. PDF/capa estilo Word = fase posterior ao MVP tipológico (HTML + e-mail). |
| D37 | **Avaliação Final Neuropsicanálise** (#12, **Done** 2026-08-31): no **vtis-quiz** (≥1.3.22); seed MC; `result_delivery` = **deferred**; timer 90 min; slug **`neuropsicanalise`**; prática 60/40 no admin; **`shuffle_options`**; save admin **preserva** gabarito MC. UI de edição de gabarito no admin ainda **fora** (issue futura opcional). |
| D38 | Palestra Teresópolis (2026-09): captura de leads **no tema WP** (`/palestra/`), não no PWA mock nem no `vtis-quiz`. PDF tokenizado + e-mail; ver ADR-009. |

---

## ADR-009 — Leads da palestra com PDF tokenizado

| Campo | Valor |
|-------|-------|
| **Data** | 2026-09-12 |
| **Status** | Aceita |
| **Contexto** | Palestra *O Comportamento Decide* (Teresópolis, 2026-09-14). Precisava de QR/link para slides em PDF com cadastro (nome, e-mail, WhatsApp) para a plataforma OCD. O PWA `app.saulocoelho.com` não persiste dados. |
| **Decisão** | Página `/palestra/` no tema `saulocoelho`. Tabela `{prefix}sc_palestra_leads`. PDF em `wp-content/themes/saulocoelho/private/` (bloqueado via HTTP). Download só com token (48 h, até 8 downloads). Cópia por e-mail (anexo; se o SMTP recusar, só o link). Admin **Leads palestra** + CSV. |
| **Motivo** | Marca do site, LGPD, dados no WP (não no protótipo Expo), deploy só do tema via FTP. |
| **Consequências** | Página criada automaticamente no `init` se o slug não existir. Não cria conta Woo. Não liga ao app OCD. O PDF continua partilhável depois do 1.º download. Evolução: CPT `sc_palestra` (CRUD) para repetir o funil em novas palestras; Teresópolis permanece canónica em `/palestra/`. |

---

## ADR-010 — Leadership Academy: convite no LMS, skin no tema

| Campo | Valor |
|-------|-------|
| **Data** | 2026-09-14 |
| **Status** | Aceita |
| **Contexto** | Turma Leadership Academy no AmaEducacional; alunos precisam de cadastro simples (QR/token) e ver o curso em Minha Conta. Quizzes vtis-quiz ainda não existem (fase 2). PWA OCD fora de escopo. |
| **Decisão** | (1) Inscrição no plugin AmaEducacional (`/inscricao/{slug}/?t=`). (2) Tema só faz skin + avisos de login em pt-BR. (3) Não usar checkout Woo nem auto-inscrição gratuita aberta. (4) Curso deve ficar oculto no catálogo. |
| **Motivo** | Matrícula já vive no LMS; Minha Conta já lista cursos matriculados. |
| **Consequências** | Deploy cruzado (plugin 1.0.20 + tema 1.3.1). Regenerar permalinks se `/inscricao/` der 404. |

---

## ADR-011 — Portal do Aluno: shell Minha Conta + PWA (só logados)

| Campo | Valor |
|-------|-------|
| **Data** | 2026-09-15 |
| **Status** | Aceita |
| **Contexto** | Alunos precisam de acesso app-like a cursos/certificados/conta sem chrome de marketing; instalação PWA só na área do aluno. OCD (`app.saulocoelho.com`) é produto à parte. |
| **Decisão** | (1) Shell se logado em Minha Conta **ou** singular LMS (`ama_course` / `ama_lesson`). (2) Tabs Cursos · Certificados · Conta; no LMS a tab Cursos fica activa. (3) Manifest/SW em `/portal-aluno/*`; CTA instalar só na tab Conta. (4) Convite `/inscricao/` e PWA OCD fora do shell. |
| **Motivo** | Reaproveitar Minha Conta + LMS; ao retomar aula o aluno não perde o chrome app-like. |
| **Consequências** | Tema ≥ 1.3.20; flush rewrite na 1.ª carga (`saulocoelho_portal_rewrite_v1`). Visitante no curso continua com header de marketing. |

---

## ADR-012 — Exercícios de aula: uma resposta + refazer

| Campo | Valor |
|-------|-------|
| **Data** | 2026-09-15 |
| **Status** | Aceita |
| **Contexto** | Quizzes de marketing permitem várias respostas; exercícios da Leadership Academy na grade LMS devem ter uma resposta por aluno e opção de refazer só se permitido. |
| **Decisão** | Implementação no plugin `vtis-quiz` (≥ 1.3.32): `one_response_per_user` + `allow_retake` (update). Política por curso no AmaEducacional (≥ 1.0.31) via filtro `vtis_quiz_allow_retake`. |
| **Motivo** | Evitar várias submissions do mesmo exercício; aluno ao voltar vê o resultado. |
| **Consequências** | Deploy dos dois plugins; quizzes de marketing sem as flags continuam ilimitados. |

---

## ADR-013 — Portal: avisos (sininho) + Web Push

| Campo | Valor |
|-------|-------|
| **Data** | 2026-09-15 |
| **Status** | Aceita (spec; implementação só com issue Ready) |
| **Contexto** | Alunos do Portal PWA precisam de avisos da equipa (turma, lembretes). iOS só tem Web Push fiável com PWA instalada; muitos alunos verão o browser sem permissão de push. OCD (`app.saulocoelho.com`) é produto à parte. |
| **Decisão** | (1) **Inbox + sininho** é a fonte de verdade (sempre disponível no Portal logado). (2) **Web Push** (VAPID) é canal opcional na mesma notificação — não um sistema paralelo. (3) Backend em **módulo/plugin WP** (tabelas + REST + admin); UI do sininho no **tema** (topbar Portal). Preferência: plugin dedicado leve no ecossistema Saulo **ou** módulo no AmaEducacional se a segmentação por curso for o núcleo — **v1: plugin/módulo no site Saulo** com audience `all` / `course:{id}` / `user:{id}` via matrículas Ama quando existir. (4) Fases: **A** inbox+admin+sininho; **B** subscribe+SW push+envio; **C** fila/relatório/e-mail. (5) Não misturar SW/origin com OCD. |
| **Motivo** | Inbox cobre quem nega push ou usa iOS sem instalar; push só “acorda” o aluno. Separar dados do tema facilita secrets VAPID e deploy. |
| **Consequências** | Issue [#16](https://github.com/esvianna/saulocoelho.com/issues/16) em **Backlog**. VAPID public no front; private só server-side. Opt-in explícito na tab Conta. SW Portal ganha handlers `push` / `notificationclick`. |

---

## ADR-014 — Convite LMS: acesso imediato sem depender do e-mail

| Campo | Valor |
|-------|-------|
| **Data** | 2026-09-16 |
| **Status** | Aceita (tema ≥ 1.3.25 em produção; [#17](https://github.com/esvianna/saulocoelho.com/pull/17) merged) |
| **Contexto** | Conta criada no `/inscricao/{slug}/?t=` com senha gerada por e-mail. Alunos sem e-mail ficavam sem senha e sem sala. |
| **Decisão** | No tema (`inc/module-lms-invite-handoff.php`): após `user_register` nesse URL, autenticar por cookie, redirecionar à sala quando matriculado, CTA de backup + aviso para definir senha em Minha Conta. E-mail do plugin continua como backup. |
| **Motivo** | O e-mail não pode ser o único próximo passo; alinhado ao login silencioso do checkout. |
| **Consequências** | Patch no tema sem esperar release do AmaEducacional. Ideal no futuro: o plugin autenticar no próprio `InviteRegistration`. Não sobrescrever o Portal 1.3.24+ no deploy — só ficheiros do handoff + bump de versão. |

