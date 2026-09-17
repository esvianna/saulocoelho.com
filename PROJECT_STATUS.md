# PROJECT_STATUS.md — Continuidade do projeto

Última atualização: 2026-09-17

## Onde paramos

**Leadership Academy Noite 3 (issue [#24](https://github.com/esvianna/saulocoelho.com/issues/24)):** **vtis-quiz 1.3.40** + **AmaEducacional 1.0.48** — Líder que preciso me tornar + Meta Master + MAPA. **Done** (validado 2026-09-17).

**Mentor: PDF de apoio (issue [#23](https://github.com/esvianna/saulocoelho.com/issues/23)):** tema **1.3.42** + Ama **1.0.47** — upload + escolha de módulo + notificar. **Done** (validado 2026-09-17).

**Notificar ao publicar aula (issue [#22](https://github.com/esvianna/saulocoelho.com/issues/22)):** tema **1.3.40** + Ama **1.0.45** — metabox admin + botão **Notificar** no Painel da turma. **Done** (validado 2026-09-17).

**Leadership Academy Noite 2 (issue [#19](https://github.com/esvianna/saulocoelho.com/issues/19)):** **vtis-quiz 1.3.39** + **AmaEducacional 1.0.44** — MAPA. **Done** (validado 2026-09-17).

**Painel do mentor (issue [#20](https://github.com/esvianna/saulocoelho.com/issues/20)):** Ama **1.0.43** + tema **1.3.38** — contraste dark; Editar aluno com **nova senha** opcional. **Done** (validado 2026-09-17).

**Portal PWA — sessão persistente (issue [#21](https://github.com/esvianna/saulocoelho.com/issues/21)):** tema **1.3.32+**. **Done** (validado 2026-09-17).

**vtis-quiz stepped vs all_at_once (issue [#14](https://github.com/esvianna/saulocoelho.com/issues/14)):** **Done** (validado 2026-09-17).

**Convite + senha (ADR-016):** Ama **1.0.42+** + tema **1.3.37+** — senha no `/inscricao/` (altura alinhada + olho); contraste do banner. **no ar** (FTP).

**Portal avisos / sininho / Web Push (issue [#16](https://github.com/esvianna/saulocoelho.com/issues/16)):** Fase A+B+C no tema **1.3.31** — **Done** (validado 2026-09-17).

- Fase A: admin **Avisos Portal**, inbox + sininho
- Fase B: VAPID + subscriptions + Conta opt-in + SW push
- Fase C: tabela `sc_portal_push_jobs`, WP-Cron lotes, checkbox e-mail, silenciar curso, coluna Envio
- Tabelas: `sc_portal_notices`, `sc_portal_notice_reads`, `sc_portal_push_subs`, `sc_portal_push_jobs`
- Vendor: `wp-content/themes/saulocoelho/vendor/` (`minishlink/web-push`)

**Convite LMS — handoff (ADR-014 / [#17](https://github.com/esvianna/saulocoelho.com/pull/17)):** tema **1.3.25** — **no ar**. **Validado**.

**Portal do Aluno (issue [#15](https://github.com/esvianna/saulocoelho.com/issues/15)):** **Done**. Tema **1.3.27** no ar (ícone PWA = logo SC do site; fallbacks «P» removidos).

**Leadership Academy Noite 1 (issue [#13](https://github.com/esvianna/saulocoelho.com/issues/13)):** **Done**. **vtis-quiz 1.3.33** + Ama **1.0.31** — no ar; Conselho validado.

**Leadership Academy (fase 1):** convite Ama **1.0.31** + tema **1.3.25** (handoff). **No ar.**

- URL: https://saulocoelho.com/inscricao/leadership-academy/

**Palestra Teresópolis (segunda 14/09):** captura de leads **e CRUD de palestras no ar** em produção.

- URL: https://saulocoelho.com/palestra/ (Teresópolis; também `/palestra/teresopolis/`)
- Admin WP: **Palestras** (criar/editar) e **Palestras → Leads**
- PDF privado; download só após cadastro
- PWA `app.saulocoelho.com` **não** entra neste fluxo

Quiz **8 Códigos de Reação** no plugin **vtis-quiz 1.3.23**, publicado em produção.

- URL: https://saulocoelho.com/quiz/8-codigos-de-reacao/
- 32 afirmações, escala 0–5, mapa em média /5, entrega imediata (Saulo testa)
- `saulo.vtis.com.br` redireciona para produção; o deploy efetivo foi em `saulocoelho.com`

Repo plugin: https://github.com/esvianna/vtis-quiz · Skin: `inc/module-vtis-quiz-skin.php`.

## Entrega #12 (resumo)

- 40 MC + gabarito; 7 dimensões; faixas 0–23 / 24–29 / 30–35 / 36–40
- Lead gate + `result_delivery` **deferred**; timer **90 min**
- Nota prática no admin + certificação **60/40** (mín. 7,5)
- Embaralhar alternativas (`shuffle_options`)
- Save no admin **preserva** gabarito MC (1.3.22)
- Leads: títulos, órfãos, relatório HTML

## Próximos passos sugeridos

1. Avançar [#11](https://github.com/esvianna/saulocoelho.com/issues/11) Identidade Método OCD (Ready) — ou nova prioridade Leadership / Portal.
2. Commit/push do tema + plugins alinhados ao `main` quando quiseres.

## Como retomar

- Palestra: admin **Palestras** (eventos) e **Palestras → Leads**. `/palestra/` permanece Teresópolis. Checklist em `TESTING.md`.
- Quiz 8 Códigos: testar o fluxo completo (32 itens + lead + mapa /5) e ajustar copy/CTA no admin.
