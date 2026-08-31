# PROJECT_STATUS.md — Continuidade do projeto

Última atualização: 2026-08-29

## Onde paramos

Issue **[#12](https://github.com/esvianna/saulocoelho.com/issues/12)** Avaliação Final Neuropsicanálise — plugin **vtis-quiz v1.3.17**. Slug canónico **`neuropsicanalise`**. Timer 90 min. Aguarda validação humana → **Done**.

Repo plugin: https://github.com/esvianna/vtis-quiz · Skin: `inc/module-vtis-quiz-skin.php`.

**URL:** https://saulocoelho.com/quiz/neuropsicanalise/

## Pós-deploy (validar no WP)

1. Completar 1 submission de teste; confirmar lead em **VTIS Quiz → Leads** (score + respostas). Resultado ao aluno: **deferred**.
2. Em Leads: título do quiz visível; se houver aviso de órfãos → **Eliminar leads órfãos**.
3. Export CSV opcional.
4. **Não** gravar este quiz no ecrã de edição do admin (destrói `options_json` MC).
5. Flush permalinks só se alguma rota `/quiz/…` falhar (rewrite já respondeu 200 em prod).

## Decisões fechadas nesta entrega

- `result_delivery`: **deferred** (como MAPA; nota revelada pela formação).
- Slug: `neuropsicanalise` (URL https://saulocoelho.com/quiz/neuropsicanalise/ ; legado `neuropsicanalise-final`).
- Timer: **90 minutos** (`time_limit_minutes`, v1.3.16+).
- **Não** gravar o quiz no ecrã de edição do admin (destrói `options_json` MC → Likert).
- Fora ainda: prática no admin, UI de gabarito MC no admin.

## Próximos passos

1. Deploy staging (`saulo.vtis.com.br`) → smoke test → prod.
2. Ligar shortcode na página Woo/formação quando existir.
3. Validar #12 → Done quando OK.
4. Issues futuras opcionais: nota prática no admin, timer, tipo MC no admin.

## Como retomar

- "Deploy do vtis-quiz 1.3.15 feito — validar #12" ou "Mudar neuro para immediate / outro slug"
