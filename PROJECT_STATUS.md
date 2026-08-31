# PROJECT_STATUS.md — Continuidade do projeto

Última atualização: 2026-08-31

## Onde paramos

Issue **[#12](https://github.com/esvianna/saulocoelho.com/issues/12)** — plugin **vtis-quiz v1.3.22**. Slug **`neuropsicanalise`**. Timer, prática 60/40, shuffle; **guardar no admin preserva gabarito MC**.

Repo plugin: https://github.com/esvianna/vtis-quiz · Skin: `inc/module-vtis-quiz-skin.php`.

**URL:** https://saulocoelho.com/quiz/neuropsicanalise/

## Validar

1. Embaralhar: duas sessões com letras diferentes; score correcto.
2. Editar settings (ex. shuffle) e Guardar → `max_score` continua 40 / opções A–D.
3. Nota prática no lead.

## Decisões

- `result_delivery`: deferred; timer 90; prática 60/40; `shuffle_options`.
- Desde 1.3.22: save admin preserva `options_json` por pergunta (D37).

## Próximos passos

1. Validar #12 → Done quando OK.
2. Issue opcional: UI de edição do gabarito MC no admin.
