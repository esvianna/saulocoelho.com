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
- ~~Ordem checkout vs. formulário~~ — **aprovado:** questionário após finalização do pedido (D14).
- ~~Check-in v1~~ — **lista manual** no painel admin; QR/crachás para v2 se necessário (D17).
- ~~Limite de vagas~~ — **estoque WooCommerce** no produto = número de vagas (D16); sem metabox `course_max_seats`.

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

Schema: `coaching-terapia-2026-07` — 22 campos; detalhe em issue #3.

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
