# PROJECT_STATUS.md — Continuidade do projeto

Última atualização: 2026-07-24

## Onde paramos

Issue **#6** (MVP) e **#7** (1.1) em **In Review**. Plugin **v1.1.0** (DB 3) em staging + produção. Skin do tema **deployada em produção** (2026-07-24).

Repo: https://github.com/esvianna/vtis-quiz · Local: `C:\Users\dudav\Documents\Projetos\vtis-quiz` · Skin: `inc/module-vtis-quiz-skin.php`.

**Pós-deploy:** no WP Admin visitar/reactivar plugin (upgrade DB→3 + seed); **flush permalinks**; testar shortcodes + visual da skin.

## Estado atual do projeto

### O que já existe

| Área | Estado |
|------|--------|
| Tema WordPress `saulocoelho` v1.3.0 | Identidade visual + inscrições/questionários |
| Home, Sobre, Programas, Loja, Contato, Legal | Templates + metaboxes |
| WooCommerce (produtos, checkout, my-account) | Customizado |
| Checkout gate (login antes do checkout) | Implementado |
| Módulo Alumni (galerias de turmas) | Implementado |
| Módulo Testemunhos | Implementado |
| Integração ViaCEP no checkout | Implementado |
| Formulários pós-inscrição CRUD (`sc_forms*`) | Implementado (v1.3.0) |
| Plugin VTIS Quiz (`vtis-quiz` 1.0.0) | MVP entregue — In Review |
| Skin quiz no tema | `module-vtis-quiz-skin.php` |
| GitHub repo + Projects | Configurado |

### Issues no GitHub Projects

| # | Título | Status |
|---|--------|--------|
| 1 | Nova identidade visual (Playfair + navy/dourado) | Done |
| 2 | Governança técnica e fluxo GitHub Projects | Done |
| 3 | Treinamento presencial: formulário, pagamento no evento e painel admin | Done |
| 4 | CRUD: formulários pós-inscrição configuráveis por produto | Done |
| 5 | Ajustes no processo de associação dos formulários dos produtos (pós-pedido) | Backlog |
| 6 | Quiz marketing multi-cliente: plugin VTIS Quiz (`vtis-quiz`) | In review |
| 7 | vtis-quiz 1.1: escalas, faixas por dimensão, pergunta aberta | In review |
| 8 | vtis-quiz 1.2: relatório resultado por e-mail (HTML) | In review |

### Pendências conhecidas

- Validar #7 (escalas, faixas, texto aberto, seed `codigo-da-lideranca`); deploy 1.1.0.
- Validar #6 em staging (checklist na issue).
- Copy/dimensões finais do 1.º quiz Saulo (substituir seed).
- Sem testes automatizados (PHPUnit, E2E).
- Deploy manual.

## Próximos passos recomendados

1. Deploy plugin em staging + flush permalinks; testar `[vtis_quiz slug="avaliacao-exemplo"]` e `/quiz/avaliacao-exemplo/`.
2. Cliente fornece copy → editar quiz no admin.
3. Após validação humana: mover #6 para **Done**.

## Riscos ativos

- **Tailwind CDN em produção** — dependência externa.
- **JavaScript inline** em `functions.php` (checkout).
- **Metaboxes grandes** — manutenção complexa.
- **AJAX checkout qty** `nopriv` — nonce pendente.
- **Quiz marketing** — LGPD/copy; seed é placeholder.

## Decisões pendentes

- Conteúdo definitivo do 1.º quiz Saulo.
- Definir se Tailwind passa a build local.
- Fluxo de deploy.

## Como retomar depois

- "Leia PROJECT_STATUS.md e a issue #6 — o que falta validar no quiz?"
