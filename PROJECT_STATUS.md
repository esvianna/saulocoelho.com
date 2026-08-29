# PROJECT_STATUS.md — Continuidade do projeto

Última atualização: 2026-08-29

## Onde paramos

Issue **[#12](https://github.com/esvianna/saulocoelho.com/issues/12)** Avaliação Final Neuropsicanálise — plugin **vtis-quiz v1.3.15** (`neuropsicanalise-final`) **deployado** em staging/prod (FTP Confrarias). Seed activo em produção (`max_score` 40, CSS `ver=1.3.15`). Aguarda validação humana completa (submission + Leads) → depois **Done**.

Repo plugin: https://github.com/esvianna/vtis-quiz · Skin: `inc/module-vtis-quiz-skin.php`.

**URL:** https://saulocoelho.com/quiz/neuropsicanalise-final/

## Pós-deploy (validar no WP)

1. Completar 1 submission de teste; confirmar lead em **VTIS Quiz → Leads** (score + respostas). Resultado ao aluno: **deferred**.
2. Export CSV opcional.
3. **Não** gravar este quiz no ecrã de edição do admin (destrói `options_json` MC).
4. Flush permalinks só se alguma rota `/quiz/…` falhar (rewrite já respondeu 200 em prod).

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
