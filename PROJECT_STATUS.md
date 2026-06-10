# PROJECT_STATUS.md — Continuidade do projeto

Última atualização: 2026-06-10

## Onde paramos

A issue **#2** (governança técnica e fluxo GitHub Projects) foi implementada. O projeto agora possui documentação viva, regras do Cursor e integração com o GitHub Projects como fonte de verdade para tarefas.

**Nenhuma funcionalidade do site foi alterada nesta etapa.**

## Estado atual do projeto

### O que já existe

| Área | Estado |
|------|--------|
| Tema WordPress `saulocoelho` v1.0.9 | Funcional |
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
| 2 | Governança técnica e fluxo GitHub Projects | In review |
| 1 | Alterar cores e fontes (nova identidade visual) | Backlog |

### Pendências conhecidas

- Issue #1 aguarda aprovação para execução (Playfair Display, azul escuro + dourado).
- Tailwind CDN temporário no `header.php` — build local (`dist/output.css`) não está em uso.
- `product_requirements_document.md` é um template vazio, não um PRD real.
- Sem testes automatizados (PHPUnit, E2E).
- Deploy manual: sincronizar arquivos do Drive com servidor + limpar cache WP/CDN.

## Próximos passos recomendados

1. Validar a governança criada nesta issue (#2) e mover para **Done**.
2. Revisar e aprovar a issue #1 (identidade visual) — mover para **Ready** quando pronta.
3. Considerar issue futura: migrar Tailwind CDN → build local.
4. Considerar issue futura: documentar PRD real do produto.

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
