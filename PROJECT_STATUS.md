# PROJECT_STATUS.md — Continuidade do projeto

Última atualização: 2026-08-31

## Onde paramos

Issue **[#12](https://github.com/esvianna/saulocoelho.com/issues/12)** — plugin **vtis-quiz v1.3.21**. Slug **`neuropsicanalise`**. Timer 90 min, nota prática 60/40, **embaralhar alternativas**.

Repo plugin: https://github.com/esvianna/vtis-quiz · Skin: `inc/module-vtis-quiz-skin.php`.

**URL:** https://saulocoelho.com/quiz/neuropsicanalise/

## Pós-deploy (validar)

1. Abrir o quiz em duas sessões: ordem A/B/C/D deve mudar; pontuação no Leads continua correcta.
2. **Não** gravar este quiz no ecrã de edição do admin (destrói `options_json` MC).

## Decisões

- `result_delivery`: **deferred**; timer 90 min; prática no lead; final = teórica×0,60 + prática×0,40.
- `shuffle_options`: activo na Neuro (letras seguem a posição na tela).

## Próximos passos

1. Validar #12 → Done quando OK.
2. Issue opcional: UI de gabarito MC no admin.
