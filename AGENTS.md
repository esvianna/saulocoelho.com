# AGENTS.md — Orientação para agentes de IA (Cursor)

Este arquivo é o ponto de entrada para qualquer agente que trabalhe no projeto **Saulocoelho.com**.

## O que é este projeto

Tema WordPress customizado (`saulocoelho`) para o site de Saulo Coelho — liderança e alta performance — com loja WooCommerce, checkout premium e páginas de vendas de cursos.

- **Repositório:** https://github.com/esvianna/saulocoelho.com
- **Projeto GitHub:** https://github.com/users/esvianna/projects/6
- **Staging:** https://saulo.vtis.com.br
- **Produção:** https://saulocoelho.com

## Antes de qualquer alteração

1. Ler esta issue no GitHub Projects e confirmar o **status** (só implementar em **Ready** ou com aprovação explícita).
2. Consultar, nesta ordem:
   - `PROJECT_STATUS.md` — onde paramos
   - `DECISIONS.md` — decisões já tomadas
   - `SECURITY.md` — quando houver impacto em dados, auth ou formulários
   - `TESTING.md` — como validar
   - `GITHUB_PROJECTS_WORKFLOW.md` — fluxo de status
3. Entender o escopo da issue: o que está **dentro** e **fora**.
4. Preservar funcionalidades existentes; evitar refatorações grandes sem issue aprovada.

## Depois de qualquer alteração

1. Atualizar `CHANGELOG.md` se a mudança for relevante.
2. Atualizar `PROJECT_STATUS.md`.
3. Registrar decisões importantes em `DECISIONS.md`.
4. Informar: arquivos alterados, como testar, testes feitos/pendentes, riscos.
5. Comentar na issue do GitHub e mover para **In Review** (se tiver permissão).

## GitHub Projects — regra de ouro

| Status | Agente pode implementar? |
|--------|--------------------------|
| Backlog | Não — apenas analisar, detalhar, propor |
| Ready | Sim — após ler critérios de aceite |
| In progress | Sim — execução em andamento |
| In review | Não — aguardar validação humana |
| Done | Não mover sem pedido explícito do usuário |

## Stack e convenções

- **PHP** (WordPress 6.x+), tema em `wp-content/themes/saulocoelho/`
- **WooCommerce** — checkout, loja, my-account customizados
- **Tailwind CSS** — build via npm em `src/input.css` → `dist/output.css`; hoje o `header.php` ainda usa CDN temporário
- **Metaboxes** — conteúdo editável em `inc/metaboxes.php`
- **Módulos** — lógica em `inc/module-*.php`, templates em `template-parts/`
- **Estética** — dark mode premium, Playfair Display (títulos), Inter (corpo), cor primária `#C5A059`

## Limites importantes

- Não alterar funcionalidades fora do escopo da issue.
- Não criar issues no GitHub sem aprovação do usuário.
- Não commitar/push sem pedido explícito.
- Não expor credenciais em código, commits ou documentação.
- Não mover issue para **Done** sem autorização explícita.
- Regras do Cursor em `.cursor/rules/` complementam este arquivo.

## Arquivos de referência legados

Documentação anterior (manter como contexto, não substituir a governança nova):

- `theme_instructions.md`
- `COMO_INSTALAR_O_TEMA.md`
- `.agent/instructions.md`

## Comandos úteis

```bash
# Build Tailwind (dentro do tema)
cd wp-content/themes/saulocoelho && npm install && npm run build

# GitHub Projects
gh project item-list 6 --owner esvianna
gh issue view <numero> --repo esvianna/saulocoelho.com
```
