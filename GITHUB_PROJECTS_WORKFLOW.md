# GITHUB_PROJECTS_WORKFLOW.md

Projeto: [Site Saulocoelho.com](https://github.com/users/esvianna/projects/6)  
Repositório: [esvianna/saulocoelho.com](https://github.com/esvianna/saulocoelho.com)

## Fluxo de status

```
Backlog → Ready → In progress → In review → Done
```

## Significado de cada status

### Backlog
Issues **não aprovadas** para implementação.

**Cursor pode:**
- Analisar e detalhar
- Propor critérios de aceite
- Identificar riscos e abordagem técnica
- Sugerir divisão em issues menores
- Comentar na issue

**Cursor não pode:** implementar código (exceto aprovação explícita na conversa).

### Ready
Issues **aprovadas** pelo mantenedor para execução.

**Cursor deve antes de codar:**
1. Ler a issue completa
2. Confirmar status Ready no Project
3. Revisar critérios de aceite
4. Consultar `AGENTS.md`, `PROJECT_STATUS.md`, `DECISIONS.md`
5. Planejar alteração e escopo

**Cursor pode:** implementar conforme a issue.

### In progress
Issue em execução ativa.

**Cursor deve:** mover para In progress ao iniciar (se tiver permissão via `gh`).

```bash
gh project item-edit \
  --project-id PVT_kwHOBfIcG84BaSb_ \
  --id <ITEM_ID> \
  --field-id PVTSSF_lAHOBfIcG84BaSb_zhVLG40 \
  --single-select-option-id 47fc9ee4
```

IDs de status (campo Status):
| Status | Option ID |
|--------|-----------|
| Backlog | `f75ad846` |
| Ready | `61e4505c` |
| In progress | `47fc9ee4` |
| In review | `df73e18b` |
| Done | `98236657` |

### In review
Implementação concluída; aguarda validação humana.

**Cursor deve:** mover ao finalizar e comentar com:
- O que foi feito
- Arquivos alterados
- Como testar
- Testes realizados / pendentes
- Riscos
- Próximo passo

### Done
Tarefa **validada** pelo mantenedor.

**Cursor só move para Done** se o usuário pedir explicitamente.

## Comandos úteis (GitHub CLI)

```bash
# Listar itens do projeto
gh project item-list 6 --owner esvianna --format json

# Ver issue
gh issue view 2 --repo esvianna/saulocoelho.com

# Comentar na issue
gh issue comment 2 --repo esvianna/saulocoelho.com --body "Resumo da implementação..."

# Campos do projeto
gh project field-list 6 --owner esvianna
```

## Campos extras do projeto

- **Priority:** P0, P1, P2
- **Size:** XS, S, M, L, XL

Usar para planejamento; não bloqueia execução de issues Ready.

## Bloqueios

Se o agente não conseguir acessar o Projects:
- Informar a limitação claramente
- Não assumir status de issues
- Pedir ao usuário confirmar status manualmente
- Continuar usando documentação local (`PROJECT_STATUS.md`)

## Criar novas issues

1. Agente propõe lista (título, escopo, critérios, riscos) em `ROADMAP.md` ou na conversa
2. Usuário aprova
3. Usuário ou agente (se pedido) cria via `gh issue create`
4. Adicionar ao Project com status **Backlog**

## Regra obrigatória

> Nenhuma implementação a partir de issue em **Backlog**, exceto aprovação direta e explícita na conversa atual.
