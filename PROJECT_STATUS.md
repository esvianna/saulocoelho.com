# PROJECT_STATUS.md — Continuidade do projeto

Última atualização: 2026-08-31

## Onde paramos

Issue **[#12](https://github.com/esvianna/saulocoelho.com/issues/12)** Avaliação Final Neuropsicanálise — plugin **vtis-quiz v1.3.20**. Slug **`neuropsicanalise`**. Timer 90 min. **Nota prática + certificação 60/40** no admin Leads.

Repo plugin: https://github.com/esvianna/vtis-quiz · Skin: `inc/module-vtis-quiz-skin.php`.

**URL:** https://saulocoelho.com/quiz/neuropsicanalise/

## Pós-deploy (validar no WP)

1. Abrir um lead da Neuro → ver score teórico + faixa; lançar nota prática (ex. 8,0); confirmar nota final e relatório.
2. Imprimir relatório: blocos teórico / prática / certificação.
3. **Não** gravar este quiz no ecrã de edição do admin (destrói `options_json` MC).

## Decisões fechadas

- `result_delivery`: **deferred**.
- Slug: `neuropsicanalise`.
- Timer: **90 minutos**.
- Prática: lançada no lead; final = teórica(0–10)×0,60 + prática×0,40; aprovado ≥ 7,5 + ética + conduta segura (D37).

## Próximos passos

1. Validar #12 → Done quando OK.
2. Issue opcional: UI de gabarito MC no admin.

## Como retomar

- "Validar nota prática #12" ou "Deploy vtis-quiz 1.3.20"
