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

## Decisões pendentes

- Estratégia de deploy (manual vs CI/CD).
- Adoção de testes automatizados.

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
