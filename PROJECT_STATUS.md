# PROJECT_STATUS.md — Continuidade do projeto

Última atualização: 2026-08-29

## Onde paramos

Issue **[#12](https://github.com/esvianna/saulocoelho.com/issues/12)** Avaliação Final Neuropsicanálise — seed no plugin **vtis-quiz v1.3.15** (`neuropsicanalise-final`, DB 8). Código pronto; aguarda **deploy** staging/prod + flush permalinks + validação humana → **In Review**.

Repo plugin: https://github.com/esvianna/vtis-quiz · Skin: `inc/module-vtis-quiz-skin.php`.

## Pós-deploy (fazer no WP)

1. Actualizar/activar plugin vtis-quiz 1.3.15 (upgrade DB→8 cria o quiz se o slug ainda não existir).
2. **Flush permalinks**.
3. Abrir `/quiz/neuropsicanalise-final/` ou página com `[vtis_quiz slug="neuropsicanalise-final"]`.
4. Completar 1 submission de teste; confirmar lead em **VTIS Quiz → Leads** (score + respostas). Resultado ao aluno: **deferred** (mensagem sem nota).
5. **Não** gravar este quiz no ecrã de edição do admin (destrói `options_json` MC).

## Decisões fechadas nesta entrega

- `result_delivery`: **deferred** (como MAPA; nota revelada pela formação).
- Slug: `neuropsicanalise-final`.
- Fora: prática no admin, timer 90 min, UI de gabarito no admin.

## Próximos passos

1. Deploy staging (`saulo.vtis.com.br`) → smoke test → prod.
2. Ligar shortcode na página Woo/formação quando existir.
3. Validar #12 → Done quando OK.
4. Issues futuras opcionais: nota prática no admin, timer, tipo MC no admin.

## Como retomar

- "Deploy do vtis-quiz 1.3.15 feito — validar #12" ou "Mudar neuro para immediate / outro slug"
