# PROJECT_STATUS.md — Continuidade do projeto

Última atualização: 2026-06-15

## Onde paramos

A issue **#1** (nova identidade visual) foi implementada e está em **In Review**. Aguarda validação em staging.

## Estado atual do projeto

### O que já existe

| Área | Estado |
|------|--------|
| Tema WordPress `saulocoelho` v1.1.0 | Identidade visual navy + dourado |
| Home, Sobre, Programas, Loja, Contato, Legal | Templates + metaboxes |
| WooCommerce (produtos, checkout, my-account) | Customizado |
| Checkout gate (login antes do checkout) | Implementado |
| Módulo Alumni (galerias de turmas) | Implementado |
| Módulo Testemunhos | Implementado |
| Integração ViaCEP no checkout | Implementado |
| GitHub repo + Projects | Configurado |

### Issues no GitHub Projects

| # | Título | Status |
|---|--------|--------|
| 1 | Nova identidade visual (Playfair + navy/dourado) | In review |
| 2 | Governança técnica e fluxo GitHub Projects | Done |
| 3 | Treinamento presencial: formulário, pagamento no evento e painel admin | Backlog (spec: Google Forms mapeado) |

### Issue #3 — especificação (2026-06-15)

- Google Forms mapeado campo a campo (22 campos); comentário em [issue #3](https://github.com/esvianna/saulocoelho.com/issues/3#issuecomment-4709638764).
- Decisões D9–D13 em `DECISIONS.md` (e-mail/CPF fora do form, sem pagamento no form, padrão "Outro", LGPD).
- **Pendente:** cliente confirmar ordem checkout ↔ formulário; depois mover issue para **Ready**.

### Pendências conhecidas

- Validar identidade visual em staging (issue #1).
- Tailwind CDN temporário no `header.php` — build local (`dist/output.css`) não está em uso.
- `product_requirements_document.md` é um template vazio, não um PRD real.
- Sem testes automatizados (PHPUnit, E2E).
- Deploy manual: sincronizar arquivos do Drive com servidor + limpar cache WP/CDN.

## Próximos passos recomendados

1. Validar issue #1 em staging e mover para **Done**.
2. Considerar issue futura: migrar Tailwind CDN → build local.

## Riscos ativos

- **Tailwind CDN em produção** — dependência externa, config duplicada (CDN + `tailwind.config.js`).
- **JavaScript inline** em `functions.php` (checkout qty, ViaCEP) — difícil de testar e versionar.
- **Metaboxes grandes** (`metaboxes.php` ~800 linhas) — manutenção complexa.
- **AJAX checkout qty** exposto a `nopriv` — revisar nonce/validação em issue dedicada.
- **Sincronização via Google Drive** — risco de conflitos e deploy inconsistente.

## Decisões pendentes

- Aprovar paleta e fonte da nova identidade visual (issue #1).
- Definir se Tailwind passará a usar build local em produção.
- Definir fluxo de deploy (FTP, CI/CD, staging automático).

## Como retomar depois

Pergunte ao agente:

- "Qual o status das issues no projeto?"
- "O que está em Ready para executar?"
- "Leia PROJECT_STATUS.md e ROADMAP.md — qual o próximo passo lógico?"
